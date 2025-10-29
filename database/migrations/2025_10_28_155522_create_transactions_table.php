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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('invoice_number')->primary()->unique();
            $table->dateTime('transaction_date');
            $table->enum('type_transaction', ['member', 'umum', 'grosir'])->default('umum');

            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'debit'])->default('cash');
            $table->enum('payment_provider', ['bca', 'lain'])->nullable();

            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['paid', 'unpaid', 'cancelled'])->default('paid');

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('membership_id')->nullable();
            $table->foreign('membership_id')->references('id')->on('members')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
