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
        Schema::create('meal_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->double('manday', 15, 2);
            $table->double('breakfast', 15, 2);
            $table->double('lunch', 15, 2);
            $table->double('dinner', 15, 2);
            $table->double('supper', 15, 2);
            $table->double('hk', 15, 2);
            $table->integer('minimum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_rates');
    }
};
