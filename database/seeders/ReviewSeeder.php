<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReviewSeeder extends Seeder
{
    const COMMENTS = [
        5 => [
            'عطر رائع فعلاً، الثبات ممتاز ويدوم طول اليوم.',
            'رائحة فخمة جدًا وتوصيل سريع، هطلب تاني أكيد.',
            'من أحلى العطور اللي جربتها، الريحة مميزة وغير مكررة.',
            'التغليف كان أنيق جدًا والعطر أصلي 100%.',
            'عاشق للعطر ده، الناس بتسألني عنه كتير.',
            'جودة عالية جدًا بسعر مناسب، تجربة ممتازة.',
            'أفضل هدية ممكن تجيبها لحد بتحبه.',
            'ثبات رهيب وريحة تفتح المزاج من أول رشة.',
        ],
        4 => [
            'عطر حلو بس الثبات كان ممكن يكون أطول شوية.',
            'ريحته مميزة بس محتاج كمية أكبر عشان يفوح.',
            'جيد جدًا وسعره مناسب لجودته.',
            'عجبني بس مكنتش متوقع يكون قوي كده من أول استخدام.',
            'تجربة كويسة هجربها تاني بحجم أكبر.',
        ],
        3 => [
            'عادي، متوقع منه أكتر من كده.',
            'الريحة حلوة بس بتخف بسرعة نسبيًا.',
            'مقبول بالنسبة للسعر بس مش هيبقى المفضل عندي.',
            'لسه بجرب فيه، لحد دلوقتي حاسس إنه عادي.',
        ],
        2 => [
            'الريحة مش زي ما توقعت من الوصف.',
            'الثبات ضعيف جدًا بالنسبة لسعره.',
        ],
        1 => [
            'للأسف مكنش زي المتوقع خالص ومحسيتش إنه يستاهل السعر.',
        ],
    ];

    const RATING_WEIGHTS = [5 => 55, 4 => 25, 3 => 12, 2 => 5, 1 => 3];

    public function run(): void
    {
        $orderItems = OrderItem::whereHas('order', fn ($q) => $q->where('status', 'delivered'))
            ->with(['order', 'productVariant.product'])
            ->get()
            ->filter(fn (OrderItem $item) => $item->productVariant !== null)
            ->shuffle();

        $selected = $orderItems->take((int) ceil($orderItems->count() * 0.7));

        Model::withoutEvents(function () use ($selected) {
            foreach ($selected as $item) {
                $rating = $this->randomRating();
                $comment = $this->randomComment($rating);
                $deliveredAt = $item->order->statusHistories()->where('status', 'delivered')->value('created_at')
                    ?? $item->order->created_at;

                $review = Review::firstOrCreate(
                    ['order_item_id' => $item->id],
                    [
                        'product_id' => $item->productVariant->product_id,
                        'customer_id' => $item->order->customer_id,
                        'rating' => $rating,
                        'comment' => $comment,
                        'is_approved' => random_int(1, 100) <= 85,
                    ]
                );

                if ($review->wasRecentlyCreated) {
                    $at = Carbon::parse($deliveredAt)->addDays(random_int(1, 10));
                    $review->timestamps = false;
                    $review->created_at = $at;
                    $review->updated_at = $at;
                    $review->saveQuietly();
                }
            }
        });
    }

    protected function randomRating(): int
    {
        $roll = random_int(1, 100);
        $cumulative = 0;

        foreach (self::RATING_WEIGHTS as $rating => $weight) {
            $cumulative += $weight;

            if ($roll <= $cumulative) {
                return $rating;
            }
        }

        return 5;
    }

    protected function randomComment(int $rating): string
    {
        $pool = self::COMMENTS[$rating];

        return $pool[array_rand($pool)];
    }
}
