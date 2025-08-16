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
        Schema::create('worklogs', function (Blueprint $table) {
            $table->id();
            $table->date('work_date');
            $table->unsignedBigInteger('entry_type_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('module_id');
            $table->integer('hours');
            $table->integer('minutes');
            $table->text('description');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('entry_type_id')->references('id')->on('entry_types')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Prevent duplicate entries
            $table->unique(['work_date', 'entry_type_id', 'customer_id', 'project_id', 'module_id', 'user_id', 'description'], 'unique_worklog_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worklogs');
    }
};
