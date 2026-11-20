<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah tipe kolom status dan subject agar tidak terjadi data truncation.
     */
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Mengubah kolom status menjadi string (VARCHAR 255)
            // Ini akan menampung nilai seperti 'active', 'pending', 'closed' dengan sangat aman.
            $table->string('status')->default('active')->change();
            
            // Mengubah kolom subject agar lebih fleksibel
            $table->string('subject')->default('Percakapan Internal')->change();
        });
    }

    /**
     * Reverse the migrations.
     * Mengembalikan struktur kolom ke tipe sebelumnya jika diperlukan.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Jika ingin revert, Anda harus tahu tipe data sebelumnya.
            // Contoh jika sebelumnya adalah char(3):
            // $table->char('status', 3)->change();
            // $table->string('subject', 50)->change();
        });
    }
};