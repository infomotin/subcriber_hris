<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('name')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('port')->default(4370)->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('push_version')->nullable();
            $table->integer('user_count')->default(0);
            $table->integer('fp_count')->default(0);
            $table->integer('face_count')->default(0);
            $table->integer('att_count')->default(0);
            $table->timestamp('last_heartbeat')->nullable();
            $table->string('status')->default('offline');
            $table->string('timezone')->default('UTC');
            $table->boolean('realtime')->default(true);
            $table->integer('delay')->default(30);
            $table->integer('error_delay')->default(60);
            $table->string('trans_times')->nullable();
            $table->integer('trans_interval')->default(1);
            $table->string('trans_flag')->nullable();
            $table->boolean('time_sync')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
