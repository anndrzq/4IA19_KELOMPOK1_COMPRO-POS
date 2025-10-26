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
        Schema::create('products', function (Blueprint $table) {
            $table->string('KdProduct')->unique()->primary();
            $table->string('Photo')->nullable();
            $table->string('nameProduct')->unique();
            $table->integer('stock')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('KdCategory');
            $table->foreign('KdCategory')->references('KdCategory')->on('categories')->onDelete('cascade');
            $table->string('KdUnit');
            $table->foreign('KdUnit')->references('KdUnit')->on('units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
