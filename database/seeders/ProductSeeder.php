<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brandIds = Brand::pluck('id', 'name');
        $categoryIds = Category::pluck('id', 'slug');
        $sizeValueIds = AttributeValue::whereHas('attribute', fn ($q) => $q->where('slug', 'size'))->pluck('id', 'value');
        $concentrationValueIds = AttributeValue::whereHas('attribute', fn ($q) => $q->where('slug', 'concentration'))->pluck('id', 'value');
        $genderValueIds = AttributeValue::whereHas('attribute', fn ($q) => $q->where('slug', 'gender'))->pluck('id', 'value');

        Model::withoutEvents(function () use ($brandIds, $categoryIds, $sizeValueIds, $concentrationValueIds, $genderValueIds) {
            foreach ($this->catalog() as $index => $entry) {
                $product = Product::firstOrCreate(
                    ['slug' => Str::slug($entry['name']).'-'.($index + 1)],
                    [
                        'category_id' => $categoryIds[$entry['category']],
                        'brand_id' => $brandIds[$entry['brand']],
                        'name' => $entry['name'],
                        'description' => $this->describe($entry),
                        'fragrance_notes' => "المقدمة: {$entry['top']}\nالقلب: {$entry['heart']}\nالقاعدة: {$entry['base']}",
                        'is_active' => $entry['active'] ?? true,
                    ]
                );

                if ($product->variants()->exists()) {
                    continue;
                }

                foreach ($entry['variants'] as $size => [$price, $stock]) {
                    $variant = $product->variants()->create([
                        'sku' => strtoupper(Str::slug($entry['name'])).'-'.($index + 1).'-'.$size,
                        'price' => $price,
                        'compare_price' => ($entry['sale'] ?? false) ? round($price * 1.18, -1) : null,
                        'stock_quantity' => $stock,
                        'is_active' => true,
                    ]);

                    $variant->attributeValues()->sync(array_filter([
                        $sizeValueIds[$size] ?? null,
                        $concentrationValueIds[$entry['concentration']] ?? null,
                        $genderValueIds[$entry['gender']] ?? null,
                    ]));
                }
            }
        });
    }

    protected function describe(array $entry): string
    {
        $intros = [
            'عطر يأسر الحواس من أول رشة',
            'تركيبة متقنة تعكس شخصية مميزة',
            'إبداع عطري يجمع بين الأصالة والفخامة',
            'رائحة تترك أثرًا لا يُنسى فى كل مكان',
        ];

        $intro = $intros[crc32($entry['name']) % count($intros)];

        return "{$intro}، يفتح بمزيج من {$entry['top']}، ليتكشف عن قلب دافئ من {$entry['heart']}، وترتكز على قاعدة {$entry['base']} تمنحك ثباتًا يدوم طوال اليوم.";
    }

    protected function catalog(): array
    {
        return [
            ['name' => 'سلطان الليل', 'brand' => 'دار الأصايل', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Eau de Parfum', 'top' => 'برغموت، هيل', 'heart' => 'عود، جلد', 'base' => 'عنبر، مسك أسود', 'sale' => true, 'variants' => ['30ml' => [950, 22], '50ml' => [1550, 14], '100ml' => [2650, 6]]],
            ['name' => 'فارس الصحراء', 'brand' => 'نجد للعطور', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Extrait de Parfum', 'top' => 'زعفران، فلفل وردي', 'heart' => 'عود عماني، قرنفل', 'base' => 'صندل، عنبر رمادي', 'variants' => ['30ml' => [1050, 20], '50ml' => [1700, 12], '100ml' => [2900, 5]]],
            ['name' => 'عنبر الملوك', 'brand' => 'سلطان العنبر', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Eau de Parfum', 'top' => 'برتقال مر، قرفة', 'heart' => 'عود كمبودي، ورد طائفي', 'base' => 'عنبر، مسك أبيض، صندل', 'variants' => ['30ml' => [900, 25], '50ml' => [1450, 18], '100ml' => [2450, 9]]],
            ['name' => 'توهج الشرق', 'brand' => 'عبير الشرق', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Eau de Parfum', 'top' => 'يوزو، هيل أخضر', 'heart' => 'زعفران، جلد مدخن', 'base' => 'عود، عنبر دافئ', 'variants' => ['30ml' => [980, 16], '50ml' => [1600, 10], '100ml' => [2700, 3]]],
            ['name' => 'أمير العود', 'brand' => 'الأصالة الفاخرة', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Extrait de Parfum', 'top' => 'فلفل أسود، خزامى', 'heart' => 'عود هندي، ورد بلغاري', 'base' => 'مسك، عنبر', 'variants' => ['30ml' => [1150, 12], '50ml' => [1850, 8], '100ml' => [3100, 0]]],
            ['name' => 'ظل القمر', 'brand' => 'مسك الخليج', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Eau de Parfum', 'top' => 'نعناع، ليمون', 'heart' => 'قرنفل، عود مدخن', 'base' => 'جاوي، صندل', 'variants' => ['30ml' => [890, 19], '50ml' => [1400, 13], '100ml' => [2350, 7]]],
            ['name' => 'راية الفخامة', 'brand' => 'دار الأصايل', 'category' => 'men-oriental', 'gender' => 'رجالي', 'concentration' => 'Eau de Parfum', 'top' => 'برغموت، زنجبيل', 'heart' => 'عود، جلد فاخر', 'base' => 'عنبر رمادي، مسك', 'sale' => true, 'variants' => ['30ml' => [1020, 14], '50ml' => [1650, 9], '100ml' => [2800, 4]]],

            ['name' => 'أزرق المحيط', 'brand' => 'نوتس آند وودز', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Toilette', 'top' => 'نعناع بحري، ليمون', 'heart' => 'خزامى، ياسمين مائي', 'base' => 'مسك أبيض، خشب الأرز', 'variants' => ['30ml' => [650, 30], '50ml' => [1000, 20], '100ml' => [1650, 12]]],
            ['name' => 'نسيم الصباح', 'brand' => 'أروماس الحرير', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Cologne', 'top' => 'برغموت، جريب فروت', 'heart' => 'نعناع، شاي أخضر', 'base' => 'مسك، فيتيفر', 'variants' => ['30ml' => [600, 28], '50ml' => [950, 17], '100ml' => [1550, 9]]],
            ['name' => 'خطوة أولى', 'brand' => 'لافندر هاوس', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Toilette', 'top' => 'تفاح أخضر، ليمون', 'heart' => 'خزامى، جوز الطيب', 'base' => 'خشب الصندل، مسك', 'variants' => ['30ml' => [680, 24], '50ml' => [1050, 15], '100ml' => [1750, 8]]],
            ['name' => 'طريق الحرير الأبيض', 'brand' => 'بيت الزهور', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Toilette', 'top' => 'برغموت، كزبرة', 'heart' => 'فلفل وردي، جلد', 'base' => 'فيتيفر، عنبر خفيف', 'variants' => ['30ml' => [720, 18], '50ml' => [1100, 11], '100ml' => [1850, 3]]],
            ['name' => 'رجل المدينة', 'brand' => 'روائع الحرير', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Toilette', 'top' => 'ليمون، أناناس', 'heart' => 'خزامى، قرنفل', 'base' => 'خشب الأرز، مسك', 'variants' => ['30ml' => [640, 22], '50ml' => [980, 14], '100ml' => [1600, 6]]],
            ['name' => 'الرحلة الطويلة', 'brand' => 'نوتس آند وودز', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Cologne', 'top' => 'برتقال، نعناع', 'heart' => 'إكليل الجبل، جوز الطيب', 'base' => 'فيتيفر، مسك أبيض', 'sale' => true, 'variants' => ['30ml' => [700, 10], '50ml' => [1080, 6], '100ml' => [1800, 2]]],
            ['name' => 'صولجان الفضة', 'brand' => 'أروماس الحرير', 'category' => 'men-western', 'gender' => 'رجالي', 'concentration' => 'Eau de Toilette', 'top' => 'جريب فروت، خزامى', 'heart' => 'قرنفل، جلد ناعم', 'base' => 'مسك، صندل', 'variants' => ['30ml' => [660, 0], '50ml' => [1020, 9], '100ml' => [1700, 5]]],

            ['name' => 'ياسمين الليل', 'brand' => 'إليكسير الورد', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'ياسمين، برغموت', 'heart' => 'ياسمين، زهر البرتقال', 'base' => 'فانيليا، مسك', 'variants' => ['30ml' => [880, 26], '50ml' => [1400, 16], '100ml' => [2350, 8]]],
            ['name' => 'لهفة الورد', 'brand' => 'عبير الشرق', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'رمان، توت أحمر', 'heart' => 'ورد طائفي، زنبق', 'base' => 'عنبر، مسك أبيض', 'variants' => ['30ml' => [930, 20], '50ml' => [1500, 13], '100ml' => [2550, 6]]],
            ['name' => 'سراب العنبر', 'brand' => 'نجد للعطور', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Extrait de Parfum', 'top' => 'برتقال، خوخ', 'heart' => 'ياسمين، زعفران', 'base' => 'عنبر، عود خفيف', 'sale' => true, 'variants' => ['30ml' => [1000, 15], '50ml' => [1600, 9], '100ml' => [2700, 4]]],
            ['name' => 'حرملك', 'brand' => 'سلطان العنبر', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Extrait de Parfum', 'top' => 'هيل، برغموت', 'heart' => 'ورد، عود', 'base' => 'مسك، صندل', 'active' => false, 'variants' => ['30ml' => [1100, 11], '50ml' => [1750, 7], '100ml' => [2950, 0]]],
            ['name' => 'لآلئ الشرق', 'brand' => 'الأصالة الفاخرة', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'فانيليا خفيفة، توت', 'heart' => 'ياسمين، ورد', 'base' => 'عنبر، مسك', 'variants' => ['30ml' => [860, 23], '50ml' => [1380, 15], '100ml' => [2300, 9]]],
            ['name' => 'عبق الحرير', 'brand' => 'مسك الخليج', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'زهر الليمون، خوخ', 'heart' => 'زنبق، ياسمين', 'base' => 'مسك حريري، صندل', 'variants' => ['30ml' => [900, 18], '50ml' => [1450, 12], '100ml' => [2450, 5]]],
            ['name' => 'أميرة العود', 'brand' => 'دار الأصايل', 'category' => 'women-oriental', 'gender' => 'نسائي', 'concentration' => 'Extrait de Parfum', 'top' => 'زعفران، رمان', 'heart' => 'عود، ورد طائفي', 'base' => 'عنبر، فانيليا', 'variants' => ['30ml' => [1050, 9], '50ml' => [1680, 5], '100ml' => [2850, 2]]],

            ['name' => 'زهرة الربيع', 'brand' => 'بيت الزهور', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Toilette', 'top' => 'توت فراولة، حمضيات', 'heart' => 'زهر الياسمين، فاوانيا', 'base' => 'مسك أبيض، خشب خفيف', 'variants' => ['30ml' => [620, 27], '50ml' => [960, 18], '100ml' => [1580, 10]]],
            ['name' => 'قطرات الندى', 'brand' => 'روائع الحرير', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Toilette', 'top' => 'خيار، حمضيات خضراء', 'heart' => 'زنبق الوادي، ياسمين مائي', 'base' => 'مسك نظيف، عنبر خفيف', 'variants' => ['30ml' => [590, 25], '50ml' => [920, 16], '100ml' => [1520, 9]]],
            ['name' => 'وشاح الحرير الوردي', 'brand' => 'إليكسير الورد', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'توت العليق، ليتشي', 'heart' => 'ورد بلغاري، فاوانيا', 'base' => 'مسك، فانيليا', 'sale' => true, 'variants' => ['30ml' => [700, 19], '50ml' => [1080, 12], '100ml' => [1800, 6]]],
            ['name' => 'نفحة الأناقة', 'brand' => 'لافندر هاوس', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Toilette', 'top' => 'برغموت، حمضيات', 'heart' => 'زهر الأقحوان، ياسمين', 'base' => 'صندل، مسك', 'variants' => ['30ml' => [650, 21], '50ml' => [1000, 13], '100ml' => [1650, 7]]],
            ['name' => 'حديقة الأزهار', 'brand' => 'أروماس الحرير', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Toilette', 'top' => 'كمثرى، خوخ', 'heart' => 'زهر البرتقال، فل', 'base' => 'مسك، خشب الأرز', 'variants' => ['30ml' => [610, 24], '50ml' => [950, 15], '100ml' => [1560, 8]]],
            ['name' => 'سيمفونية الزهور', 'brand' => 'بيت الزهور', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'توت، حمضيات', 'heart' => 'زنبق، ياسمين، ورد', 'base' => 'مسك أبيض، عنبر خفيف', 'variants' => ['30ml' => [670, 8], '50ml' => [1040, 4], '100ml' => [1720, 1]]],
            ['name' => 'رذاذ الفانيليا', 'brand' => 'روائع الحرير', 'category' => 'women-western', 'gender' => 'نسائي', 'concentration' => 'Eau de Parfum', 'top' => 'كراميل خفيف، حمضيات', 'heart' => 'فانيليا، زهر البرتقال', 'base' => 'مسك، خشب صندل', 'variants' => ['30ml' => [630, 16], '50ml' => [980, 10], '100ml' => [1600, 5]]],

            ['name' => 'نسيم الصحراء', 'brand' => 'نوتس آند وودز', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'زعفران، حمضيات', 'heart' => 'خشب الصندل، ورد', 'base' => 'عنبر، مسك', 'variants' => ['30ml' => [750, 22], '50ml' => [1200, 14], '100ml' => [2000, 7]]],
            ['name' => 'توازن الأرواح', 'brand' => 'نجد للعطور', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'هيل، برغموت', 'heart' => 'فيتيفر، خزامى', 'base' => 'مسك، صندل', 'variants' => ['30ml' => [800, 18], '50ml' => [1280, 11], '100ml' => [2150, 5]]],
            ['name' => 'خطى الحرير', 'brand' => 'مسك الخليج', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'توت أبيض، حمضيات', 'heart' => 'ياسمين، فيتيفر', 'base' => 'مسك حريري، عنبر خفيف', 'sale' => true, 'variants' => ['30ml' => [780, 20], '50ml' => [1250, 13], '100ml' => [2100, 6]]],
            ['name' => 'أثر النخيل', 'brand' => 'الأصالة الفاخرة', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'تمر، هيل', 'heart' => 'عود خفيف، ورد', 'base' => 'صندل، عنبر', 'variants' => ['30ml' => [820, 14], '50ml' => [1320, 8], '100ml' => [2200, 3]]],
            ['name' => 'رمال ذهبية', 'brand' => 'سلطان العنبر', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Extrait de Parfum', 'top' => 'زعفران، برتقال مر', 'heart' => 'عنبر، جلد', 'base' => 'صندل، مسك', 'variants' => ['30ml' => [900, 12], '50ml' => [1450, 7], '100ml' => [2450, 0]]],
            ['name' => 'سكون', 'brand' => 'لافندر هاوس', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Eau de Toilette', 'top' => 'نعناع، حمضيات خضراء', 'heart' => 'فيتيفر، خزامى', 'base' => 'مسك أبيض، خشب الأرز', 'variants' => ['30ml' => [700, 25], '50ml' => [1120, 16], '100ml' => [1880, 9]]],
            ['name' => 'جسر الشرق والغرب', 'brand' => 'عبير الشرق', 'category' => 'unisex-perfumes', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'يوزو، هيل', 'heart' => 'ورد، عود خفيف', 'base' => 'مسك، عنبر', 'variants' => ['30ml' => [850, 10], '50ml' => [1360, 6], '100ml' => [2280, 2]]],

            ['name' => 'عود ملكي', 'brand' => 'دار الأصايل', 'category' => 'oud-musk', 'gender' => 'للجنسين', 'concentration' => 'Extrait de Parfum', 'top' => 'هيل، زعفران', 'heart' => 'عود كمبودي، ورد طائفي', 'base' => 'عنبر، صندل، مسك', 'variants' => ['15ml' => [900, 20], '30ml' => [1650, 14], '50ml' => [2650, 8], '100ml' => [4600, 3]]],
            ['name' => 'عود كمبودي أصيل', 'brand' => 'نجد للعطور', 'category' => 'oud-musk', 'gender' => 'رجالي', 'concentration' => 'Extrait de Parfum', 'top' => 'توابل شرقية', 'heart' => 'عود كمبودي خام', 'base' => 'مسك أسود، عنبر', 'sale' => true, 'variants' => ['15ml' => [1050, 15], '30ml' => [1950, 9], '50ml' => [3150, 4]]],
            ['name' => 'مسك الغزال', 'brand' => 'سلطان العنبر', 'category' => 'oud-musk', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'هيل أخضر', 'heart' => 'مسك أبيض، ورد', 'base' => 'صندل، عنبر خفيف', 'variants' => ['15ml' => [700, 22], '30ml' => [1300, 15], '50ml' => [2100, 9]]],
            ['name' => 'عنبر وعود', 'brand' => 'الأصالة الفاخرة', 'category' => 'oud-musk', 'gender' => 'للجنسين', 'concentration' => 'Extrait de Parfum', 'top' => 'زعفران، برتقال مر', 'heart' => 'عود، عنبر', 'base' => 'مسك، جاوي', 'variants' => ['15ml' => [850, 18], '30ml' => [1550, 12], '50ml' => [2500, 6]]],
            ['name' => 'دهن العود الهندي', 'brand' => 'مسك الخليج', 'category' => 'oud-musk', 'gender' => 'رجالي', 'concentration' => 'Extrait de Parfum', 'top' => 'توابل دافئة', 'heart' => 'عود هندي خام', 'base' => 'مسك أسود، صندل', 'active' => false, 'variants' => ['15ml' => [1200, 10], '30ml' => [2200, 6], '50ml' => [3600, 0]]],
            ['name' => 'عود وورد طائفي', 'brand' => 'دار الأصايل', 'category' => 'oud-musk', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'هيل، قرفة', 'heart' => 'عود، ورد طائفي', 'base' => 'عنبر، مسك', 'variants' => ['15ml' => [800, 16], '30ml' => [1480, 11], '50ml' => [2400, 5]]],
            ['name' => 'مسك أبيض فاخر', 'brand' => 'عبير الشرق', 'category' => 'oud-musk', 'gender' => 'للجنسين', 'concentration' => 'Eau de Parfum', 'top' => 'حمضيات خفيفة', 'heart' => 'مسك أبيض، ياسمين', 'base' => 'صندل، عنبر خفيف', 'variants' => ['15ml' => [650, 24], '30ml' => [1200, 17], '50ml' => [1950, 10]]],
        ];
    }
}
