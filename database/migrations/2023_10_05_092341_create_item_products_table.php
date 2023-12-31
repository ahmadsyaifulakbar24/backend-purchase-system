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
        Schema::create('item_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignUuid('item_category_id')->constrained('item_categories')->onUpdate('cascade');
            $table->foreignUuid('sub_item_category_id')->nullable()->constrained('item_categories')->onUpdate('cascade');
            $table->string('brand')->nullable();
            $table->string('description')->nullable();
            $table->string('size')->nullable();
            $table->foreignUuid('unit_id')->nullable()->constrained('params')->onUpdate('cascade');
            $table->enum('tax', ['yes', 'no']);
            
            $table->foreignUuid('location_id')->constrained('locations')->onUpdate('cascade');
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onUpdate('cascade');
            $table->decimal('price', 18, 2);
            $table->decimal('sell_price', 18, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_products');
    }
};
