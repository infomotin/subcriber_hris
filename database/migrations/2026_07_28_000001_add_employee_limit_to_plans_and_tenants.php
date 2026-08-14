<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->integer('max_employees')->default(50)->after('max_devices');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->integer('max_employees')->default(50)->after('max_devices');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('max_employees');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('max_employees');
        });
    }
};
