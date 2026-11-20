<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom yang diperlukan untuk menyimpan bukti lembur (attachment).
     */
    public function up(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            /**
             * [UPDATE] Menambahkan kolom attachment.
             * Tipe: string (menyimpan path file).
             * Nullable: Boleh kosong (karena mungkin tidak semua lembur ada foto buktinya).
             * After: Diletakkan setelah kolom 'reason' agar struktur tabel rapi.
             */
            $table->string('attachment')->nullable()->after('reason');
            
            /**
             * [TAMBAHAN] Admin Note
             * Penting untuk memberikan alasan jika lembur ditolak oleh Admin.
             */
            if (!Schema::hasColumn('overtimes', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus kolom jika migrasi di-rollback.
     */
    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'admin_note']);
        });
    }
};