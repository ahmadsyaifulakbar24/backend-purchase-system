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
        Schema::create('po_supplier_caterings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('po_catering_id')->constrained('po_caterings')->onUpdate('cascade');
            $table->bigInteger('serial_number');
            $table->string('po_number')->unique();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onUpdate('cascade');
            $table->foreignUuid('discount_id')->constrained('discounts')->onUpdate('cascade');
            $table->text('term_condition');
            $table->enum('status', ['draft', 'submit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_supplier_caterings');
    }
};
