<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movement_monthly_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('movement_type_id');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->unsignedInteger('max_allowed')->default(3);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('movement_type_id')->references('id')->on('movement_types')->cascadeOnDelete();
            $table->unique(['tenant_id', 'movement_type_id', 'month', 'year'], 'movlimits_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movement_monthly_limits');
    }
};
