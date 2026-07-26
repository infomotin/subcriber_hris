<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->string('goal_title');
            $table->text('description')->nullable();
            $table->date('target_date');
            $table->tinyInteger('weightage')->comment('out of 100%');
            $table->enum('status', ['defined', 'ongoing', 'achieved', 'missed'])->default('defined');
            $table->tinyInteger('score_rating')->nullable()->comment('out of 10 after review');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
