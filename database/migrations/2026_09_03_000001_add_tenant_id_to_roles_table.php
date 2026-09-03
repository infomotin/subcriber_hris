<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->default(0)->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
        });

        // Mark existing roles as system roles (tenant_id = 0)
        DB::table('roles')->whereNull('tenant_id')->update(['tenant_id' => 0]);

        // Drop old unique constraint and add tenant-aware one
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']);
            $table->unique(['tenant_id', 'name', 'guard_name']);
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'name', 'guard_name']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['name', 'guard_name']);
        });
    }
};
