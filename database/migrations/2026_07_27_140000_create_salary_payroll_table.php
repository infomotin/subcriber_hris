<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_payroll', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->string('year_month', 7);
            $table->unsignedBigInteger('salary_role_id')->nullable();

            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('house_rent', 12, 2)->default(0);
            $table->decimal('medical', 12, 2)->default(0);
            $table->decimal('conveyance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);

            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holiday_days')->default(0);
            $table->integer('weekend_days')->default(0);
            $table->integer('working_days')->default(0);

            $table->integer('total_late_minutes')->default(0);
            $table->integer('total_ot_minutes')->default(0);
            $table->integer('total_early_minutes')->default(0);

            $table->decimal('daily_rate', 12, 2)->default(0);
            $table->decimal('per_minute_rate', 12, 4)->default(0);

            $table->decimal('late_deduction', 12, 2)->default(0);
            $table->decimal('absent_deduction', 12, 2)->default(0);
            $table->decimal('ot_payable', 12, 2)->default(0);
            $table->decimal('advance_deduction', 12, 2)->default(0);
            $table->decimal('tax_deduction', 12, 2)->default(0);
            $table->decimal('pf_deduction', 12, 2)->default(0);

            $table->decimal('net_payable', 12, 2)->default(0);

            $table->enum('status', ['generated', 'approved', 'paid'])->default('generated');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->foreign('salary_role_id')->references('id')->on('salary_relations')->nullOnDelete();
            $table->unique(['tenant_id', 'employee_profile_id', 'year_month'], 'salary_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payroll');
    }
};
