<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tenant_token', 32)->unique(); // Custom token for ZKTeco machine endpoints
            $table->foreignId('subscription_plan_id')->nullable();
            $table->string('status')->default('active'); // active, expired, suspended
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_devices')->default(2);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
