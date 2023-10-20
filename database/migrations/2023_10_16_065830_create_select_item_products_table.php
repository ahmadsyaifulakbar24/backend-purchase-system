<?php

use Illuminate\Console\View\Components\Task;
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
        Schema::create('select_item_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_type');
            $table->string('reference_id');
            $table->foreignUuid('item_product_id')->constrained('item_products')->onUpdate('cascade');
            $table->string('description')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('quantity');
            $table->decimal('item_price', 16, 2);
            $table->integer('vat');
            $table->enum('tnt', ['T', 'NT'])->nullable();
            $table->text('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('select_item_products');
    }
};
