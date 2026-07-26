<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('nid', 30)->nullable()->after('blood_group');
            $table->string('birth_certificate', 30)->nullable()->after('nid');
            $table->string('religion', 50)->nullable()->after('birth_certificate');
            $table->string('marital_status', 20)->nullable()->after('religion');
            $table->string('father_name', 100)->nullable()->after('marital_status');
            $table->string('father_occupation', 100)->nullable()->after('father_name');
            $table->string('mother_name', 100)->nullable()->after('father_occupation');
            $table->string('mother_occupation', 100)->nullable()->after('mother_name');
            $table->string('guardian_name', 100)->nullable()->after('mother_occupation');
            $table->string('guardian_relation', 50)->nullable()->after('guardian_name');
            $table->string('guardian_phone', 20)->nullable()->after('guardian_relation');
        });
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'nid', 'birth_certificate', 'religion', 'marital_status',
                'father_name', 'father_occupation', 'mother_name', 'mother_occupation',
                'guardian_name', 'guardian_relation', 'guardian_phone'
            ]);
        });
    }
};
