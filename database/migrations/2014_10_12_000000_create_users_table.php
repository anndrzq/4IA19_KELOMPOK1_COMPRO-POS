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
            $table->id();
            $table->string('name');
            $table->string('phoneNumber')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Admin', 'SuperAdmin', 'Kasir']);
            $table->enum('jk', ['Laki', 'Perempuan']);
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
