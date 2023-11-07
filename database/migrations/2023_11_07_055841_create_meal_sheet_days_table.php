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
        Schema::create('meal_sheet_days', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('meal_sheet_group_id')->constrained('meal_sheet_groups');
            $table->date('meal_sheet_date');
            $table->integer('contract_value');
            $table->enum('status', ['lock', 'unlock']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_sheet_days');
    }
};
