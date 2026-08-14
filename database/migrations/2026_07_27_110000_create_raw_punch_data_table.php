<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_punch_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id')->nullable();
            $table->string('employee_id');
            $table->string('punch_machine_serial')->nullable();
            $table->dateTime('punch_date_time');
            $table->string('status')->nullable();
            $table->string('verify_type')->nullable();
            $table->string('source_file')->nullable();
            $table->boolean('is_matched')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->nullOnDelete();
            $table->index(['tenant_id', 'employee_id']);
            $table->index(['tenant_id', 'punch_date_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_punch_data');
    }
};
