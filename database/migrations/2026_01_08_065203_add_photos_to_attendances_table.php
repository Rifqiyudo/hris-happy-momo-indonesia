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
        Schema::table('attendances', function (Blueprint $table) {
            
            // Cek dulu: Jika kolom 'photo_in' BELUM ada, baru ditambahkan
            if (!Schema::hasColumn('attendances', 'photo_in')) {
                $table->string('photo_in')->nullable()->after('status');
            }

            // Cek dulu: Jika kolom 'photo_out' BELUM ada, baru ditambahkan
            if (!Schema::hasColumn('attendances', 'photo_out')) {
                // Kita coba taruh after photo_in, kalau photo_in baru dibuat
                // Jika photo_in sudah ada sebelumnya, urutan tetap aman
                $table->string('photo_out')->nullable()->after('status'); 
            }

            // Cek koordinat
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->string('latitude')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->string('longitude')->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Hapus kolom hanya jika kolomnya ada
            $columnsToDrop = [];
            
            if (Schema::hasColumn('attendances', 'photo_in')) $columnsToDrop[] = 'photo_in';
            if (Schema::hasColumn('attendances', 'photo_out')) $columnsToDrop[] = 'photo_out';
            if (Schema::hasColumn('attendances', 'latitude')) $columnsToDrop[] = 'latitude';
            if (Schema::hasColumn('attendances', 'longitude')) $columnsToDrop[] = 'longitude';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};