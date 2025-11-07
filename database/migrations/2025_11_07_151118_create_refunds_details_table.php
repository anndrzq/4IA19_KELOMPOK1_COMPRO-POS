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
        Schema::create('refunds_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('refund_id');
            $table->foreign('refund_id')->references('id')->on('refunds')->onDelete('cascade');
            $table->string('KdProduct');
            $table->foreign('KdProduct')->references('KdProduct')->on('products')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds_details');
    }
};
