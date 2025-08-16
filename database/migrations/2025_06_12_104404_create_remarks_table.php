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
         Schema::create('remarks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->date('remark_date');
            $table->text('remark');
            $table->unsignedBigInteger('sales_remark_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();

            // Foreign key constraint
            $table->foreign('sales_remark_id')
                ->references('id')->on('sales_records')
                ->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remarks');
    }
};
