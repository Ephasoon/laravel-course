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
        Schema::create('favourite_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'car_id')->constrained(table: 'cars');
            $table->foreignId(column: 'user_id')->constrained(table: 'users');
            $table->unique(['user_id', 'car_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourite_cars');
    }
};
