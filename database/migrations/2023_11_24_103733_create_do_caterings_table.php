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
        Schema::create('do_caterings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('created_by')->constrained('users')->onUpdate('cascade');
            $table->foreignUuid('po_supplier_catering_id')->constrained('po_supplier_caterings')->onUpdate('cascade');
            $table->bigInteger('serial_number');
            $table->string('do_number')->unique();
            $table->enum('status', ['draft', 'submit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('do_caterings');
    }
};
