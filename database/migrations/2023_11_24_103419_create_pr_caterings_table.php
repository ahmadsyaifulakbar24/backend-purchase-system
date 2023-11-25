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
        Schema::create('pr_caterings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('serial_number');
            $table->string('pr_number')->unique();
            $table->foreignUuid('location_id')->constrained('locations')->onUpdate('cascade');
            $table->date('request_date');
            $table->date('delivery_date');
            $table->text('description')->nullable();
            $table->foreignUuid('prepared_by')->constrained('users')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_caterings');
    }
};
