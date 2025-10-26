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
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('KdProduct');
            $table->foreign('KdProduct')->references('KdProduct')->on('products')->onDelete('cascade');
            $table->string('KdSuppliers');
            $table->foreign('KdSuppliers')->references('kdSuppliers')->on('supliers')->onDelete('cascade');
            $table->string('batch_code')->unique();
            $table->integer('quantity');
            $table->date('expired_date')->nullable();
            $table->string('qr_code')->nullable();
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('markup_percentage', 5, 2)->default(0);
            $table->decimal('final_price', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
