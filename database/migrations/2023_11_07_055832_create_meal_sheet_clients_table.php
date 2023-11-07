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
        Schema::create('meal_sheet_clients', function (Blueprint $table) {
            $table->foreignUuid('meal_sheet_group_id')->constrained('meal_sheet_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('client_id')->constrained('clients')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_sheet_clients');
    }
};
