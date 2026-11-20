<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel manajemen aset untuk inventaris yang dibawa karyawan.
     */
    public function up(): void
    {
        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi ke tabel users (Karyawan)
            // Menggunakan set null agar jika karyawan resign/hapus, aset tetap tercatat di database perusahaan
            $table->foreignId('user_id')
                  ->nullable() 
                  ->constrained()
                  ->onDelete('set null')
                  ->comment('Karyawan pemegang aset');

            // 2. Detail Identitas Aset
            $table->string('asset_name')->comment('Contoh: Laptop, Motor, HP');
            $table->string('asset_brand')->nullable()->comment('Contoh: Apple, Honda');
            $table->string('asset_serial_number')->unique()->nullable()->comment('S/N atau Nomor Mesin');
            
            // 3. Kategori & Kondisi
            $table->enum('category', ['electronics', 'vehicle', 'furniture', 'others'])->default('electronics');
            $table->enum('condition', ['new', 'good', 'broken'])->default('good');
            
            /**
             * =============================================================
             * TAMBAHAN TERBARU UNTUK SINKRONISASI DAFTAR ASET & FINANSIAL
             * =============================================================
             */
            
            // 4. Detail Finansial & Pembelian
            $table->decimal('purchase_price', 15, 2)->default(0)->comment('Harga beli aset');
            $table->date('purchase_date')->nullable()->comment('Tanggal pembelian aset oleh kantor');
            
            // 5. Informasi Operasional & Penyerahan
            $table->date('handover_date')->nullable()->comment('Tanggal barang diserahkan ke karyawan');
            $table->enum('status', ['available', 'used', 'maintenance', 'lost'])->default('available');
            $table->text('description')->nullable()->comment('Catatan tambahan/kelengkapan');

            $table->timestamps();
            $table->softDeletes(); // Menjaga histori aset jika suatu saat dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};