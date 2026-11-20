<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah kolom 'type' menjadi string untuk fleksibilitas input.
     */
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Mengubah tipe data kolom dari ENUM ke STRING
            // Ini akan menghentikan error "Data truncated" saat input 'Cuti'
            $table->string('type')->change();
            
            // Opsional: Menambahkan kolom catatan admin jika belum ada
            if (!Schema::hasColumn('leaves', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Mengembalikan ke ENUM jika diperlukan (Hati-hati jika data sudah banyak)
            $table->enum('type', ['sakit', 'izin', 'cuti_tahunan', 'lainnya'])->change();
            
            if (Schema::hasColumn('leaves', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
        });
    }
};