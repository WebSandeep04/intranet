<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add tenant_id columns without foreign key constraints
        Schema::table('attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('user_id');
        });

        Schema::table('movements', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('attendance_id');
        });

        // Step 2: Populate tenant_id values from users table
        DB::statement('
            UPDATE attendance a 
            INNER JOIN users u ON a.user_id = u.id 
            SET a.tenant_id = u.tenant_id 
            WHERE a.tenant_id IS NULL
        ');

        DB::statement('
            UPDATE movements m 
            INNER JOIN attendance a ON m.attendance_id = a.id 
            SET m.tenant_id = a.tenant_id 
            WHERE m.tenant_id IS NULL
        ');

        // Step 3: Make tenant_id NOT NULL
        Schema::table('attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
        });

        Schema::table('movements', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
        });

        // Step 4: Add foreign key constraints
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::table('movements', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
