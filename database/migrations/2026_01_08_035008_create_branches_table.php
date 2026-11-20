<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan data titik lokasi kantor/unit kerja PT. Happy Momo Indonesia.
     * Terintegrasi dengan Geofencing Absensi dan Payroll.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            
            // 1. IDENTITAS UTAMA CABANG
            // Code: Kode unik (HMI-JKT-01), Name: Nama PT/Cabang (Happy Momo Pusat)
            $table->string('code')->unique(); 
            $table->string('name');
            
            // 2. INFORMASI OPERASIONAL & PIC
            // Berguna untuk monitoring siapa penanggung jawab di unit tersebut
            $table->string('pic_name')->nullable()->comment('Nama Manager/Kepala Cabang');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // 3. ALAMAT LENGKAP
            $table->text('address')->nullable();
            $table->string('city')->nullable(); 
            $table->string('province')->nullable();

            /**
             * 4. PENETAPAN GEOLOKASI (SANGAT PENTING UNTUK ABSENSI)
             * Latitude & Longitude menggunakan presisi tinggi.
             * Decimal 10,8 mencakup akurasi hingga jarak 1.1 mm.
             */
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            /**
             * 5. RADIUS PEMBATAS (GEOFENCING)
             * Jarak aman dalam satuan METER untuk validasi lokasi absen karyawan.
             */
            $table->integer('radius')->default(100)->comment('Batas jarak absen dalam satuan Meter'); 

            /**
             * 6. ZONA WAKTU (TIMEZONE)
             * Menjamin jam absen sinkron jika ada cabang di wilayah WITA atau WIT.
             */
            $table->string('timezone')->default('Asia/Jakarta'); 

            /**
             * =============================================================
             * TAMBAHAN TERBARU UNTUK SINKRONISASI PAYROLL & MONITORING
             * =============================================================
             * PERBAIKAN: Menghilangkan ->after() karena ini fungsi Create Table
             */
            
            // 7. DATA FINANSIAL UNIT
            // Digunakan sebagai dasar perhitungan upah minimum di wilayah tersebut
            $table->decimal('umr_value', 15, 2)->default(0)->comment('Nilai UMR/UMK wilayah cabang');

            // 8. IDENTITAS VISUAL & LOKASI
            // Untuk mempermudah verifikasi saat monitoring lokasi di dashboard
            $table->string('location_snapshot')->nullable()->comment('URL/Path foto gedung/tampak depan');
            
            // 9. STATUS PROPERTI
            $table->enum('ownership_status', ['owned', 'rented', 'virtual'])->default('owned');

            // 10. STATUS UNIT
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes(); // Menghindari kehilangan data riwayat jika cabang dinonaktifkan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};