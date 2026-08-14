<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'hero_cta_url',
        'hero_bg_image',
        'features_json',
        'hero_features_json',
        'stats_json',
        'about_title',
        'about_description',
        'about_image',
        'about_page_content',
        'features_section_json',
        'testimonials_json',
        'cta_section_json',
        'footer_copyright',
        'footer_social_links_json',
        'footer_quick_links_json',
        'footer_logo_url',
        'social_links_json',
        'contact_email',
        'contact_phone',
        'contact_address',
        'contact_map_embed',
        'contact_page_content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'logo_url',
        'favicon_url',
        'faq_json',
        'terms_content',
        'privacy_content',
        'pricing_page_content',
        'pricing_features_json',
        'color_theme_json',
        'menu_links_json',
    ];

    protected $casts = [
        'features_json' => 'array',
        'hero_features_json' => 'array',
        'stats_json' => 'array',
        'features_section_json' => 'array',
        'testimonials_json' => 'array',
        'cta_section_json' => 'array',
        'footer_social_links_json' => 'array',
        'footer_quick_links_json' => 'array',
        'social_links_json' => 'array',
        'faq_json' => 'array',
        'color_theme_json' => 'array',
        'menu_links_json' => 'array',
        'pricing_features_json' => 'array',
        'terms_content' => 'string',
        'privacy_content' => 'string',
        'about_page_content' => 'string',
        'contact_page_content' => 'string',
        'pricing_page_content' => 'string',
    ];
}