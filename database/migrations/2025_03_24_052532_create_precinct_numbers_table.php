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
        Schema::create('precinct_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('precinct_num'); // Changed from integer to string
            $table->foreignId('clustered_precinct_id')->constrained('clustered_precincts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precinct_numbers');
    }
};
