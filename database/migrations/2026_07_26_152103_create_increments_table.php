<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('increments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_profile_id');
            $table->unsignedBigInteger('increment_rule_id')->nullable();
            $table->enum('increment_type', ['annual', 'special', 'manual', 'bulk']);
            $table->decimal('old_basic', 12, 2)->default(0);
            $table->decimal('old_gross', 12, 2)->default(0);
            $table->decimal('new_basic', 12, 2)->default(0);
            $table->decimal('new_gross', 12, 2)->default(0);
            $table->decimal('increment_amount', 12, 2)->default(0);
            $table->decimal('increment_percentage', 5, 2)->nullable();
            $table->enum('based_on', ['basic', 'gross'])->default('basic');
            $table->enum('status', ['pending', 'enforced', 'cancelled'])->default('pending');
            $table->timestamp('enforced_at')->nullable();
            $table->string('enforced_by')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('employee_profile_id')->references('id')->on('employee_profiles')->cascadeOnDelete();
            $table->foreign('increment_rule_id')->references('id')->on('increment_rules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('increments');
    }
};
