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
        Schema::create('po_caterings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pr_catering_id')->constrained('pr_caterings')->onUpdate('cascade');
            $table->bigInteger('serial_number');
            $table->string('po_number')->unique();
            $table->foreignUuid('discount_id')->constrained('discounts')->onUpdate('cascade');
            $table->text('term_condition');
            $table->text('term_payment');
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
        Schema::dropIfExists('po_caterings');
    }
};
