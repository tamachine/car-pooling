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
        Schema::create('journeys', function (Blueprint $table) {
            $table->id();
            $table->integer('people');
            $table->unsignedBigInteger('car_id')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('car_id')
                  ->references('id')
                  ->on('cars');                 

            $table->index('car_id'); // Index to speed up queries involving car_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journeys');
    }
};
