<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    const STATUS_WEIGHTS = [
        'delivered' => 40,
        'shipped' => 15,
        'processing' => 15,
        'pending' => 15,
        'cancelled' => 10,
        'refunded' => 5,
    ];

    const ORDER_COUNT = 60;

    public function run(): void
    {
        if (Order::exists()) {
            return;
        }

        $admin = Admin::first();
        $customers = Customer::with('addresses')->has('addresses')->get();
        $variants = ProductVariant::with('product.category')->get();
        $shippingZoneCosts = ShippingZone::pluck('cost', 'governorate');
        $coupons = Coupon::whereIn('code', ['AHLAN10', 'ATHR50', 'SUMMER15', 'VIP100'])->get();

        DB::transaction(function () use ($admin, $customers, $variants, $shippingZoneCosts, $coupons) {
            Model::withoutEvents(function () use ($admin, $customers, $variants, $shippingZoneCosts, $coupons) {
                for ($i = 0; $i < self::ORDER_COUNT; $i++) {
                    $this->createOrder($admin, $customers, $variants, $shippingZoneCosts, $coupons);
                }
            });
        });
    }

    protected function createOrder(?Admin $admin, Collection $customers, Collection $variants, $shippingZoneCosts, Collection $coupons): void
    {
        $customer = $customers->random();
        $address = $customer->addresses->firstWhere('is_default', true) ?? $customer->addresses->first();
        $shippingCost = $shippingZoneCosts[$address->governorate] ?? null;

        if ($shippingCost === null) {
            return;
        }

        $status = $this->randomStatus();
        $placedAt = Carbon::now()->subDays(random_int(1, 120))->subMinutes(random_int(0, 720));

        [$subtotal, $orderItemsData] = $this->buildItems($variants);

        $coupon = null;
        $discount = 0.0;

        if (random_int(1, 100) <= 20 && $coupons->isNotEmpty()) {
            $candidate = $coupons->random();

            if ($subtotal >= (float) ($candidate->min_order_amount ?? 0)) {
                $coupon = $candidate;
                $discount = $coupon->discountFor($subtotal);
            }
        }

        $paymentMethod = random_int(1, 100) <= 55 ? 'cod' : 'paymob';
        $total = $subtotal - $discount + $shippingCost;

        $paymentStatus = match (true) {
            $status === 'refunded' => 'refunded',
            $status === 'cancelled' => $paymentMethod === 'paymob' ? 'failed' : 'pending',
            $status === 'delivered' => 'paid',
            $paymentMethod === 'paymob' && in_array($status, ['processing', 'shipped'], true) => 'paid',
            default => 'pending',
        };

        $order = new Order([
            'order_number' => $this->orderNumber($placedAt),
            'customer_id' => $customer->id,
            'status' => $status,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'address_id' => $address->id,
            'coupon_id' => $coupon?->id,
            'notes' => random_int(1, 100) <= 15 ? 'برجاء الاتصال قبل التوصيل.' : null,
        ]);

        $this->saveAt($order, $placedAt);

        foreach ($orderItemsData as $itemData) {
            $this->saveAt($order->items()->make($itemData), $placedAt);
        }

        $lastAt = $this->recordHistory($order, $status, $placedAt, $admin);

        if ($paymentMethod === 'paymob' && in_array($paymentStatus, ['paid', 'failed', 'refunded'], true)) {
            $this->saveAt($order->payments()->make([
                'gateway' => 'paymob',
                'transaction_id' => (string) random_int(100000000, 999999999),
                'amount' => $total,
                'status' => $paymentStatus,
                'raw_response' => ['source' => 'seeder', 'status' => $paymentStatus],
            ]), $lastAt);
        }

        $coupon?->increment('used_count');
    }

    protected function buildItems(Collection $variants): array
    {
        $subtotal = 0.0;
        $items = [];

        foreach ($variants->random(random_int(1, 3)) as $variant) {
            $quantity = random_int(1, 3);
            $price = (float) $variant->price;
            $lineTotal = $price * $quantity;
            $subtotal += $lineTotal;

            $items[] = [
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'variant_label' => $variant->label(),
                'quantity' => $quantity,
                'price' => $price,
                'total' => $lineTotal,
            ];
        }

        return [$subtotal, $items];
    }

    protected function recordHistory(Order $order, string $finalStatus, Carbon $placedAt, ?Admin $admin): Carbon
    {
        $timeline = match ($finalStatus) {
            'processing' => ['pending', 'processing'],
            'shipped' => ['pending', 'processing', 'shipped'],
            'delivered' => ['pending', 'processing', 'shipped', 'delivered'],
            'cancelled' => ['pending', 'cancelled'],
            'refunded' => ['pending', 'processing', 'shipped', 'delivered', 'refunded'],
            default => ['pending'],
        };

        $at = $placedAt->copy();

        foreach ($timeline as $index => $status) {
            if ($index > 0) {
                $at = $at->copy()->addHours(random_int(4, 48));
            }

            $this->saveAt($order->statusHistories()->make([
                'status' => $status,
                'note' => $status === 'cancelled' ? 'ألغى العميل الطلب بنفسه.' : null,
                'changed_by_admin_id' => $index === 0 ? null : $admin?->id,
            ]), $at);
        }

        return $at;
    }

    protected function randomStatus(): string
    {
        $roll = random_int(1, 100);
        $cumulative = 0;

        foreach (self::STATUS_WEIGHTS as $status => $weight) {
            $cumulative += $weight;

            if ($roll <= $cumulative) {
                return $status;
            }
        }

        return 'pending';
    }

    protected function orderNumber(Carbon $placedAt): string
    {
        do {
            $number = 'ATHR-'.$placedAt->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    protected function saveAt(Model $model, Carbon $at): void
    {
        $model->timestamps = false;
        $model->created_at = $at;
        $model->updated_at = $at;
        $model->save();
    }
}
