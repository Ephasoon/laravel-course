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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'maker_id')->constrained(table: 'makers');
            $table->foreignId(column: 'model_id')->constrained(table: 'models');
            $table->integer(column: 'year');
            $table->integer(column: 'price');
            $table->string(column: 'vin', length:255);
            $table->integer(column: 'mileage');
            $table->foreignId(column: 'car_type_id')->constrained(table: 'car_types');
            $table->foreignId(column: 'fuel_type_id')->constrained(table: 'fuel_types');
            $table->foreignId(column: 'user_id')->constrained(table: 'users');
            $table->foreignId(column: 'city_id')->constrained(table: 'cities');
            $table->string(column: 'address', length:255);
            $table->string(column: 'phone', length:45);
            $table->longText(column: 'description')->nullable();
            $table->timestamp(column: 'published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('year');
            $table->index('price');
            $table->index('mileage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
