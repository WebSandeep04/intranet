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
       Schema::create('prospectuses', function (Blueprint $table) {
    $table->id();
    $table->string('prospectus_name');
    $table->string('contact_person', 100)->nullable();
    $table->string('contact_number', 20)->nullable();
    $table->text('address')->nullable();
    $table->unsignedBigInteger('state_id')->nullable();
    $table->unsignedBigInteger('city_id')->nullable();
    $table->string('email', 100)->nullable();
    $table->unsignedBigInteger('business_type_id')->nullable();
    $table->unsignedBigInteger('tenant_id')->nullable();

    $table->timestamps(); // ✅ Adds created_at and updated_at

    // Foreign keys
    $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
    $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
    $table->foreign('business_type_id')->references('id')->on('sales_business_types')->onDelete('set null');
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospectuses');
    }
};


