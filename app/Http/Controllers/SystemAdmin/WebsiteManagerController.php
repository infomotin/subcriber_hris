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
                'contact_email' => 'support@amds.test',
                'contact_phone' => '+880 1700 000000',
                'features_json' => [
                    ['title' => 'Multi-Tenant Data Isolation', 'desc' => 'Complete data privacy and subscriber isolation powered by tenant token architecture.'],
                    ['title' => 'ZKTeco Push Protocol', 'desc' => 'Direct TCP/HTTP communication with physical ZKTeco biometric devices.'],
                    ['title' => 'External Server Webhooks', 'desc' => 'Push realtime attendance punches to remote ERP, CSV, or Excel endpoints.'],
                ],
            ]
        );

        return view('system_admin.website.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = WebsiteSetting::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string',
            'hero_cta_text' => 'required|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'features' => 'nullable|array',
        ]);

        $setting->update([
            'hero_title' => $validated['hero_title'],
            'hero_subtitle' => $validated['hero_subtitle'],
            'hero_cta_text' => $validated['hero_cta_text'],
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'features_json' => $validated['features'] ?? [],
        ]);

        return redirect()->route('admin.system.website.index')
            ->with('success', 'Public Landing Website content updated successfully.');
    }
}
