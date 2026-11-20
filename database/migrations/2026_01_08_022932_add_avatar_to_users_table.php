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
        // CEK DULU: Apakah kolom 'avatar' belum ada di tabel 'users'?
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('email'); // Buat kolom foto
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // CEK DULU: Kalau kolomnya ada, baru dihapus (untuk rollback)
        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
    }
};