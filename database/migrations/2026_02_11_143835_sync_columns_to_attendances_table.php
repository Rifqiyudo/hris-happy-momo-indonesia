<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sinkronisasi kolom untuk sistem absensi Happy Momo
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // 1. Tambah kolom Address (Text) untuk menyimpan alamat riil dari Nominatim
            if (!Schema::hasColumn('attendances', 'address')) {
                $table->text('address')->nullable()->after('longitude');
            }

            // 2. Tambah kolom image_in jika belum ada (menggantikan image_visit jika perlu)
            if (!Schema::hasColumn('attendances', 'image_in')) {
                $table->string('image_in')->nullable()->after('clock_in');
            }

            // 3. Tambah kolom image_out untuk menyimpan foto saat absen pulang
            if (!Schema::hasColumn('attendances', 'image_out')) {
                $table->string('image_out')->nullable()->after('clock_out');
            }

            // 4. Tambah kolom Category (Office/Visit) jika belum ada
            if (!Schema::hasColumn('attendances', 'category')) {
                $table->enum('category', ['office', 'visit'])->default('office')->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['address', 'image_in', 'image_out', 'category']);
        });
    }
};