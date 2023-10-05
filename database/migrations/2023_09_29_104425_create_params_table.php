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
        Schema::create('params', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('category');
            $table->string('param');
            $table->string('slug')->nullable();
            $table->integer('order')->nullable();
        });

        Schema::table('params', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('params')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('params');
    }
};
