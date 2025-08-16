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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->enum('movement_type', ['office', 'field', 'break']);
            $table->enum('movement_action', ['in', 'out', 'start', 'end']);
            $table->datetime('time');
            $table->string('description')->nullable();
            $table->timestamps();
            
            $table->foreign('attendance_id')->references('id')->on('attendance')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
