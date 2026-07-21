<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_webhook_settings', function (Blueprint $table) {
            $table->string('scheduled_time', 5)->default('23:00')->after('push_schedule');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_webhook_settings', function (Blueprint $table) {
            $table->dropColumn('scheduled_time');
        });
    }
};
