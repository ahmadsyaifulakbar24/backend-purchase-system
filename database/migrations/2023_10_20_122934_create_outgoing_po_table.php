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
        Schema::create('outgoing_po', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('serial_number');
            $table->string('po_number')->unique();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onUpdate('cascade');
            $table->string('attn_name');
            $table->date('request_date');
            $table->date('delivery_date');       
            $table->foreignUuid('location_id')->constrained('locations')->onUpdate('cascade');
            $table->foreignUuid('discount_id')->constrained('discounts')->onUpdate('cascade');
            $table->text('term_condition');
            $table->foreignUuid('prepared_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('checked_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('approved1_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('approved2_by')->constrained('users')->onUpdate('cascade');
            $table->date('checked_date')->nullable();
            $table->date('approved1_date')->nullable();
            $table->date('approved2_date')->nullable();
            $table->enum('status', ['draft', 'submit', 'reject', 'finish']);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_po');
    }
};
