<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->unsignedBigInteger('old_department_id');
            $table->unsignedBigInteger('new_department_id');
            $table->unsignedBigInteger('old_designation_id');
            $table->unsignedBigInteger('new_designation_id');
            $table->string('promotion_type');
            $table->text('notes')->nullable();
            $table->date('effective_date');
            $table->string('status')->default('active');
            $table->string('reference_number')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->foreign('old_department_id')->references('id')->on('departments');
            $table->foreign('new_department_id')->references('id')->on('departments');
            $table->foreign('old_designation_id')->references('id')->on('designations');
            $table->foreign('new_designation_id')->references('id')->on('designations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_promotions');
    }
};
