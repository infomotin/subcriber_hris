<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_push_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('endpoint_url');
            $table->string('data_format')->default('json');
            $table->integer('records_count')->default(0);
            $table->integer('status_code')->nullable();
            $table->text('response_body')->nullable();
            $table->boolean('is_success')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_push_logs');
    }
};
