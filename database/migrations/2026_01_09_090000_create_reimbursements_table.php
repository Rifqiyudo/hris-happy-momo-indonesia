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
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Karyawan yang mengajukan klaim)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // [UPDATE] Kategori Klaim (Medis, Transportasi, Operasional, dll)
            // Ini penting untuk filter laporan keuangan HRD
            $table->string('category')->default('Umum'); 
            
            $table->string('item_name'); // Nama Barang/Kegiatan (Contoh: Bensin Surabaya-Sidoarjo)
            $table->date('date');        // Tanggal sesuai yang tertera di struk/nota
            
            /**
             * [DETAIL] Nominal Uang
             * Menggunakan decimal(15,2) agar mendukung angka hingga triliunan 
             * dan presisi untuk perhitungan keuangan.
             */
            $table->decimal('amount', 15, 2); 
            
            $table->string('proof_file')->nullable(); // Path foto struk/nota di storage
            $table->text('description')->nullable();  // Alasan atau detail tambahan
            
            // Status Alur Persetujuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Catatan dari Admin/Finance (Kenapa ditolak atau instruksi pembayaran)
            $table->text('admin_note')->nullable();
            
            // [UPDATE] Tanggal disetujui/ditolak untuk audit trail
            $table->timestamp('action_at')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};