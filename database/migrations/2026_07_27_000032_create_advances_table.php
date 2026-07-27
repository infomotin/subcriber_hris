<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->unsignedBigInteger('advance_type_id');
            $table->unsignedBigInteger('advance_source_id');
            $table->unsignedBigInteger('reference_employee_id')->nullable()->comment('Reference employee');
            $table->decimal('amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->unsignedInteger('installments')->default(1)->comment('Number of monthly installments');
            $table->decimal('monthly_deduction', 12, 2)->nullable()->comment('Amount deducted per month');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('actioned_by')->nullable();
            $table->text('action_remarks')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->foreign('advance_type_id')->references('id')->on('advance_types')->cascadeOnDelete();
            $table->foreign('advance_source_id')->references('id')->on('advance_sources')->cascadeOnDelete();
            $table->foreign('reference_employee_id')->references('id')->on('employee_profiles')->nullOnDelete();
            $table->foreign('actioned_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advances');
    }
};
