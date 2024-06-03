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
            $table->foreignUuid('meal_sheet_daily_id')->constrained('meal_sheet_daily')->onUpdate('cascade');
            $table->foreignUuid('client_id')->constrained('clients')->onUpdate('cascade');
            $table->foreignUuid('formula_id')->constrained('formulas')->onUpdate('cascade');
            $table->integer('mandays')->nullable();
            $table->integer('casual_breakfast')->nullable();
            $table->integer('casual_lunch')->nullable();
            $table->integer('casual_dinner')->nullable();
            $table->json('prepared_by');
            $table->json('checked_by');
            $table->json('approved_by');
            $table->json('acknowladge_by')->nullable();
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
