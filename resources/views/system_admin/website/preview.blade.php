@extends('layouts.system_admin')
@section('title', 'Website Preview')
@section('content')
@php
    $setting = WebsiteSetting::first();
    if (!$setting) { echo '<div class="p-5 text-center"><h4>No website settings found.</h4></div>'; return; }
    $features = $setting->features_json ?? [];
    $stats = $setting->stats_json ?? [];
    $testimonials = $setting->testimonials_json ?? [];
    $socialLinks = $setting->social_links_json ?? $setting->footer_social_links_json ?? [];
    $menuLinks = $setting->menu_links_json ?? [];
    $theme = $setting->color_theme_json ?? [];
    $primary = $theme['primary'] ?? '#4f46e5';
    $secondary = $theme['secondary'] ?? '#6366f1';
@endphp
<div style="max-width:100%; margin:0 auto; background:#fff; font-family:'Segoe UI',sans-serif;">

    <!-- Top Bar -->
    <div style="background:#f8fafc;padding:8px 0;font-size:11px;border-bottom:1px solid #e2e8f0;">
        <div style="max-width:1200px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;">
            <span><i class="bx bxs-envelope"></i> {{ $setting->contact_email ?? 'support@amds.test' }}</span>
            <span><i class="bx bxs-phone"></i> {{ $setting->contact_phone ?? '+880 1700 000000' }}</span>
        </div>
    </div>

    <!-- Navbar -->
    <nav style="background:#fff;padding:12px 0;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="max-width:1200px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;align-items:center;gap:8px;">
                @if($setting->logo_url)
                    <img src="{{ $setting->logo_url }}" alt="Logo" style="height:36px;">
                @else
                    <div style="width:36px;height:36px;background:{{ $primary }};color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;">A</div>
                @endif
                <span style="font-weight:700;font-size:16px;color:#0f172a;">ADMS</span>
            </div>
            <div style="display:flex;gap:20px;align-items:center;font-size:13px;">
                @foreach($menuLinks as $link)
                    <a href="{{ $link['url'] ?? '#' }}" target="{{ $link['target'] ?? '_self' }}" style="color:#4b5563;text-decoration:none;font-weight:500;">{{ $link['label'] ?? 'Link' }}</a>
                @endforeach
                <a href="#subscribe" style="background:{{ $primary }};color:#fff;padding:6px 16px;border-radius:20px;text-decoration:none;font-size:12px;font-weight:600;">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section style="background:{{ $setting->hero_bg_image ? 'url('.e($setting->hero_bg_image).') center/cover' : 'linear-gradient(135deg,'.$primary.','.$secondary.')' }};padding:80px 20px;color:#fff;text-align:center;">
        <div style="max-width:800px;margin:0 auto;">
            <h1 style="font-size:2.5rem;font-weight:700;margin-bottom:16px;">{{ $setting->hero_title }}</h1>
            <p style="font-size:1.1rem;opacity:0.9;margin-bottom:30px;line-height:1.6;">{{ $setting->hero_subtitle }}</p>
            <a href="{{ $setting->hero_cta_url ?? '/subscription/plans' }}" style="background:#fff;color:{{ $primary }};padding:12px 32px;border-radius:50px;font-weight:700;text-decoration:none;font-size:14px;">{{ $setting->hero_cta_text }}</a>
        </div>
    </section>

    <!-- Stats -->
    @if(!empty($stats))
    <section style="padding:40px 20px;background:#f8fafc;">
        <div style="max-width:1000px;margin:0 auto;display:grid;grid-template-columns:repeat({{ min(count($stats),4) }},1fr);gap:20px;text-align:center;">
            @foreach($stats as $stat)
                <div>
                    <div style="font-size:2rem;font-weight:700;color:{{ $primary }};">{{ $stat['number'] ?? 0 }}</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:4px;">{{ $stat['label'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Features -->
    @if(!empty($features))
    <section style="padding:60px 20px;">
        <div style="max-width:1200px;margin:0 auto;">
            <h2 style="text-align:center;font-size:1.8rem;margin-bottom:10px;">Why Choose Us</h2>
            <p style="text-align:center;color:#6b7280;margin-bottom:40px;">Powerful features for your business</p>
            <div style="display:grid;grid-template-columns:repeat({{ min(count($features),3) }},1fr);gap:24px;">
                @foreach($features as $feat)
                    <div style="text-align:center;padding:30px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                        <div style="width:56px;height:56px;background:{{ $primary }}1a;color:{{ $primary }};border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;">
                            <i class="bx bx-rocket"></i>
                        </div>
                        <h5 style="font-weight:700;margin-bottom:8px;">{{ $feat['title'] ?? '' }}</h5>
                        <p style="font-size:13px;color:#6b7280;">{{ $feat['desc'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- About -->
    @if($setting->about_title || $setting->about_description)
    <section style="padding:60px 20px;background:#f8fafc;">
        <div style="max-width:1000px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;">
            <div>
                <h2 style="font-size:1.8rem;margin-bottom:16px;">{{ $setting->about_title }}</h2>
                <p style="color:#4b5563;line-height:1.8;">{{ $setting->about_description }}</p>
            </div>
            @if($setting->about_image)
                <div><img src="{{ $setting->about_image }}" alt="About" style="width:100%;border-radius:12px;"></div>
            @endif
        </div>
    </section>
    @endif

    <!-- Testimonials -->
    @if(!empty($testimonials))
    <section style="padding:60px 20px;">
        <div style="max-width:1000px;margin:0 auto;">
            <h2 style="text-align:center;font-size:1.8rem;margin-bottom:40px;">What Our Clients Say</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;">
                @foreach($testimonials as $t)
                    <div style="padding:24px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                        <p style="font-style:italic;color:#4b5563;margin-bottom:16px;">"{{ $t['text'] ?? '' }}"</p>
                        <div style="font-weight:700;font-size:14px;">{{ $t['author'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section style="padding:60px 20px;background:{{ $primary }};color:#fff;text-align:center;">
        <div style="max-width:700px;margin:0 auto;">
            <h2 style="font-size:1.8rem;margin-bottom:12px;">Ready to Get Started?</h2>
            <p style="opacity:0.9;margin-bottom:30px;">Join thousands of businesses using ADMS for their biometric attendance needs.</p>
            <a href="{{ $setting->hero_cta_url ?? '/subscription/plans' }}" style="background:#fff;color:{{ $primary }};padding:14px 36px;border-radius:50px;font-weight:700;text-decoration:none;font-size:15px;">{{ $setting->hero_cta_text }}</a>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background:#0f172a;color:#cbd5e1;padding:40px 20px 20px;">
        <div style="max-width:1200px;margin:0 auto;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:30px;margin-bottom:30px;">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                        @if($setting->footer_logo_url)
                            <img src="{{ $setting->footer_logo_url }}" alt="Logo" style="height:28px;">
                        @else
                            <div style="width:28px;height:28px;background:{{ $primary }};color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;">A</div>
                        @endif
                        <span style="font-weight:700;color:#fff;">ADMS</span>
                    </div>
                    <p style="font-size:12px;line-height:1.6;">{{ $setting->hero_title ?? 'ZKTeco Biometric Attendance SaaS' }}</p>
                </div>
                @if(!empty($setting->footer_quick_links_json))
                <div>
                    <h6 style="color:#fff;font-weight:700;margin-bottom:12px;">Quick Links</h6>
                    @foreach($setting->footer_quick_links_json as $link)
                        <div><a href="{{ $link['url'] ?? '#' }}" style="color:#cbd5e1;text-decoration:none;font-size:13px;">{{ $link['label'] ?? 'Link' }}</a></div>
                    @endforeach
                </div>
                @endif
                <div>
                    <h6 style="color:#fff;font-weight:700;margin-bottom:12px;">Contact Us</h6>
                    <p style="font-size:12px;margin-bottom:4px;"><i class="bx bxs-envelope"></i> {{ $setting->contact_email ?? '' }}</p>
                    <p style="font-size:12px;margin-bottom:4px;"><i class="bx bxs-phone"></i> {{ $setting->contact_phone ?? '' }}</p>
                    <p style="font-size:12px;"><i class="bx bxs-map"></i> {{ Str::limit($setting->contact_address ?? '', 100) }}</p>
                </div>
            </div>
            <div style="border-top:1px solid #334155;padding-top:16px;display:flex;justify-content:space-between;align-items:center;font-size:12px;">
                <span>{{ $setting->footer_copyright ?? '© 2026 ADMS. All rights reserved.' }}</span>
                <div style="display:flex;gap:12px;">
                    @foreach($socialLinks as $social)
                        <a href="{{ $social['url'] ?? '#' }}" target="_blank" style="color:#cbd5e1;font-size:18px;" title="{{ $social['icon'] ?? 'Social' }}"><i class="{{ $social['icon'] ?? 'bx bxl-globe' }}"></i></a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection