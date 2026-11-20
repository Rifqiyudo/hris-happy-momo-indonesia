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
        // 1. Tabel Master Aset
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            /**
             * PERBAIKAN: Menghapus ->after()
             * Susun kolom langsung di posisi yang diinginkan.
             */
            $table->string('brand')->nullable()->comment('Merk aset: Apple, Honda, dll');
            $table->string('serial_number')->unique();
            $table->enum('category', ['electronics', 'furniture', 'vehicle', 'others']);
            $table->enum('status', ['available', 'assigned', 'broken', 'maintenance'])->default('available');
            
            $table->string('location')->nullable()->comment('Lokasi fisik aset: Gudang, Kantor Pusat');
            
            $table->decimal('purchase_price', 15, 2);
            $table->date('purchase_date');
            
            $table->string('image')->nullable()->comment('Foto fisik aset');
            
            // Relasi ke pemegang saat ini
            $table->foreignId('current_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Karyawan pemegang saat ini');
            
            $table->timestamps();
            $table->softDeletes(); // Mengamankan histori aset jika dihapus
        });

        // 2. Tabel Riwayat Penyerahan (Assignment)
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('return_date')->nullable();
            $table->text('condition_notes')->nullable();

            /**
             * Snapshot Data Karyawan
             * Mencatat detail posisi/cabang saat barang diserahkan (untuk audit)
             */
            $table->string('employee_nik_snapshot')->nullable();
            $table->string('branch_name_snapshot')->nullable();
            $table->string('handover_image')->nullable()->comment('Bukti foto serah terima');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key constraints sebelum drop agar tidak error
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::enableForeignKeyConstraints();
    }
};