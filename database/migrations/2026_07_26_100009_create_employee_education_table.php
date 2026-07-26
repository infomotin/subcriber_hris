<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->string('degree_name'); // e.g. B.Sc. in CS, PMP
            $table->string('institution');
            $table->string('passing_year', 4);
            $table->string('result')->comment('CGPA or Grade');
            $table->string('certification_type')->nullable()->comment('education or training');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_education');
    }
};
