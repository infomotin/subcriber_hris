<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id')->unique();
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('house_rent', 12, 2)->default(0.00);
            $table->decimal('medical_allowance', 12, 2)->default(0.00);
            $table->decimal('conveyance_allowance', 12, 2)->default(0.00);
            $table->decimal('other_allowances', 12, 2)->default(0.00);
            $table->decimal('provident_fund_deduction', 12, 2)->default(0.00);
            $table->decimal('tax_deduction', 12, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
