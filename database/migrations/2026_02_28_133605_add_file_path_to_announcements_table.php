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
        Schema::table('announcements', function (Blueprint $table) {
            // Menambahkan kolom file_path setelah konten agar urutan rapi
            if (!Schema::hasColumn('announcements', 'file_path')) {
                $table->string('file_path')->nullable()->after('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Hapus kolom jika rollback
            if (Schema::hasColumn('announcements', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};