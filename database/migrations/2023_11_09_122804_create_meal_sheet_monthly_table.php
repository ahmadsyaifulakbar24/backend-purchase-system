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
        Schema::create('meal_sheet_monthly', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('month');
            $table->integer('year');
            $table->foreignUuid('meal_sheet_group_id')->constrained('meal_sheet_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->json('recap_per_day');
            $table->json('prepared_by');
            $table->json('checked_by');
            $table->json('approved_by');
            $table->json('acknowladge_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_sheet_monthly');
    }
};
