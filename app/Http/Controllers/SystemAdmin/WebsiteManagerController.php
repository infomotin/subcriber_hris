<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteManagerController extends Controller
{
    public function index()
    {
        $setting = WebsiteSetting::firstOrCreate(
            ['id' => 1],
            [
                'hero_title' => 'Cloud-Based ZKTeco Biometric Attendance SaaS Solution',
                'hero_subtitle' => 'Connect unlimited ZKTeco ADMS devices, manage subscribers, export attendance logs, and push realtime data to remote ERP servers.',
                'hero_cta_text' => 'Start 14-Day Free Trial',
                'hero_cta_url' => '/subscription/plans',
                'contact_email' => 'support@amds.test',
                'contact_phone' => '+880 1700 000000',
                'features_json' => [],
                'stats_json' => [],
                'about_description' => '',
                'social_links_json' => [],
                'footer_social_links_json' => [],
                'footer_quick_links_json' => [],
                'menu_links_json' => [],
                'color_theme_json' => [],
                'faq_json' => [],
            ]
        );

        return view('system_admin.website.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = WebsiteSetting::firstOrFail();

        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_cta_text' => 'nullable|string|max:100',
            'hero_cta_url' => 'nullable|string|max:255',
            'hero_bg_image' => 'nullable|string|max:500',
            'features_json' => 'nullable|array',
            'stats_json' => 'nullable|array',
            'about_title' => 'nullable|string|max:255',
            'about_description' => 'nullable|string',
            'about_image' => 'nullable|string|max:500',
            'about_page_content' => 'nullable|string',
            'features_section_json' => 'nullable|array',
            'testimonials_json' => 'nullable|array',
            'cta_section_json' => 'nullable|array',
            'footer_copyright' => 'nullable|string',
            'footer_logo_url' => 'nullable|string|max:500',
            'footer_social_links_json' => 'nullable|array',
            'footer_quick_links_json' => 'nullable|array',
            'social_links_json' => 'nullable|array',
            'menu_links_json' => 'nullable|array',
            'color_theme_json' => 'nullable|array',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string',
            'contact_map_embed' => 'nullable|string',
            'contact_page_content' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'logo_url' => 'nullable|string|max:500',
            'favicon_url' => 'nullable|string|max:500',
            'faq_json' => 'nullable|array',
            'terms_content' => 'nullable|string',
            'privacy_content' => 'nullable|string',
            'pricing_page_content' => 'nullable|string',
            'pricing_features_json' => 'nullable|array',
        ]);

        $setting->update($validated);

        return back()->with('success', 'Website content updated successfully.');
    }

    public function preview()
    {
        $setting = WebsiteSetting::first();

        return view('system_admin.website.preview', compact('setting'));
    }
}