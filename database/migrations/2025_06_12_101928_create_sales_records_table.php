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
        Schema::create('sales_records', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key as BIGINT UNSIGNED

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('leads_name');
            $table->string('contact_person');
            $table->string('contact_number', 50);
            $table->text('address');
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('email');
            $table->unsignedBigInteger('business_type_id')->nullable();
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            // $table->longText('remark')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->unsignedBigInteger('products_id')->nullable();
            $table->unsignedBigInteger('prospectus_id')->nullable();
            $table->dateTime('updatedat')->nullable();
            $table->string('update_remark')->nullable();
            $table->string('status_update_remark')->nullable();
            $table->dateTime('status_updatedat')->nullable();
            $table->date('createdat')->nullable();
            $table->bigInteger('ticket_value')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('business_type_id')->references('id')->on('sales_business_types')->onDelete('set null');
            $table->foreign('lead_source_id')->references('id')->on('sales_lead_sources')->onDelete('set null');
            $table->foreign('status_id')->references('id')->on('sales_status')->onDelete('set null');
            $table->foreign('products_id')->references('id')->on('sales_products')->onDelete('set null');
            $table->foreign('prospectus_id')->references('id')->on('prospectuses')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_records');
    }
};
