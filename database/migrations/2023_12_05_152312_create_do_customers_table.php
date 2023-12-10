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
        Schema::create('do_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('po_customer_id')->unique()->constrained('po_customers')->onUpdate('cascade');
            $table->bigInteger('serial_number');
            $table->string('do_number')->unique();
            $table->foreignUuid('approved_by')->constrained('users')->onUpdate('cascade');
            $table->date('approved_date')->nullable();
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
        Schema::dropIfExists('do_customers');
    }
};
