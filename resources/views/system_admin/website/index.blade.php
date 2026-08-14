@extends('layouts.system_admin')

@section('title', 'Website CMS')

@section('content')
<style>
    .cms-sidebar { width: 220px; flex-shrink: 0; position: sticky; top: 0; height: calc(100vh - 60px); overflow-y: auto; }
    .cms-sidebar .nav-link { padding: 8px 14px; font-size: 12px; border-radius: 8px; margin: 1px 0; color: #4b5563; transition: all 0.15s; cursor: pointer; border: none; background: none; width: 100%; text-align: left; display: flex; align-items: center; gap: 8px; }
    .cms-sidebar .nav-link:hover { background: #f1f5f9; color: #1e293b; }
    .cms-sidebar .nav-link.active { background: #4f46e5; color: #fff; }
    .cms-sidebar .nav-link i { width: 18px; text-align: center; font-size: 13px; }
    .cms-section { display: none; }
    .cms-section.active { display: block; }
    .form-label-lg { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px; display: block; }
    .form-control-lg { padding: 10px 14px; font-size: 13px; border: 1px solid #d1d5db; border-radius: 10px; transition: border-color 0.2s, box-shadow 0.2s; }
    .form-control-lg:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); outline: none; }
    .form-text-sm { font-size: 11px; color: #9ca3af; margin-top: 4px; }
    .card-section { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 16px; overflow: hidden; }
    .card-section-header { padding: 14px 18px; background: #fafafa; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 10px; }
    .card-section-header h6 { margin: 0; font-size: 13px; font-weight: 700; color: #1e293b; }
    .card-section-body { padding: 18px; }
    .badge-live { background: #d1fae5; color: #065f46; font-size: 10px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .btn-save { background: #4f46e5; color: #fff; border: none; padding: 10px 28px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #4338ca; }
    .toggle-switch { position: relative; width: 44px; height: 24px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #d1d5db; border-radius: 24px; transition: 0.3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: 0.3s; }
    .toggle-switch input:checked + .toggle-slider { background: #4f46e5; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
    .color-input { width: 40px; height: 40px; border: 2px solid #e5e7eb; border-radius: 10px; cursor: pointer; padding: 0; }
    .item-card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fafafa; margin-bottom: 10px; transition: box-shadow 0.2s; }
    .item-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .item-card .remove-btn { color: #ef4444; cursor: pointer; background: none; border: none; font-size: 16px; padding: 4px 8px; }
    .item-card .remove-btn:hover { color: #dc2626; }
    @media (min-width: 992px) {
        .cms-layout { display: flex; gap: 0; }
        .cms-content { flex: 1; min-width: 0; }
    }
    @media (max-width: 991px) {
        .cms-sidebar { width: 100%; position: static; height: auto; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-globe text-warning me-2"></i> Website CMS Manager</h4>
        <p class="text-muted font-size-13 mb-0">Complete website customization — homepage, pages, SEO, theme, and more.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.system.website.preview') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bx bx-show me-1"></i> Preview Live
        </a>
        <button type="submit" form="websiteForm" class="btn btn-save">
            <i class="bx bx-save me-1"></i> Save All Changes
        </button>
    </div>
</div>

<div class="cms-layout">
    <!-- Sidebar Navigation -->
    <div class="cms-sidebar card border-0 shadow-sm p-2 mb-3">
        <div class="nav flex-column nav-pills" id="cmsNav" role="tablist">
            <a class="nav-link active" href="#cms-general" data-bs-toggle="pill"><i class="bx bx-cog"></i> General Settings</a>
            <a class="nav-link" href="#cms-homepage" data-bs-toggle="pill"><i class="bx bx-home"></i> Homepage</a>
            <a class="nav-link" href="#cms-about" data-bs-toggle="pill"><i class="bx bx-info-circle"></i> About Page</a>
            <a class="nav-link" href="#cms-contact" data-bs-toggle="pill"><i class="bx bx-envelope"></i> Contact Page</a>
            <a class="nav-link" href="#cms-faq" data-bs-toggle="pill"><i class="bx bx-help-circle"></i> FAQ</a>
            <a class="nav-link" href="#cms-legal" data-bs-toggle="pill"><i class="bx bx-file"></i> Legal Pages</a>
            <a class="nav-link" href="#cms-pricing" data-bs-toggle="pill"><i class="bx bx-tag"></i> Pricing Page</a>
            <a class="nav-link" href="#cms-footer" data-bs-toggle="pill"><i class="bx bx-columns"></i> Footer</a>
            <a class="nav-link" href="#cms-menu" data-bs-toggle="pill"><i class="bx bx-menu"></i> Navigation</a>
            <a class="nav-link" href="#cms-theme" data-bs-toggle="pill"><i class="bx bx-palette"></i> Theme & Colors</a>
        </div>
    </div>

    <!-- Content Area -->
    <div class="cms-content">
        <form method="POST" action="{{ route('admin.system.website.update') }}" id="websiteForm">
            @csrf
            <div class="tab-content" id="cmsTabContent">

                {{-- GENERAL --}}
                <div class="cms-section active" id="cms-general">
                    <div class="card-section">
                        <div class="card-section-header">
                            <i class="bx bx-lock text-primary"></i>
                            <h6>Brand Identity</h6>
                        </div>
                        <div class="card-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-lg">Logo URL</label>
                                    <input type="url" name="logo_url" class="form-control form-control-lg" value="{{ old('logo_url', $setting->logo_url) }}" placeholder="https://example.com/logo.png">
                                    <div class="form-text-sm">Your company logo (shown in header and navbar).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-lg">Favicon URL</label>
                                    <input type="url" name="favicon_url" class="form-control form-control-lg" value="{{ old('favicon_url', $setting->favicon_url) }}" placeholder="https://example.com/favicon.ico">
                                    <div class="form-text-sm">Browser tab icon (16x16 or 32x32 pixels).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-lg">Footer Logo URL</label>
                                    <input type="url" name="footer_logo_url" class="form-control form-control-lg" value="{{ old('footer_logo_url', $setting->footer_logo_url) }}" placeholder="https://example.com/footer-logo.png">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-section">
                        <div class="card-section-header">
                            <i class="bx bx-search text-primary"></i>
                            <h6>SEO Settings</h6>
                        </div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">SEO Page Title</label>
                                <input type="text" name="seo_title" class="form-control form-control-lg" value="{{ old('seo_title', $setting->seo_title) }}" placeholder="Page title for search engines (50-60 chars)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">SEO Meta Description</label>
                                <textarea name="seo_description" class="form-control form-control-lg" rows="3" placeholder="Meta description shown in search results (150-160 chars)">{{ old('seo_description', $setting->seo_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">SEO Keywords</label>
                                <input type="text" name="seo_keywords" class="form-control form-control-lg" value="{{ old('seo_keywords', $setting->seo_keywords) }}" placeholder="attendance, biometric, saas, ZKTeco, time clock">
                                <div class="form-text-sm">Comma-separated keywords for search engines.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save General Settings</button>
                    </div>
                </div>

                {{-- HOMEPAGE --}}
                <div class="cms-section" id="cms-homepage">
                    <div class="card-section">
                        <div class="card-section-header">
                            <i class="bx bx-heart text-danger"></i>
                            <h6>Hero Banner</h6>
                        </div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">Hero Title</label>
                                <input type="text" name="hero_title" class="form-control form-control-lg" value="{{ old('hero_title', $setting->hero_title) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">Hero Subtitle</label>
                                <textarea name="hero_subtitle" class="form-control form-control-lg" rows="4" required>{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-lg">CTA Button Text</label>
                                    <input type="text" name="hero_cta_text" class="form-control form-control-lg" value="{{ old('hero_cta_text', $setting->hero_cta_text) }}" placeholder="Start Free Trial">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-lg">CTA Button URL</label>
                                    <input type="url" name="hero_cta_url" class="form-control form-control-lg" value="{{ old('hero_cta_url', $setting->hero_cta_url) }}" placeholder="/subscription/plans">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label-lg">Hero Background Image URL (optional)</label>
                                <input type="url" name="hero_bg_image" class="form-control form-control-lg" value="{{ old('hero_bg_image', $setting->hero_bg_image) }}" placeholder="https://example.com/hero-bg.jpg">
                                <div class="form-text-sm">Leave empty for a gradient background.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-section">
                        <div class="card-section-header d-flex justify-content-between">
                            <h6><i class="bx bxs-star text-warning me-1"></i> Features</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary cms-add-item" data-target="home-features"><i class="bx bx-plus me-1"></i> Add</button>
                        </div>
                        <div class="card-section-body">
                            <div id="home-features">
                                @php $features = old('features_json', $setting->features_json ?? []); @endphp
                                @if(!empty($features))
                                    @foreach($features as $i => $f)
                                        <div class="item-card">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-4">
                                                    <input type="text" name="features_json[{{ $i }}][title]" class="form-control form-control-lg" value="{{ $f['title'] ?? '' }}" placeholder="Feature title">
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" name="features_json[{{ $i }}][desc]" class="form-control form-control-lg" value="{{ $f['desc'] ?? '' }}" placeholder="Description">
                                                </div>
                                                <div class="col-2 text-center">
                                                    <button type="button" class="cms-remove-item btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-section">
                        <div class="card-section-header d-flex justify-content-between">
                            <h6><i class="bx bx-bar-chart-alt-2 text-success me-1"></i> Statistics</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary cms-add-item" data-target="home-stats"><i class="bx bx-plus me-1"></i> Add</button>
                        </div>
                        <div class="card-section-body">
                            <div id="home-stats">
                                @php $stats = old('stats_json', $setting->stats_json ?? []); @endphp
                                @if(!empty($stats))
                                    @foreach($stats as $i => $s)
                                        <div class="item-card">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-3">
                                                    <input type="text" name="stats_json[{{ $i }}][number]" class="form-control form-control-lg" value="{{ $s['number'] ?? '' }}" placeholder="100+">
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" name="stats_json[{{ $i }}][label]" class="form-control form-control-lg" value="{{ $s['label'] ?? '' }}" placeholder="Clients served">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" name="stats_json[{{ $i }}][icon]" class="form-control form-control-lg" value="{{ $s['icon'] ?? '' }}" placeholder="bx-user">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Homepage</button>
                    </div>
                </div>

                {{-- ABOUT PAGE --}}
                <div class="cms-section" id="cms-about">
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-info-circle text-primary"></i><h6>About Page</h6></div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">About Page Title</label>
                                <input type="text" name="about_title" class="form-control form-control-lg" value="{{ old('about_title', $setting->about_title) }}" placeholder="About Our Company">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">About Description</label>
                                <textarea name="about_description" class="form-control form-control-lg" rows="4">{{ old('about_description', $setting->about_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">About Image URL</label>
                                <input type="url" name="about_image" class="form-control form-control-lg" value="{{ old('about_image', $setting->about_image) }}" placeholder="https://example.com/about.jpg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">About Page Full HTML Content</label>
                                <textarea name="about_page_content" class="form-control form-control-lg" rows="10" style="font-family: 'Courier New', monospace; font-size: 12px;">{{ old('about_page_content', $setting->about_page_content) }}</textarea>
                                <div class="form-text-sm">Rich HTML content for the full About page. Supports images, tables, lists.</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save About Page</button>
                    </div>
                </div>

                {{-- CONTACT PAGE --}}
                <div class="cms-section" id="cms-contact">
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-map text-primary"></i><h6>Contact Information</h6></div>
                        <div class="card-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-lg">Support Email</label>
                                    <input type="email" name="contact_email" class="form-control form-control-lg" value="{{ old('contact_email', $setting->contact_email) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-lg">Support Phone</label>
                                    <input type="text" name="contact_phone" class="form-control form-control-lg" value="{{ old('contact_phone', $setting->contact_phone) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label-lg">Physical Address</label>
                                    <textarea name="contact_address" class="form-control form-control-lg" rows="3">{{ old('contact_address', $setting->contact_address) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-lg">Google Maps Embed HTML</label>
                                    <textarea name="contact_map_embed" class="form-control form-control-lg" rows="4" style="font-family: monospace; font-size: 12px;">{{ old('contact_map_embed', $setting->contact_map_embed) }}</textarea>
                                    <div class="form-text-sm">Paste the iframe HTML from Google Maps.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-file text-primary"></i><h6>Contact Page Content</h6></div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">Contact Page HTML</label>
                                <textarea name="contact_page_content" class="form-control form-control-lg" rows="8" style="font-family: monospace;">{{ old('contact_page_content', $setting->contact_page_content) }}</textarea>
                                <div class="form-text-sm">Rich HTML for the Contact Us page body content.</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Contact Page</button>
                    </div>
                </div>

                {{-- FAQ --}}
                <div class="cms-section" id="cms-faq">
                    <div class="card-section">
                        <div class="card-section-header d-flex justify-content-between">
                            <h6><i class="bx bx-help-circle text-primary me-1"></i> FAQ Items</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary cms-add-item" data-target="faq-list"><i class="bx bx-plus me-1"></i> Add Question</button>
                        </div>
                        <div class="card-section-body">
                            <div id="faq-list">
                                @php $faqs = old('faq_json', $setting->faq_json ?? []); @endphp
                                @if(!empty($faqs))
                                    @foreach($faqs as $i => $faq)
                                        <div class="item-card">
                                            <div class="mb-2">
                                                <label class="form-label-sm fw-bold">Question</label>
                                                <input type="text" name="faq_json[{{ $i }}][q]" class="form-control form-control-lg" value="{{ $faq['q'] ?? '' }}" placeholder="FAQ question">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label-sm fw-bold">Answer</label>
                                                <textarea name="faq_json[{{ $i }}][a]" class="form-control form-control-lg" rows="3" placeholder="FAQ answer">{{ $faq['a'] ?? '' }}</textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="cms-remove-item btn btn-sm btn-outline-danger"><i class="bx bx-trash me-1"></i> Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save FAQ</button>
                    </div>
                </div>

                {{-- LEGAL PAGES --}}
                <div class="cms-section" id="cms-legal">
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-file text-primary"></i><h6>Terms & Conditions</h6></div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">Terms Content (HTML)</label>
                                <textarea name="terms_content" class="form-control form-control-lg" rows="12" style="font-family: monospace; font-size: 12px;">{{ old('terms_content', $setting->terms_content) }}</textarea>
                                <div class="form-text-sm">Full HTML content for the Terms & Conditions page.</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-shield text-primary"></i><h6>Privacy Policy</h6></div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">Privacy Policy Content (HTML)</label>
                                <textarea name="privacy_content" class="form-control form-control-lg" rows="12" style="font-family: monospace; font-size: 12px;">{{ old('privacy_content', $setting->privacy_content) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Legal Pages</button>
                    </div>
                </div>

                {{-- PRICING PAGE --}}
                <div class="cms-section" id="cms-pricing">
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-tag text-primary"></i><h6>Pricing Page Content</h6></div>
                        <div class="card-section-body">
                            <div class="mb-3">
                                <label class="form-label-lg">Pricing Page Intro HTML</label>
                                <textarea name="pricing_page_content" class="form-control form-control-lg" rows="6">{{ old('pricing_page_content', $setting->pricing_page_content) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-lg">Pricing Features (JSON)</label>
                                <textarea name="pricing_features_json" class="form-control form-control-lg" rows="5" style="font-family: monospace; font-size: 12px;" placeholder='[{"icon":"bx-check","text":"50 Devices"},{"icon":"bx-check","text":"24/7 Support"}]'>{{ json_encode(old('pricing_features_json', $setting->pricing_features_json ?? []), JSON_PRETTY_PRINT) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Pricing Page</button>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="cms-section" id="cms-footer">
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-data text-primary"></i><h6>Footer Settings</h6></div>
                        <div class="card-section-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-lg">Footer Logo URL</label>
                                    <input type="url" name="footer_logo_url" class="form-control form-control-lg" value="{{ old('footer_logo_url', $setting->footer_logo_url) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-lg">Copyright Text</label>
                                    <input type="text" name="footer_copyright" class="form-control form-control-lg" value="{{ old('footer_copyright', $setting->footer_copyright) }}" placeholder="© 2026 AMDS. All rights reserved.">
                                </div>
                                <div class="col-12">
                                    <label class="form-label-lg">Footer Quick Links (JSON)</label>
                                    <textarea name="footer_quick_links_json" class="form-control form-control-lg" rows="4" style="font-family: monospace; font-size: 12px;" placeholder='[{"label":"Home","url":"/"},{"label":"About","url":"/about"}]'>{{ json_encode(old('footer_quick_links_json', $setting->footer_quick_links_json ?? []), JSON_PRETTY_PRINT) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-lg">Footer Social Links (JSON)</label>
                                    <textarea name="footer_social_links_json" class="form-control form-control-lg" rows="4" style="font-family: monospace; font-size: 12px;" placeholder='[{"icon":"bx bxl-facebook","url":"https://facebook.com/..."},{"icon":"bx bxl-linkedin","url":"..."}]'>{{ json_encode(old('footer_social_links_json', $setting->footer_social_links_json ?? []), JSON_PRETTY_PRINT) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Footer</button>
                    </div>
                </div>

                {{-- MENU --}}
                <div class="cms-section" id="cms-menu">
                    <div class="card-section">
                        <div class="card-section-header d-flex justify-content-between">
                            <h6><i class="bx bx-menu text-primary"></i> Navigation Menu Links</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary cms-add-item" data-target="menu-list"><i class="bx bx-plus me-1"></i> Add Link</button>
                        </div>
                        <div class="card-section-body">
                            <div id="menu-list">
                                @php $menuLinks = old('menu_links_json', $setting->menu_links_json ?? []); @endphp
                                @if(!empty($menuLinks))
                                    @foreach($menuLinks as $i => $link)
                                        <div class="item-card">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-3">
                                                    <input type="text" name="menu_links_json[{{ $i }}][label]" class="form-control form-control-lg" value="{{ $link['label'] ?? '' }}" placeholder="Menu label">
                                                </div>
                                                <div class="col-5">
                                                    <input type="url" name="menu_links_json[{{ $i }}][url]" class="form-control form-control-lg" value="{{ $link['url'] ?? '' }}" placeholder="/about">
                                                </div>
                                                <div class="col-3">
                                                    <select name="menu_links_json[{{ $i }}][target]" class="form-select form-control-lg">
                                                        <option value="_self" {{ ($link['target'] ?? '_self') === '_self' ? 'selected' : '' }}>Same Tab</option>
                                                        <option value="_blank" {{ ($link['target'] ?? '_self') === '_blank' ? 'selected' : '' }}>New Tab</option>
                                                    </select>
                                                </div>
                                                <div class="col-1 text-center">
                                                    <button type="button" class="cms-remove-item btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Menu</button>
                    </div>
                </div>

                {{-- THEME --}}
                <div class="cms-section" id="cms-theme">
                    <div class="card-section">
                        <div class="card-section-header"><i class="bx bx-palette text-primary"></i><h6>Color Theme</h6></div>
                        <div class="card-section-body">
                            @php $theme = old('color_theme_json', $setting->color_theme_json ?? []); @endphp
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label-lg">Primary Color</label>
                                    <input type="color" name="color_theme_json[primary]" class="color-input" value="{{ $theme['primary'] ?? '#4f46e5' }}" title="Primary">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-lg">Secondary Color</label>
                                    <input type="color" name="color_theme_json[secondary]" class="color-input" value="{{ $theme['secondary'] ?? '#6366f1' }}" title="Secondary">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-lg">Accent Color</label>
                                    <input type="color" name="color_theme_json[accent]" class="color-input" value="{{ $theme['accent'] ?? '#f59e0b' }}" title="Accent">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-lg">Background Color</label>
                                    <input type="color" name="color_theme_json[bg]" class="color-input" value="{{ $theme['bg'] ?? '#f8fafc' }}" title="Background">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-lg">Card Background</label>
                                    <input type="color" name="color_theme_json[card_bg]" class="color-input" value="{{ $theme['card_bg'] ?? '#ffffff' }}" title="Card">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-lg">Text Color</label>
                                    <input type="color" name="color_theme_json[text]" class="color-input" value="{{ $theme['text'] ?? '#1e293b' }}" title="Text">
                                </div>
                            </div>
                            <div class="mt-3 p-3 rounded-3 border" style="background: {{ $theme['bg'] ?? '#f8fafc' }}; color: {{ $theme['text'] ?? '#1e293b' }};">
                                <strong style="color: {{ $theme['primary'] ?? '#4f46e5' }}">Preview Text</strong>
                                <p style="font-size: 12px;">This is a live preview of your theme colors applied to text and backgrounds.</p>
                                <button class="btn btn-sm" style="background: {{ $theme['primary'] ?? '#4f46e5' }}; color: #fff; border: none; border-radius: 8px;">Preview Button</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-4 no-print">
                        <button type="submit" class="btn btn-save">Save Theme</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    var navLinks = document.querySelectorAll('#cmsNav .nav-link');
    var sections = document.querySelectorAll('.cms-section');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var target = this.getAttribute('href').substring(1);
            navLinks.forEach(function(l) { l.classList.remove('active'); });
            this.classList.add('active');
            sections.forEach(function(s) { s.classList.remove('active'); });
            var targetSection = document.getElementById(target);
            if (targetSection) targetSection.classList.add('active');
        });
    });

    // Add dynamic item
    document.addEventListener('click', function(e) {
        if (e.target.closest('.cms-add-item')) {
            var btn = e.target.closest('.cms-add-item');
            var targetId = btn.dataset.target;
            var container = document.getElementById(targetId);
            if (!container) return;
            var idx = container.children.length;
            var div = document.createElement('div');
            div.className = 'item-card';

            if (targetId === 'home-features') {
                div.innerHTML = '<div class="row g-2 align-items-center"><div class="col-4"><input type="text" name="features_json[' + idx + '][title]" class="form-control form-control-lg" placeholder="Feature title"></div><div class="col-6"><input type="text" name="features_json[' + idx + '][desc]" class="form-control form-control-lg" placeholder="Description"></div><div class="col-2 text-center"><button type="button" class="cms-remove-item btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></div></div>';
            } else if (targetId === 'home-stats') {
                div.innerHTML = '<div class="row g-2 align-items-center"><div class="col-3"><input type="text" name="stats_json[' + idx + '][number]" class="form-control form-control-lg" placeholder="100"></div><div class="col-4"><input type="text" name="stats_json[' + idx + '][label]" class="form-control form-control-lg" placeholder="Clients served"></div><div class="col-5"><input type="text" name="stats_json[' + idx + '][icon]" class="form-control form-control-lg" placeholder="bx-user"></div></div>';
            } else if (targetId === 'faq-list') {
                div.innerHTML = '<div class="mb-2"><label class="form-label-sm fw-bold">Question</label><input type="text" name="faq_json[' + idx + '][q]" class="form-control form-control-lg" placeholder="FAQ question"></div><div class="mb-2"><label class="form-label-sm fw-bold">Answer</label><textarea name="faq_json[' + idx + '][a]" class="form-control form-control-lg" rows="2" placeholder="FAQ answer"></textarea></div><div class="text-end"><button type="button" class="cms-remove-item btn btn-sm btn-outline-danger"><i class="bx bx-trash me-1"></i> Remove</button></div>';
            } else if (targetId === 'menu-list') {
                div.innerHTML = '<div class="row g-2 align-items-center"><div class="col-3"><input type="text" name="menu_links_json[' + idx + '][label]" class="form-control form-control-lg" placeholder="Menu label"></div><div class="col-5"><input type="url" name="menu_links_json[' + idx + '][url]" class="form-control form-control-lg" placeholder="/about"></div><div class="col-3"><select name="menu_links_json[' + idx + '][target]" class="form-select form-control-lg"><option value="_self">Same Tab</option><option value="_blank">New Tab</option></select></div><div class="col-1 text-center"><button type="button" class="cms-remove-item btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></div></div>';
            }
            container.appendChild(div);
        }

        if (e.target.closest('.cms-remove-item')) {
            var card = e.target.closest('.item-card');
            if (card) card.remove();
        }
    });
});
</script>
@endsection
