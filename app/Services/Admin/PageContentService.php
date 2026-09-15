<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\PageContentRepositoryInterface;
use App\Services\Support\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class PageContentService
{
    const PAGES = [
        'home' => [
            'label' => 'الرئيسية',
            'fields' => [
                'hero_eyebrow' => ['label' => 'نص علوى صغير فوق العنوان', 'type' => 'text'],
                'hero_title' => ['label' => 'العنوان الرئيسى', 'type' => 'text'],
                'hero_title_highlight' => ['label' => 'الجزء المميز من العنوان', 'type' => 'text'],
                'hero_paragraph' => ['label' => 'الفقرة التعريفية', 'type' => 'textarea'],
                'hero_image' => ['label' => 'صورة الغلاف الرئيسية', 'type' => 'image'],
                'hero_cta_label' => ['label' => 'نص زر الدعوة للتسوق', 'type' => 'text'],
                'feature_1_title' => ['label' => 'عنوان الميزة الأولى', 'type' => 'text'],
                'feature_1_subtitle' => ['label' => 'وصف الميزة الأولى', 'type' => 'text'],
                'feature_2_title' => ['label' => 'عنوان الميزة الثانية', 'type' => 'text'],
                'feature_2_subtitle' => ['label' => 'وصف الميزة الثانية', 'type' => 'text'],
                'feature_3_title' => ['label' => 'عنوان الميزة الثالثة', 'type' => 'text'],
                'feature_3_subtitle' => ['label' => 'وصف الميزة الثالثة', 'type' => 'text'],
                'feature_4_title' => ['label' => 'عنوان الميزة الرابعة', 'type' => 'text'],
                'feature_4_subtitle' => ['label' => 'وصف الميزة الرابعة', 'type' => 'text'],
                'newsletter_eyebrow' => ['label' => 'نص علوى صغير (قسم النشرة البريدية)', 'type' => 'text'],
                'newsletter_title' => ['label' => 'عنوان قسم النشرة البريدية', 'type' => 'text'],
                'newsletter_title_highlight' => ['label' => 'الجزء المميز من عنوان النشرة', 'type' => 'text'],
                'newsletter_paragraph' => ['label' => 'فقرة قسم النشرة البريدية', 'type' => 'textarea'],
            ],
        ],
        'shop' => [
            'label' => 'المتجر',
            'fields' => [
                'heading_eyebrow' => ['label' => 'نص علوى صغير', 'type' => 'text'],
                'heading_title' => ['label' => 'عنوان الصفحة', 'type' => 'text'],
                'heading_subtitle' => ['label' => 'وصف الصفحة', 'type' => 'textarea'],
            ],
        ],
        'story' => [
            'label' => 'قصتنا',
            'fields' => [
                'eyebrow' => ['label' => 'نص علوى صغير', 'type' => 'text'],
                'title' => ['label' => 'العنوان', 'type' => 'text'],
                'title_highlight' => ['label' => 'الجزء المميز من العنوان', 'type' => 'text'],
                'paragraph_1' => ['label' => 'الفقرة الأولى', 'type' => 'textarea'],
                'paragraph_2' => ['label' => 'الفقرة الثانية', 'type' => 'textarea'],
                'image' => ['label' => 'الصورة', 'type' => 'image'],
                'image_caption_line1' => ['label' => 'السطر الأول أسفل الصورة', 'type' => 'text'],
                'image_caption_line2' => ['label' => 'السطر الثانى أسفل الصورة', 'type' => 'text'],
            ],
        ],
        'ritual' => [
            'label' => 'طقوس العطر',
            'fields' => [
                'eyebrow' => ['label' => 'نص علوى صغير', 'type' => 'text'],
                'title' => ['label' => 'العنوان', 'type' => 'text'],
                'title_highlight' => ['label' => 'الجزء المميز من العنوان', 'type' => 'text'],
                'paragraph' => ['label' => 'الفقرة', 'type' => 'textarea'],
                'cta_label' => ['label' => 'نص الزر', 'type' => 'text'],
                'stat_number' => ['label' => 'الرقم البارز', 'type' => 'text'],
                'stat_label' => ['label' => 'وصف الرقم', 'type' => 'text'],
            ],
        ],
        'contact' => [
            'label' => 'تواصل معنا',
            'fields' => [
                'eyebrow' => ['label' => 'نص علوى صغير', 'type' => 'text'],
                'title' => ['label' => 'العنوان', 'type' => 'text'],
                'title_highlight' => ['label' => 'الجزء المميز من العنوان', 'type' => 'text'],
                'intro_paragraph' => ['label' => 'فقرة تعريفية', 'type' => 'textarea'],
                'address' => ['label' => 'العنوان (المكان)', 'type' => 'text'],
                'phone' => ['label' => 'رقم الهاتف', 'type' => 'text'],
                'email' => ['label' => 'البريد الإلكتروني', 'type' => 'text'],
                'instagram' => ['label' => 'رابط انستغرام', 'type' => 'text'],
                'tiktok' => ['label' => 'رابط تيك توك', 'type' => 'text'],
            ],
        ],
    ];

    public function __construct(
        protected PageContentRepositoryInterface $pages,
        protected ImageUploadService $images,
    ) {}

    public function getPage(string $pageKey): Collection
    {
        $fields = self::PAGES[$pageKey]['fields'] ?? [];

        return collect($fields)->mapWithKeys(fn (array $config, string $key) => [$key => null])
            ->merge($this->pages->getPage($pageKey));
    }

    public function updatePage(string $pageKey, array $values, array $files = []): void
    {
        $fields = self::PAGES[$pageKey]['fields'] ?? [];
        $current = $this->pages->getPage($pageKey);

        foreach ($fields as $fieldKey => $config) {
            if ($config['type'] !== 'image') {
                continue;
            }

            /** @var UploadedFile|null $file */
            $file = $files[$fieldKey] ?? null;

            if ($file instanceof UploadedFile) {
                $values[$fieldKey] = $this->images->replace($file, 'pages', $current->get($fieldKey));
            } else {
                unset($values[$fieldKey]);
            }
        }

        $this->pages->setMany($pageKey, $values);
    }
}
