<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->decimal('basic_percent', 5, 2)->default(50.00)->comment('Percentage of Gross');
            $table->decimal('house_rent_percent', 5, 2)->default(25.00)->comment('Percentage of Gross');
            $table->decimal('medical_percent', 5, 2)->default(10.00)->comment('Percentage of Gross');
            $table->decimal('tada_percent', 5, 2)->default(15.00)->comment('Percentage of Gross (TA/DA)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_relations');
    }
};
