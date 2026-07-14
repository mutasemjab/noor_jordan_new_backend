<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Hero ──────────────────────────────────────────────────────────
            ['hero_badge',    'hero', '🎓 منصة الباحث التعليمية', '🎓 Al-Bahith Educational Platform'],
            ['hero_subtitle', 'hero',
                'انطلق نحو التميّز مع أفضل المعلمين المتخصصين في الأردن، واحصل على نتائج استثنائية تفتح أمامك أبواب المستقبل.',
                'Launch towards excellence with Jordan\'s finest specialist teachers and achieve exceptional results that open doors to your future.'],
            ['hero_image',    'hero', '', ''],

            // ── About ─────────────────────────────────────────────────────────
            ['about_title',   'about', 'نبنيك اليوم', 'We Build You Today'],
            ['about_description', 'about',
                'أكاديمية الباحث هي مؤسسة تعليمية رائدة في الأردن تأسست قبل أكثر من 25 عامًا بهدف توفير التعليم الأكاديمي المتميز لجميع المراحل الدراسية. نؤمن بأن كل طالب قادر على التفوق مع البيئة التعليمية المناسبة والمعلم الكفء.',
                'Al-Bahith Academy is a leading educational institution in Jordan founded over 25 years ago with the goal of providing distinguished academic education for all grade levels. We believe every student can excel with the right learning environment and capable teacher.'],
            ['about_years',         'about', '25', '25'],
            ['about_image_main',    'about', '', ''],
            ['about_image_secondary','about', '', ''],
            ['about_value1_title',  'about', 'التميّز الأكاديمي', 'Academic Excellence'],
            ['about_value1_desc',   'about', 'نضمن أعلى مستويات الجودة في المحتوى التعليمي والتدريس', 'We guarantee the highest levels of quality in educational content and teaching'],
            ['about_value2_title',  'about', 'المنهج المُحدَّث', 'Updated Curriculum'],
            ['about_value2_desc',   'about', 'مناهج مُطابقة لوزارة التربية ومُطوَّرة بشكل مستمر', 'Curricula aligned with the Ministry of Education and continuously developed'],
            ['about_value3_title',  'about', 'الدعم المستمر', 'Continuous Support'],
            ['about_value3_desc',   'about', 'فريق متخصص لمتابعة الطالب وتقديم الدعم على مدار الساعة', 'Dedicated team to follow up with students and provide round-the-clock support'],
            ['about_value4_title',  'about', 'نتائج مثبتة', 'Proven Results'],
            ['about_value4_desc',   'about', 'آلاف الخريجين الناجحين الذين حققوا أعلى النتائج', 'Thousands of successful graduates who achieved the highest results'],

            // ── Contact ───────────────────────────────────────────────────────
            ['contact_address', 'contact', 'عمّان، الأردن', 'Amman, Jordan'],
            ['contact_phone',   'contact', '+962 6 XXX XXXX', '+962 6 XXX XXXX'],
            ['contact_email',   'contact', 'info@albahithacademy.edu.jo', 'info@albahithacademy.edu.jo'],
            ['contact_hours',   'contact', 'السبت – الخميس: 8 صباحاً – 8 مساءً', 'Sat – Thu: 8:00 AM – 8:00 PM'],
            ['contact_whatsapp','contact', '', ''],

            // ── Social Media ─────────────────────────────────────────────────
            ['social_facebook',  'social', '', ''],
            ['social_instagram',  'social', '', ''],
            ['social_youtube',    'social', '', ''],
            ['social_twitter',    'social', '', ''],
            ['social_tiktok',     'social', '', ''],
            ['social_snapchat',   'social', '', ''],
            ['social_whatsapp',   'social', '', ''],

            // ── Mobile Apps ──────────────────────────────────────────────────
            ['app_google_play', 'apps', '', ''],
            ['app_store',       'apps', '', ''],
        ];

        foreach ($settings as [$key, $group, $ar, $en]) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value_ar' => $ar, 'value_en' => $en, 'group' => $group]
            );
        }
    }
}
