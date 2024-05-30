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
            $table->foreignUuid('location_id')->constrained('locations')->onUpdate('cascade');
            $table->double('manday', 8, 2);
            $table->double('breakfast', 8, 2);
            $table->double('lunch', 8, 2);
            $table->double('dinner', 8, 2);
            $table->double('supper', 8, 2);
            $table->double('hk', 8, 2);
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
