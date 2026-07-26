<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->string('section');
            $table->enum('status', ['pending', 'verified', 'expired'])->default('pending');
            $table->string('verified_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->unique(['employee_profile_id', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_verifications');
    }
};
