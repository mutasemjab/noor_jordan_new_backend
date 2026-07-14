<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tawjihi = DB::table('categories')
            ->whereNull('parent_id')
            ->where('name_ar', 'like', '%توجيهي%')
            ->first();

        if (! $tawjihi) {
            return;
        }

        // Guard: skip if already restructured
        $alreadyDone = DB::table('categories')
            ->where('parent_id', $tawjihi->id)
            ->where('name_ar', 'like', '%حادي عشر%')
            ->exists();

        if ($alreadyDone) {
            return;
        }

        // Get existing children of توجيهي (e.g. علمي / أدبي tracks)
        $existingChildIds = DB::table('categories')
            ->where('parent_id', $tawjihi->id)
            ->pluck('id');

        // Create الصف الثاني عشر
        $grade12Id = DB::table('categories')->insertGetId([
            'parent_id'   => $tawjihi->id,
            'level'       => ($tawjihi->level + 1),
            'name_ar'     => 'الصف الثاني عشر',
            'name_en'     => 'Grade 12',
            'is_active'   => 1,
            'order_index' => 20,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Move existing children under الثاني عشر
        if ($existingChildIds->isNotEmpty()) {
            DB::table('categories')
                ->whereIn('id', $existingChildIds)
                ->update([
                    'parent_id'  => $grade12Id,
                    'level'      => ($tawjihi->level + 2),
                    'updated_at' => now(),
                ]);
        }

        // Create الصف الحادي عشر (ordered before الثاني عشر)
        $grade11Id = DB::table('categories')->insertGetId([
            'parent_id'   => $tawjihi->id,
            'level'       => ($tawjihi->level + 1),
            'name_ar'     => 'الصف الحادي عشر',
            'name_en'     => 'Grade 11',
            'is_active'   => 1,
            'order_index' => 10,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Create subjects directly under Grade 11
        $subjects = [
            ['name_ar' => 'الرياضيات',        'name_en' => 'Mathematics',       'order_index' => 1],
            ['name_ar' => 'اللغة العربية',     'name_en' => 'Arabic Language',   'order_index' => 2],
            ['name_ar' => 'التاريخ',           'name_en' => 'History',           'order_index' => 3],
            ['name_ar' => 'التربية الإسلامية', 'name_en' => 'Islamic Education', 'order_index' => 4],
            ['name_ar' => 'اللغة الإنجليزية', 'name_en' => 'English Language',  'order_index' => 5],
        ];

        foreach ($subjects as $sub) {
            DB::table('subjects')->insert([
                'category_id' => $grade11Id,
                'name_ar'     => $sub['name_ar'],
                'name_en'     => $sub['name_en'],
                'order_index' => $sub['order_index'],
                'is_active'   => 1,
                'is_elective' => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        $tawjihi = DB::table('categories')
            ->whereNull('parent_id')
            ->where('name_ar', 'like', '%توجيهي%')
            ->first();

        if (! $tawjihi) return;

        $grade11 = DB::table('categories')
            ->where('parent_id', $tawjihi->id)
            ->where('name_ar', 'like', '%حادي عشر%')
            ->first();

        $grade12 = DB::table('categories')
            ->where('parent_id', $tawjihi->id)
            ->where('name_ar', 'like', '%ثاني عشر%')
            ->first();

        if ($grade11) {
            DB::table('subjects')->where('category_id', $grade11->id)->delete();
            DB::table('categories')->where('id', $grade11->id)->delete();
        }

        if ($grade12) {
            DB::table('categories')
                ->where('parent_id', $grade12->id)
                ->update(['parent_id' => $tawjihi->id, 'level' => ($tawjihi->level + 1)]);
            DB::table('categories')->where('id', $grade12->id)->delete();
        }
    }
};
