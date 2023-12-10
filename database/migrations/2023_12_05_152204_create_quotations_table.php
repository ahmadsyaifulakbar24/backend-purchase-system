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
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('serial_number');
            $table->string('quotation_number')->unique();
            $table->foreignUuid('pr_customer_id')->unique()->constrained('pr_customers')->onUpdate('cascade');
            $table->foreignUuid('customer_id')->constrained('customers')->onUpdate('cascade');
            $table->string('vessel');
            $table->string('shipping_address');
            $table->integer('mark_up');
            $table->foreignUuid('prepared_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('checked_by')->constrained('users')->onUpdate('cascade');
            $table->text('term_condition');
            $table->date('checked_date')->nullable();
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
        Schema::dropIfExists('quotations');
    }
};
