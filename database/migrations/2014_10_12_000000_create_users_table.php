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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phoneNumber')->unique();
            $table->string('email')->unique();
            $table->string('address');
            $table->string('password');
            $table->enum('role', ['Admin', 'SuperAdmin', 'Kasir']);
            $table->enum('gender', ['Laki', 'Perempuan']);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
