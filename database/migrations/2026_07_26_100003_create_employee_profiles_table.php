<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id'); // Link to login user
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->string('employee_id')->comment('Formatted ID code');
            $table->date('joining_date');
            $table->string('gender', 20);
            $table->date('dob');
            $table->string('phone_number');
            $table->string('blood_group', 5)->nullable();
            $table->enum('status', ['active', 'probation', 'terminated', 'resigned'])->default('probation');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('designation_id')->references('id')->on('designations')->nullOnDelete();
            $table->unique(['tenant_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
