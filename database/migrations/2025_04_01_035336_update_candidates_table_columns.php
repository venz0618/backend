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
    Schema::table('candidates', function (Blueprint $table) {
        if (!Schema::hasColumn('candidates', 'position_id')) {
            $table->unsignedBigInteger('position_id')->nullable();
        }
        if (!Schema::hasColumn('candidates', 'province_id')) {
            $table->unsignedBigInteger('province_id')->nullable();
        }
        if (!Schema::hasColumn('candidates', 'city_id')) {
            $table->unsignedBigInteger('city_id')->nullable();
        }
        if (!Schema::hasColumn('candidates', 'district_id')) {
            $table->unsignedBigInteger('district_id')->nullable();
        }
        if (!Schema::hasColumn('candidates', 'barangay_id')) {
            $table->unsignedBigInteger('barangay_id')->nullable();
        }
    });
}


public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropUnique(['candidate_name']);
        $table->dropForeign(['position_id']);
        $table->dropForeign(['province_id']);
        $table->dropForeign(['city_id']);
        $table->dropForeign(['district_id']);
        $table->dropForeign(['barangay_id']);
    });
}

};
