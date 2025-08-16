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
       Schema::create('sales_business_types', function (Blueprint $table) {
        $table->id(); // auto-incrementing ID
        $table->string('business_name')->nullable(); // equivalent to varchar(255) DEFAULT NULL
        $table->unsignedBigInteger('tenant_id')->nullable();
        $table->timestamps(); // created_at and updated_at columns
        
        // Foreign key constraint
        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_business_types');
    }
};
