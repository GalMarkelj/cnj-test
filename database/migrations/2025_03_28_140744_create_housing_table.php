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
        Schema::create('housing', function (Blueprint $table) {
            $table->ulid('uuid')->primary();
            $table->string('date');
            $table->string('area');
            $table->unsignedBigInteger('average_price');
            $table->string('code');
            $table->integer('houses_sold')->nullable();
            $table->float('no_of_crimes')->nullable();
            $table->integer('borough_flag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housing');
    }
};
