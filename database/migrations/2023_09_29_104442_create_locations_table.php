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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('location_code')->unique();
            $table->string('location');
            $table->uuid('parent_location_id')->nullable();
            $table->boolean('main')->default('0');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign('parent_location_id')->references('id')->on('locations')->onUpdate('cascade');    
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
