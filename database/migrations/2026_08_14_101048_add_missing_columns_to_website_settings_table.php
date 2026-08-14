<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->string('hero_cta_url')->nullable()->after('hero_cta_text');
            $table->string('hero_bg_image')->nullable()->after('hero_cta_url');
            $table->json('hero_features_json')->nullable()->after('hero_bg_image');
            $table->json('stats_json')->nullable()->after('hero_features_json');
            $table->string('about_title')->nullable()->after('stats_json');
            $table->text('about_description')->nullable()->after('about_title');
            $table->string('about_image')->nullable()->after('about_description');
            $table->longText('about_page_content')->nullable()->after('about_image');
            $table->json('features_section_json')->nullable()->after('about_page_content');
            $table->json('testimonials_json')->nullable()->after('features_section_json');
            $table->json('cta_section_json')->nullable()->after('testimonials_json');
            $table->string('footer_copyright')->nullable()->after('cta_section_json');
            $table->json('footer_social_links_json')->nullable()->after('footer_copyright');
            $table->json('footer_quick_links_json')->nullable()->after('footer_social_links_json');
            $table->string('footer_logo_url')->nullable()->after('footer_quick_links_json');
            $table->json('social_links_json')->nullable()->after('footer_logo_url');
            $table->text('contact_address')->nullable()->after('social_links_json');
            $table->text('contact_map_embed')->nullable()->after('contact_address');
            $table->longText('contact_page_content')->nullable()->after('contact_map_embed');
            $table->string('seo_title')->nullable()->after('contact_page_content');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_keywords')->nullable()->after('seo_description');
            $table->string('logo_url')->nullable()->after('seo_keywords');
            $table->string('favicon_url')->nullable()->after('logo_url');
            $table->json('faq_json')->nullable()->after('favicon_url');
            $table->longText('terms_content')->nullable()->after('faq_json');
            $table->longText('privacy_content')->nullable()->after('terms_content');
            $table->longText('pricing_page_content')->nullable()->after('privacy_content');
            $table->json('pricing_features_json')->nullable()->after('pricing_page_content');
            $table->json('color_theme_json')->nullable()->after('pricing_features_json');
            $table->json('menu_links_json')->nullable()->after('color_theme_json');
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_cta_url', 'hero_bg_image', 'hero_features_json', 'stats_json',
                'about_title', 'about_description', 'about_image', 'about_page_content',
                'features_section_json', 'testimonials_json', 'cta_section_json',
                'footer_copyright', 'footer_social_links_json', 'footer_quick_links_json',
                'footer_logo_url', 'social_links_json', 'contact_address', 'contact_map_embed',
                'contact_page_content', 'seo_title', 'seo_description', 'seo_keywords',
                'logo_url', 'favicon_url', 'faq_json', 'terms_content', 'privacy_content',
                'pricing_page_content', 'pricing_features_json', 'color_theme_json', 'menu_links_json',
            ]);
        });
    }
};
