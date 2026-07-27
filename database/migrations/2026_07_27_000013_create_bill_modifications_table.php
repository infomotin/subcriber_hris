<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_modifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->decimal('original_amount', 12, 2);
            $table->decimal('new_amount', 12, 2);
            $table->text('reason');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')->cascadeOnDelete();
            $table->foreign('modified_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_modifications');
    }
};
