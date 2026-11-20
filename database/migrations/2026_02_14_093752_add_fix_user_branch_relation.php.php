<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // LANGKAH 1: Pembersihan Data (Data Scrubbing)
        // Memastikan tidak ada branch_id di tabel users yang ID-nya tidak ada di tabel branches
        // Jika ada, kita set ke ID 1 (Pusat) sebagai fail-safe.
        DB::table('users')->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('branches')
                  ->whereRaw('branches.id = users.branch_id');
        })->update(['branch_id' => 1]);

        // LANGKAH 2: Modifikasi Struktur Tabel
        Schema::table('users', function (Blueprint $table) {
            // Mengubah tipe data menjadi unsigned agar sinkron dengan primary key branches
            // Gunakan change() untuk mengubah kolom yang sudah ada
            $table->unsignedBigInteger('branch_id')->nullable(false)->change();

            // Memasang Foreign Key Constraint
            // onDelete('cascade'): Jika cabang dihapus, user di cabang tersebut ikut terhapus
            // atau gunakan onDelete('restrict') jika ingin mencegah penghapusan cabang yang masih punya user
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus foreign key terlebih dahulu
            $table->dropForeign(['branch_id']);
            
            // Mengembalikan ke tipe data semula jika diperlukan (opsional)
            $table->bigInteger('branch_id')->change();
        });
    }
};