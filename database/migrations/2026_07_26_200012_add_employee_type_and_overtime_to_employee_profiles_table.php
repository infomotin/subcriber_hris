<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('employee_type', 20)->nullable()->after('status')->comment('worker, staff, manager');
            $table->boolean('overtime_eligible')->default(false)->after('employee_type');
            $table->decimal('overtime_rate', 10, 2)->nullable()->after('overtime_eligible')->comment('BDT per hour');
        });
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['employee_type', 'overtime_eligible', 'overtime_rate']);
        });
    }
};
