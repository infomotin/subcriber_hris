<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('form_token', 64)->unique();
            $table->unsignedSmallInteger('step')->default(1);
            $table->json('step_data');
            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'form_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_drafts');
    }
};
