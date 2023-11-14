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
        Schema::create('mors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('location_id')->constrained('locations')->onUpdate('cascade');
            $table->foreignUuid('item_product_id')->constrained('item_products')->onUpdate('cascade');
            $table->date('date');
            $table->bigInteger('quantity');
            $table->decimal('item_price', 16, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mors');
    }
};
