<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('positions', function (Blueprint $table) {
            $table->string('level')->after('position_type')->default('local'); // Default to local
        });
    }

    public function down(): void {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
