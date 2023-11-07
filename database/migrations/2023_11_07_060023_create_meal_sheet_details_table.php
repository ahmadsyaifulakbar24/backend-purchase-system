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
        Schema::create('meal_sheet_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meal_sheet_day_id')->constrained('meal_sheet_days')->onUpdate('cascade');
            $table->foreignUuid('client_id')->constrained('clients')->onUpdate('cascade');
            $table->integer('mandays');
            $table->integer('casual_breakfast');
            $table->integer('casual_lunch');
            $table->integer('casual_dinner');
            $table->json('prepared_by');
            $table->json('checked_by');
            $table->json('approved_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_sheet_details');
    }
};
