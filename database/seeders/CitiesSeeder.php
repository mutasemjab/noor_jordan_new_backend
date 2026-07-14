<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name_ar' => 'عمّان',    'name_en' => 'Amman'],
            ['name_ar' => 'إربد',     'name_en' => 'Irbid'],
            ['name_ar' => 'الزرقاء', 'name_en' => 'Zarqa'],
            ['name_ar' => 'البلقاء', 'name_en' => 'Balqa'],
            ['name_ar' => 'الكرك',   'name_en' => 'Karak'],
            ['name_ar' => 'مادبا',   'name_en' => 'Madaba'],
            ['name_ar' => 'جرش',     'name_en' => 'Jerash'],
            ['name_ar' => 'عجلون',   'name_en' => 'Ajloun'],
            ['name_ar' => 'معان',    'name_en' => "Ma'an"],
            ['name_ar' => 'الطفيلة','name_en' => 'Tafilah'],
            ['name_ar' => 'العقبة', 'name_en' => 'Aqaba'],
            ['name_ar' => 'المفرق', 'name_en' => 'Mafraq'],
        ];

        $now = now();
        City::insert(array_map(fn ($c) => $c + ['created_at' => $now, 'updated_at' => $now], $cities));
    }
}
