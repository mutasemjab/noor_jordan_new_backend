<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    private array $groups = ['hero', 'about', 'contact', 'social', 'apps'];

    public function edit()
    {
        $settings = SiteSetting::all()->keyBy('key');
        return view('admin.site-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $bilingual = [
            'hero_badge', 'hero_subtitle',
            'about_title', 'about_description',
            'about_value1_title', 'about_value1_desc',
            'about_value2_title', 'about_value2_desc',
            'about_value3_title', 'about_value3_desc',
            'about_value4_title', 'about_value4_desc',
            'contact_address', 'contact_hours',
        ];

        $single = [
            'hero'    => ['hero_image'],
            'about'   => ['about_years', 'about_image_main', 'about_image_secondary'],
            'contact' => ['contact_phone', 'contact_email', 'contact_whatsapp'],
            'social'  => ['social_facebook', 'social_instagram', 'social_youtube', 'social_twitter', 'social_tiktok', 'social_snapchat', 'social_whatsapp'],
            'apps'    => ['app_google_play', 'app_store'],
        ];

        // Groups for bilingual keys
        $keyGroup = [
            'hero_badge' => 'hero', 'hero_subtitle' => 'hero',
            'about_title' => 'about', 'about_description' => 'about',
            'about_value1_title' => 'about', 'about_value1_desc' => 'about',
            'about_value2_title' => 'about', 'about_value2_desc' => 'about',
            'about_value3_title' => 'about', 'about_value3_desc' => 'about',
            'about_value4_title' => 'about', 'about_value4_desc' => 'about',
            'contact_address' => 'contact', 'contact_hours' => 'contact',
        ];

        // Save bilingual fields
        foreach ($bilingual as $key) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value_ar' => $request->input("{$key}_ar"),
                    'value_en' => $request->input("{$key}_en"),
                    'group'    => $keyGroup[$key] ?? 'general',
                ]
            );
        }

        // Save single-value fields (URLs, numbers, etc.)
        foreach ($single as $group => $keys) {
            foreach ($keys as $key) {
                $value = $request->input($key, '');

                // Handle image uploads
                if (in_array($key, ['hero_image', 'about_image_main', 'about_image_secondary'])) {
                    if ($request->hasFile($key)) {
                        $value = uploadImage('assets/uploads/site', $request->file($key));
                    } else {
                        // Keep existing value if no new file uploaded
                        $existing = SiteSetting::raw($key);
                        $value = $existing;
                    }
                }

                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value_ar' => $value, 'value_en' => $value, 'group' => $group]
                );
            }
        }

        SiteSetting::clearCache();

        return redirect()->route('admin.site-settings.edit')
            ->with('success', __('messages.updated_successfully'));
    }

    public function togglePriceDisplay()
    {
        $current = SiteSetting::raw('show_price') ?: '1';
        $new     = $current === '1' ? '2' : '1';

        SiteSetting::set('show_price', $new, $new, 'app');
        SiteSetting::clearCache();

        $label = $new === '1' ? 'مفعّل (يظهر السعر)' : 'معطّل (مخفي في App Store)';

        return back()->with('success', "تم تغيير إعداد السعر: {$label}");
    }
}
