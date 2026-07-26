<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('increment_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->date('joining_date_from')->nullable();
            $table->date('joining_date_to')->nullable();
            $table->enum('increment_based_on', ['basic', 'gross'])->default('basic');
            $table->date('year_start_date')->nullable();
            $table->decimal('special_max_percentage', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('increment_rules');
    }
};
