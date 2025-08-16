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
        // Update existing pending leaves to approved
        DB::table('leaves')->where('status', 'pending')->update(['status' => 'approved']);
        
        // Change the default value for new records
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the default value back to pending
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
        
        // Note: We don't revert existing approved leaves back to pending as this could cause data loss
    }
};
