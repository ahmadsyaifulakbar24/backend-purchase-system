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
        Schema::create('outgoing_do', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('do_number')->unique();
            $table->foreignUuid('incoming_po_id')->constrained('incoming_po')->onUpdate('cascade');
            $table->foreignUuid('customer_id')->constrained('customers')->onUpdate('cascade');
            $table->text('address');
            $table->date('delivery_date');
            $table->foreignUuid('location_id')->constrained('locations')->onUpdate('cascade');
            $table->foreignUuid('prepared_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('checked_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('approved1_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('approved2_by')->constrained('users')->onUpdate('cascade');
            $table->date('checked_date')->nullable();
            $table->date('approved1_date')->nullable();
            $table->date('approved2_date')->nullable();
            $table->enum('status', ['draft', 'submit', 'reject', 'finish']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_do');
    }
};
