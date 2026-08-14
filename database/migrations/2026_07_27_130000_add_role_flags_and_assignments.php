<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_relations', function (Blueprint $table) {
            $table->boolean('is_ot_payable')->default(true)->after('is_active');
            $table->boolean('is_late_deduction')->default(false)->after('is_ot_payable');
            $table->boolean('single_punch_full_day')->default(false)->after('is_late_deduction');
        });

        Schema::create('salary_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('salary_role_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('applicable_month', 7);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('salary_role_id')->references('id')->on('salary_relations')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->unique(['tenant_id', 'salary_role_id', 'department_id', 'applicable_month'], 'role_dept_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_role_assignments');

        Schema::table('salary_relations', function (Blueprint $table) {
            $table->dropColumn(['is_ot_payable', 'is_late_deduction', 'single_punch_full_day']);
        });
    }
};
