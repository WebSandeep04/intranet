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
       Schema::create('cities', function (Blueprint $table) {
        $table->id(); // id INT AUTO_INCREMENT PRIMARY KEY
        $table->unsignedBigInteger('state_id'); // state_id INT NOT NULL
        $table->string('city_name'); // city_name VARCHAR(255) NOT NULL
        $table->unsignedBigInteger('tenant_id')->nullable();
        $table->timestamps();

        // Optional: Add foreign key if states table exists
        $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
