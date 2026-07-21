<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->string('pin');
            $table->timestamp('punched_at');
            $table->integer('status')->default(0);
            $table->integer('verify_type')->default(0);
            $table->integer('work_code')->nullable();
            $table->string('reserved_1')->nullable();
            $table->string('reserved_2')->nullable();
            $table->text('raw_data')->nullable();
            $table->timestamps();

            $table->index(['pin', 'punched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
