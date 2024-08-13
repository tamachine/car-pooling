<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dropoffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');            
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('journey_id')
                  ->references('id')
                  ->on('journeys');

            $table->index('journey_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dropoffs');
    }
};
