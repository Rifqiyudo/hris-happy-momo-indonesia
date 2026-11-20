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
        // 1. Membuat Tabel Pola Kerja Mingguan
        Schema::create('working_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Contoh: Pola Reguler Office'); 
            
            // Mapping 7 Hari ke ID Master Shift (Tabel 'shifts')
            // nullable() karena bisa jadi hari tersebut libur (tidak ada shift)
            // nullOnDelete() agar jika shift master dihapus, harinya jadi kosong (tidak error)
            $table->foreignId('mon_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('tue_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('wed_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('thu_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('fri_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('sat_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('sun_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            
            $table->timestamps();
        });

        // 2. Menambahkan relasi pattern_id ke tabel users (karyawan)
        Schema::table('users', function (Blueprint $table) {
            // Menaruh kolom pattern_id setelah posisi/jabatan agar rapi di database
            $table->foreignId('pattern_id')->nullable()->after('position_id')->constrained('working_patterns')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Hapus relasi dan kolom dari tabel users terlebih dahulu
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pattern_id']);
            $table->dropColumn('pattern_id');
        });

        // 2. Baru hapus tabel working_patterns
        Schema::dropIfExists('working_patterns');
    }
};