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
        Schema::create('mor_month_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mor_month_id')->constrained('mor_months')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('item_product_id')->constrained('item_products')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('price');
            $table->bigInteger('last_stock');
            $table->bigInteger('actual_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mor_month_details');
    }
};
