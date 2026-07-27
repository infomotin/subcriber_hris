<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_verifications', function (Blueprint $table) {
            $table->string('verification_method')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('employee_verifications', function (Blueprint $table) {
            $table->dropColumn('verification_method');
        });
    }
};
