<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_informations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id')->unique();
            $table->string('bank_name');
            $table->string('branch_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->enum('payment_mode', ['bank_transfer', 'cash', 'cheque', 'mobile_banking'])->default('bank_transfer');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_informations');
    }
};
