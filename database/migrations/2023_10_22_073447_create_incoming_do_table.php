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
        Schema::create('incoming_do', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('do_number')->unique();
            $table->foreignUuid('supplier_id')->constrained('suppliers')->onUpdate('cascade');
            $table->date('delivery_date');
            $table->date('received_date');
            $table->string('total');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_do');
    }
};
