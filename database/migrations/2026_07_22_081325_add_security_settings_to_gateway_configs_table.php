<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gateway_configs', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('sslcommerz_is_sandbox');
            $table->boolean('captcha_enabled')->default(false)->after('two_factor_enabled');
            $table->string('captcha_site_key')->nullable()->after('captcha_enabled');
            $table->string('captcha_secret_key')->nullable()->after('captcha_site_key');
            $table->boolean('honeypot_enabled')->default(false)->after('captcha_secret_key');
        });
    }

    public function down(): void
    {
        Schema::table('gateway_configs', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled',
                'captcha_enabled',
                'captcha_site_key',
                'captcha_secret_key',
                'honeypot_enabled',
            ]);
        });
    }
};
