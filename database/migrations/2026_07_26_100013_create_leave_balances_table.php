<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->year('calendar_year');
            $table->decimal('allocated_days', 5, 2);
            $table->decimal('spent_days', 5, 2)->default(0.00);
            $table->decimal('earned_days', 5, 2)->default(0.00)->comment('Leave days earned via encashment rules');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->cascadeOnDelete();
            $table->unique(['tenant_id', 'employee_profile_id', 'leave_type_id', 'calendar_year'], 'unique_balance_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
