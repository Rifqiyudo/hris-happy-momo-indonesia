<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom slug untuk mendukung URL SEO-friendly dan pencarian data.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Menambahkan kolom slug setelah kolom title
            // unique() untuk memastikan tidak ada slug yang sama
            $table->string('slug')->unique()->after('title')->comment('SEO friendly URL from title');
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus kolom slug jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};