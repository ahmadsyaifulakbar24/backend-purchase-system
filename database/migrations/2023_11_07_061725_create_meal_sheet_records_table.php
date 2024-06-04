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
        Schema::create('meal_sheet_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meal_sheet_detail_id')->constrained('meal_sheet_details')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('company')->nullable();
            $table->boolean('breakfast');
            $table->boolean('lunch');
            $table->boolean('dinner');
            $table->boolean('supper');
            $table->boolean('accomodation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_sheet_records');
    }
};
