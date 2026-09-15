<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        Model::withoutEvents(function () {
            foreach ($this->defaults() as $pageKey => $fields) {
                foreach ($fields as $fieldKey => $value) {
                    PageContent::firstOrCreate(
                        ['page_key' => $pageKey, 'field_key' => $fieldKey],
                        ['value' => $value]
                    );
                }
            }
        });
    }

    protected function defaults(): array
    {
        return [
            'home' => [
                'hero_eyebrow' => 'مجموعة الخريف 2026',
                'hero_title' => 'لكل حضور',
                'hero_title_highlight' => 'أثرٌ لا يُنسى.',
                'hero_paragraph' => 'نمزج أندر المكونات في عطور تحكي شخصيتك، وتبقى في الذاكرة حتى بعد رحيلك.',
                'hero_image' => 'pages/hero.jpg',
                'hero_cta_label' => 'اكتشف المجموعة',
                'feature_1_title' => 'توصيل مجانى',
                'feature_1_subtitle' => 'للطلبات فوق 500 ج.م',
                'feature_2_title' => 'أصالة مضمونة',
                'feature_2_subtitle' => '100% منتجات أصلية',
                'feature_3_title' => 'هدية مع كل طلب',
                'feature_3_subtitle' => 'لأن التفاصيل تصنع الفرق',
                'feature_4_title' => 'إرجاع سهل',
                'feature_4_subtitle' => 'خلال 14 يومًا',
                'newsletter_eyebrow' => 'ابق على الأثر',
                'newsletter_title' => 'رسائل تستحق',
                'newsletter_title_highlight' => 'أن تُفتح.',
                'newsletter_paragraph' => 'اشترك لتصلك أحدث التشكيلات، قصص المكونات، وهدايا خاصة لأعضاء نادى أثر.',
            ],
            'shop' => [
                'heading_eyebrow' => 'مختارات أثر',
                'heading_title' => 'عطور تحمل بصمتك.',
                'heading_subtitle' => 'تشكيلة منتقاة بعناية من العطور الشرقية والغربية. اكتشف الرائحة التى تشبهك.',
            ],
            'story' => [
                'eyebrow' => 'حكاية أثر',
                'title' => 'العطر ليس رائحة،',
                'title_highlight' => 'بل ذاكرة.',
                'paragraph_1' => 'فى أثر نؤمن أن العطر لغة لا تحتاج إلى ترجمة. لذلك نبحث فى كل زاوية من الشرق عن مكونات أصيلة، ونمنحها لمسة معاصرة لتصنع رائحة تشبهك وحدك.',
                'paragraph_2' => 'من العود المعتّق إلى بتلات الورد الطائفى، كل قطرة تحمل قصة، وكل قصة تبدأ معك.',
                'image' => 'pages/story.jpg',
                'image_caption_line1' => 'من قلب الشرق',
                'image_caption_line2' => 'إلى حواس العالم',
            ],
            'ritual' => [
                'eyebrow' => 'طقوس العطر',
                'title' => 'اصنع لحظتك',
                'title_highlight' => 'الخاصة.',
                'paragraph' => 'العطر الجيد لا يُرشّ فقط، بل يُعاش. اكتشف طقوسنا الصغيرة لتجعل من كل يوم مناسبة.',
                'cta_label' => 'اكتشف الطقوس',
                'stat_number' => '03',
                'stat_label' => 'خطوات لبصمة تدوم',
            ],
            'contact' => [
                'eyebrow' => 'نحن هنا لأجلك',
                'title' => 'تواصل معنا،',
                'title_highlight' => 'يسعدنا سماعك.',
                'intro_paragraph' => 'لأى استفسار عن طلبك أو عطرك المفضل، فريقنا جاهز للرد عليك فى أقرب وقت.',
                'address' => 'القاهرة، مصر',
                'phone' => '01000000000',
                'email' => 'hello@athr.test',
                'instagram' => 'https://instagram.com/athr.store',
                'tiktok' => 'https://tiktok.com/@athr.store',
            ],
        ];
    }
}
