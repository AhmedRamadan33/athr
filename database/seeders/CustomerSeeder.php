<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    const CITIES = [
        'القاهرة' => ['مدينة نصر', 'المعادي', 'الزمالك', 'مصر الجديدة'],
        'الجيزة' => ['الدقي', 'الشيخ زايد', '6 أكتوبر', 'المهندسين'],
        'الإسكندرية' => ['سموحة', 'سيدي جابر', 'ميامي', 'العجمي'],
        'الدقهلية' => ['المنصورة', 'طلخا'],
        'الغربية' => ['طنطا', 'المحلة الكبرى'],
        'الشرقية' => ['الزقازيق', 'العاشر من رمضان'],
        'بورسعيد' => ['حي الشرق', 'حي المناخ'],
        'المنوفية' => ['شبين الكوم', 'منوف'],
        'أسوان' => ['أسوان البلد', 'كوم أمبو'],
        'الأقصر' => ['الأقصر البلد', 'الكرنك'],
        'المنيا' => ['المنيا البلد', 'ملوي'],
        'القليوبية' => ['بنها', 'شبرا الخيمة'],
    ];

    public function run(): void
    {
        Model::withoutEvents(function () {
            foreach ($this->customers() as $data) {
                $customer = Customer::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'password' => Hash::make('Password@123'),
                        'email_verified_at' => now(),
                        'is_active' => $data['active'] ?? true,
                    ]
                );

                if ($customer->addresses()->exists()) {
                    continue;
                }

                $cities = self::CITIES[$data['governorate']] ?? [$data['governorate']];

                $customer->addresses()->create([
                    'label' => 'المنزل',
                    'governorate' => $data['governorate'],
                    'city' => $cities[array_rand($cities)],
                    'address_line' => $data['street'],
                    'phone' => $data['phone'],
                    'is_default' => true,
                ]);

                if ($data['second_address'] ?? false) {
                    $customer->addresses()->create([
                        'label' => 'العمل',
                        'governorate' => $data['governorate'],
                        'city' => $cities[array_rand($cities)],
                        'address_line' => 'مبنى إداري، بجوار المحطة الرئيسية',
                        'phone' => $data['phone'],
                        'is_default' => false,
                    ]);
                }
            }
        });
    }

    protected function customers(): array
    {
        return [
            ['name' => 'عميل تجريبى', 'email' => 'customer@athr.test', 'phone' => '01000000000', 'governorate' => 'القاهرة', 'street' => '15 شارع التحرير', 'second_address' => true],
            ['name' => 'أحمد محمود السيد', 'email' => 'ahmed.mahmoud@gmail.com', 'phone' => '01012345678', 'governorate' => 'القاهرة', 'street' => '4 شارع عباس العقاد'],
            ['name' => 'مروة حسن فتحي', 'email' => 'marwa.hassan@yahoo.com', 'phone' => '01123456789', 'governorate' => 'الجيزة', 'street' => '22 شارع سوريا'],
            ['name' => 'محمد عبد الرحمن جاد', 'email' => 'm.abdelrahman@gmail.com', 'phone' => '01234567890', 'governorate' => 'الإسكندرية', 'street' => '9 شارع فؤاد'],
            ['name' => 'سارة إبراهيم عادل', 'email' => 'sara.ibrahim@hotmail.com', 'phone' => '01555667788', 'governorate' => 'الدقهلية', 'street' => '3 شارع الجمهورية'],
            ['name' => 'عمر خالد نبيل', 'email' => 'omar.khaled@gmail.com', 'phone' => '01098765432', 'governorate' => 'القاهرة', 'street' => '17 شارع مكرم عبيد', 'second_address' => true],
            ['name' => 'نور الدين حسين', 'email' => 'noureldin.hussein@gmail.com', 'phone' => '01211122233', 'governorate' => 'الجيزة', 'street' => '8 شارع البطل أحمد عبد العزيز'],
            ['name' => 'ياسمين طارق فؤاد', 'email' => 'yasmin.tarek@yahoo.com', 'phone' => '01044455566', 'governorate' => 'الغربية', 'street' => '11 شارع الجلاء'],
            ['name' => 'كريم عصام الدين', 'email' => 'karim.essam@gmail.com', 'phone' => '01166677788', 'governorate' => 'الشرقية', 'street' => '6 شارع النهضة'],
            ['name' => 'هبة الله محمد سالم', 'email' => 'hebatallah.m@gmail.com', 'phone' => '01522233344', 'governorate' => 'القاهرة', 'street' => '25 شارع الهرم الفرعي'],
            ['name' => 'أحمد سعيد الجندي', 'email' => 'ahmed.saeed@outlook.com', 'phone' => '01099988877', 'governorate' => 'بورسعيد', 'street' => '2 شارع 23 يوليو'],
            ['name' => 'دينا وليد سامي', 'email' => 'dina.walid@gmail.com', 'phone' => '01288899900', 'governorate' => 'الإسكندرية', 'street' => '14 شارع أبو قير', 'second_address' => true],
            ['name' => 'محمود رضا شعبان', 'email' => 'mahmoud.reda@yahoo.com', 'phone' => '01033344455', 'governorate' => 'المنوفية', 'street' => '5 شارع السوق'],
            ['name' => 'آية جمال الدين', 'email' => 'aya.gamal@gmail.com', 'phone' => '01511144477', 'governorate' => 'القاهرة', 'street' => '30 شارع رمسيس'],
            ['name' => 'حسام الدين عبد الله', 'email' => 'hossam.abdullah@gmail.com', 'phone' => '01277788899', 'governorate' => 'الجيزة', 'street' => '19 شارع الملك فيصل'],
            ['name' => 'رنا أشرف توفيق', 'email' => 'rana.ashraf@hotmail.com', 'phone' => '01066622233', 'governorate' => 'أسوان', 'street' => '7 شارع الكورنيش'],
            ['name' => 'طارق نجيب عبد الحميد', 'email' => 'tarek.naguib@gmail.com', 'phone' => '01144477722', 'governorate' => 'الأقصر', 'street' => '12 شارع المتحف'],
            ['name' => 'منة الله سيد على', 'email' => 'mennatallah.sayed@gmail.com', 'phone' => '01599933322', 'governorate' => 'القاهرة', 'street' => '21 شارع فيصل'],
            ['name' => 'عبد الرحمن يوسف كامل', 'email' => 'abdelrahman.youssef@gmail.com', 'phone' => '01022255566', 'governorate' => 'الجيزة', 'street' => '16 شارع الطيران'],
            ['name' => 'لمياء فتحي جابر', 'email' => 'lamia.fathy@yahoo.com', 'phone' => '01133366644', 'governorate' => 'المنيا', 'street' => '10 شارع الثورة', 'active' => false],
            ['name' => 'سيف الدين محمد وهبة', 'email' => 'saifeldin.m@gmail.com', 'phone' => '01288844411', 'governorate' => 'القليوبية', 'street' => '13 شارع الاستاد', 'active' => false],
        ];
    }
}
