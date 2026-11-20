<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom agama dan memastikan kolom pendukung lainnya tersedia.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambahkan kolom agama setelah gender
            if (!Schema::hasColumn('users', 'religion')) {
                $table->string('religion')->nullable()->after('gender');
            }

            // 2. Pastikan kolom lain yang dibutuhkan form edit sudah ada (untuk jaga-jaga)
            if (!Schema::hasColumn('users', 'last_education')) {
                $table->string('last_education')->nullable()->after('religion');
            }

            if (!Schema::hasColumn('users', 'birth_place')) {
                $table->string('birth_place')->nullable()->after('last_education');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['religion', 'last_education', 'birth_place']);
        });
    }
};