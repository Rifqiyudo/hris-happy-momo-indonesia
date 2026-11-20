<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users, jika user dihapus, dokumen ikut terhapus (cascade)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Kategori berkas: KTP, Ijazah, Kontrak Kerja, SK, dll
            $table->string('type'); 
            
            // Lokasi penyimpanan file di server (misal: documents/nik/file.pdf)
            $table->string('file_path');
            
            // Nama asli file saat diupload
            $table->string('file_name');
            
            // Ukuran file dalam KB (opsional, berguna untuk monitoring storage)
            $table->integer('file_size')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};