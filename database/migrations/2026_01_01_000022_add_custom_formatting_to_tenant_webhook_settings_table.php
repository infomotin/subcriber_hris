<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_webhook_settings', function (Blueprint $table) {
            $table->string('date_format')->default('Y-m-d H:i:s')->after('data_format');
            $table->json('custom_mapping')->nullable()->after('date_format');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_webhook_settings', function (Blueprint $table) {
            $table->dropColumn(['date_format', 'custom_mapping']);
        });
    }
};
