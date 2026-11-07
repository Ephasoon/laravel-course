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
        Schema::create('car_features', function (Blueprint $table) {
            $table->unsignedBigInteger( column: 'car_id')->primary();
            $table->boolean( column: 'abs')->default( value: 0);
            $table->boolean( column: 'air_conditioning')->default( value: 0);
            $table->boolean( column: 'power_windows')->default( value: 0);
            $table->boolean( column: 'power_door_locks')->default( value: 0);
            $table->boolean( column: 'cruise_control')->default( value: 0);
            $table->boolean( column: 'bluetooth_connectivity')->default( value: 0);
            $table->boolean( column: 'remote_start')->default( value: 0);
            $table->boolean( column: 'gps_navigation')->default( value: 0);
            $table->boolean( column: 'heated_seats')->default( value: 0);
            $table->boolean( column: 'climate_control')->default( value: 0);
            $table->boolean( column: 'rear_parking_sensors')->default( value: 0);
            $table->boolean( column: 'leather_seats')->default( value: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_features');
    }
};
