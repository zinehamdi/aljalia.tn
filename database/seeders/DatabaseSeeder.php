<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Countries with Continent Grouping
        $countries = [
            // Europe
            ['name' => 'فرنسا', 'code' => 'FR', 'icon' => '🇫🇷', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'إيطاليا', 'code' => 'IT', 'icon' => '🇮🇹', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'ألمانيا', 'code' => 'DE', 'icon' => '🇩🇪', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'بلجيكا', 'code' => 'BE', 'icon' => '🇧🇪', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'إسبانيا', 'code' => 'ES', 'icon' => '🇪🇸', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'بريطانيا', 'code' => 'GB', 'icon' => '🇬🇧', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'سويسرا', 'code' => 'CH', 'icon' => '🇨🇭', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'النمسا', 'code' => 'AT', 'icon' => '🇦🇹', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            ['name' => 'هولندا', 'code' => 'NL', 'icon' => '🇳🇱', 'continent' => 'europe', 'continent_ar' => 'أوروبا'],
            
            // Gulf
            ['name' => 'السعودية', 'code' => 'SA', 'icon' => '🇸🇦', 'continent' => 'gulf', 'continent_ar' => 'الخليج'],
            ['name' => 'الإمارات', 'code' => 'AE', 'icon' => '🇦🇪', 'continent' => 'gulf', 'continent_ar' => 'الإمارات'],
            ['name' => 'قطر', 'code' => 'QA', 'icon' => '🇶🇦', 'continent' => 'gulf', 'continent_ar' => 'الخليج'],
            ['name' => 'الكويت', 'code' => 'KW', 'icon' => '🇰🇼', 'continent' => 'gulf', 'continent_ar' => 'الخليج'],
            ['name' => 'عمان', 'code' => 'OM', 'icon' => '🇴🇲', 'continent' => 'gulf', 'continent_ar' => 'الخليج'],
            
            // North America
            ['name' => 'كندا', 'code' => 'CA', 'icon' => '🇨🇦', 'continent' => 'north_america', 'continent_ar' => 'أمريكا الشمالية'],
            ['name' => 'أمريكا', 'code' => 'US', 'icon' => '🇺🇸', 'continent' => 'north_america', 'continent_ar' => 'أمريكا الشمالية'],
        ];

        foreach ($countries as $countryData) {
            $country = \App\Models\Country::create($countryData);
            
            switch ($country->code) {
                case 'FR':
                    $country->cities()->createMany([['name' => 'باريس'], ['name' => 'مارسيليا'], ['name' => 'ليون'], ['name' => 'نيس']]);
                    break;
                case 'IT':
                    $country->cities()->createMany([['name' => 'ميلانو'], ['name' => 'روما'], ['name' => 'باليرمو'], ['name' => 'نابولي'], ['name' => 'كالياري']]);
                    break;
                case 'DE':
                    $country->cities()->createMany([['name' => 'برلين'], ['name' => 'ميونخ'], ['name' => 'فرانكفورت'], ['name' => 'هامبورغ']]);
                    break;
                case 'ES':
                    $country->cities()->createMany([['name' => 'مدريد'], ['name' => 'برشلونة'], ['name' => 'فالنسيا']]);
                    break;
                case 'GB':
                    $country->cities()->createMany([['name' => 'لندن'], ['name' => 'مانشستر'], ['name' => 'برمنغهام']]);
                    break;
                case 'CH':
                    $country->cities()->createMany([['name' => 'جنيف'], ['name' => 'زيورخ'], ['name' => 'لوزان']]);
                    break;
                case 'NL':
                    $country->cities()->createMany([['name' => 'أمستردام'], ['name' => 'روتردام']]);
                    break;
                case 'SA':
                    $country->cities()->createMany([['name' => 'الرياض'], ['name' => 'جدة'], ['name' => 'الدمام'], ['name' => 'المدينة المنورة']]);
                    break;
                case 'AE':
                    $country->cities()->createMany([['name' => 'دبي'], ['name' => 'أبو ظبي'], ['name' => 'الشارقة']]);
                    break;
                case 'QA':
                    $country->cities()->createMany([['name' => 'الدوحة'], ['name' => 'الريان']]);
                    break;
                case 'CA':
                    $country->cities()->createMany([['name' => 'مونتريال'], ['name' => 'تورونتو'], ['name' => 'كيبيك'], ['name' => 'أوتاوا']]);
                    break;
                case 'US':
                    $country->cities()->createMany([['name' => 'نيويورك'], ['name' => 'واشنطن'], ['name' => 'هيوستن'], ['name' => 'لوس أنجلوس']]);
                    break;
            }
        }

        // Seed initial Categories (الحومة)
        $categories = [
            ['name' => 'طيّب تونسي (هريسة، كسكسي...)', 'slug' => 'tunisian-food', 'icon' => 'utensils', 'order' => 1],
            ['name' => 'وين نركشو (قهاوي وشيشة)', 'slug' => 'cafes', 'icon' => 'coffee', 'order' => 2],
            ['name' => 'نحب نتعرف (صحبة/عرس)', 'slug' => 'social', 'icon' => 'users', 'order' => 3],
            ['name' => 'شوفلي حل (أوراق وإقامة)', 'slug' => 'legal', 'icon' => 'file-text', 'order' => 4],
            ['name' => 'الديوانة والـ FCR', 'slug' => 'diwana-fcr', 'icon' => 'car', 'order' => 5],
            ['name' => 'فزعة (مساعدة عاجلة)', 'slug' => 'help', 'icon' => 'life-buoy', 'order' => 6],
            ['name' => 'إي-سوق (بيع وشري)', 'slug' => 'e-souq', 'icon' => 'shopping-cart', 'order' => 7],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }
    }
}
