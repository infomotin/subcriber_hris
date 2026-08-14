<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonus_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('salary_role_id');
            $table->enum('calculation_type', ['basic_half', 'gross_1_5x', 'basic_percent', 'gross_percent', 'fixed_amount']);
            $table->decimal('calculation_value', 12, 2)->default(0)->comment('percent or fixed amount');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('salary_role_id')->references('id')->on('salary_relations')->cascadeOnDelete();
            $table->unique(['tenant_id', 'salary_role_id']);
        });

        Schema::create('bonus_eligibility_slabs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_config_id');
            $table->integer('min_months')->default(0);
            $table->integer('max_months')->nullable()->comment('null = unlimited');
            $table->decimal('percent_of_bonus', 5, 2)->default(0)->comment('0-100');
            $table->timestamps();

            $table->foreign('bonus_config_id')->references('id')->on('bonus_configs')->cascadeOnDelete();
        });

        Schema::table('salary_payroll', function (Blueprint $table) {
            $table->decimal('bonus_amount', 12, 2)->default(0)->after('ot_payable');
            $table->decimal('bonus_eligible_percent', 5, 2)->default(0)->after('bonus_amount');
            $table->integer('tenure_months')->default(0)->after('bonus_eligible_percent');
        });
    }

    public function down(): void
    {
        Schema::table('salary_payroll', function (Blueprint $table) {
            $table->dropColumn(['bonus_amount', 'bonus_eligible_percent', 'tenure_months']);
        });

        Schema::dropIfExists('bonus_eligibility_slabs');
        Schema::dropIfExists('bonus_configs');
    }
};
