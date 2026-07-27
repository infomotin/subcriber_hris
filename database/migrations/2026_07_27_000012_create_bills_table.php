<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->unsignedBigInteger('bill_type_id');
            $table->unsignedBigInteger('bill_purpose_id');
            $table->decimal('amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable()->comment('Amount after modification if any');
            $table->string('bill_no', 50)->nullable();
            $table->string('voucher_path', 500)->nullable()->comment('Uploaded voucher file path');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'modified'])->default('pending');
            $table->unsignedBigInteger('actioned_by')->nullable();
            $table->text('action_remarks')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->foreign('bill_type_id')->references('id')->on('bill_types')->cascadeOnDelete();
            $table->foreign('bill_purpose_id')->references('id')->on('bill_purposes')->cascadeOnDelete();
            $table->foreign('actioned_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
