<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_processed', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->date('date');
            $table->string('day_name');
            $table->enum('day_type', ['working', 'weekend', 'holiday'])->default('working');
            $table->dateTime('in_time')->nullable();
            $table->dateTime('out_time')->nullable();
            $table->dateTime('scheduled_out_time')->nullable();
            $table->integer('total_seconds')->default(0);
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->integer('late_minutes')->default(0);
            $table->integer('early_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->integer('short_minutes')->default(0);
            $table->boolean('is_late')->default(false);
            $table->boolean('is_early')->default(false);
            $table->enum('status', ['present', 'absent', 'leave', 'holiday', 'weekend', 'missing_punch'])->default('present');
            $table->string('leave_type')->nullable();
            $table->string('shift_name')->nullable();
            $table->integer('punch_count')->default(0);
            $table->string('source')->default('manual');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->unique(['tenant_id', 'employee_profile_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_processed');
    }
};
