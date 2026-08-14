<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SysWebsiteController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = WebsiteSetting::first();
        if (!$settings) {
            return response()->json(['message' => 'No website settings found'], 404);
        }
        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string',
            'hero_subtitle' => 'nullable|string',
            'hero_cta_text' => 'nullable|string',
            'hero_cta_url' => 'nullable|string',
            'about_title' => 'nullable|string',
            'about_description' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'contact_address' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'terms_content' => 'nullable|string',
            'privacy_content' => 'nullable|string',
            'logo_url' => 'nullable|string',
            'favicon_url' => 'nullable|string',
        ]);

        $settings = WebsiteSetting::firstOrNew();
        $settings->fill($validated)->save();

        return response()->json(['message' => 'Website settings updated', 'settings' => $settings]);
    }
}
