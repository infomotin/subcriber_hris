<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_webhook_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('endpoint_url')->nullable();
            $table->string('push_schedule')->default('realtime'); // realtime, hourly, daily, manual
            $table->string('data_format')->default('json'); // json, csv, excel
            $table->string('auth_type')->default('none'); // none, bearer, api_key, basic
            $table->string('auth_header_name')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('auth_username')->nullable();
            $table->string('auth_password')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamp('last_pushed_at')->nullable();
            $table->integer('last_status_code')->nullable();
            $table->text('last_response_body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_webhook_settings');
    }
};
