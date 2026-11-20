<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengatur default value untuk branch_id agar tidak NULL.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolom branch_id ada, lalu set default ke 1 (PT. Happy Momo)
            // Menggunakan change() membutuhkan paket doctrine/dbal jika Laravel < 10
            $table->unsignedBigInteger('branch_id')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     * Mengembalikan kolom ke kondisi nullable tanpa default.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->default(null)->change();
        });
    }
};