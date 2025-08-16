<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
        $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY
        $table->string('state_name'); // state_name VARCHAR(255) NOT NULL
        $table->unsignedBigInteger('tenant_id')->nullable();
        $table->timestamps();
        
        // Foreign key constraint
        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
