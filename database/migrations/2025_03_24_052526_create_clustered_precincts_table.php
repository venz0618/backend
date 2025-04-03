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
        Schema::create('clustered_precincts', function (Blueprint $table) {
            $table->id();
            $table->integer('clustered_precinct_num');
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade');
            $table->integer('clustered_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clustered_precincts');
    }
};
