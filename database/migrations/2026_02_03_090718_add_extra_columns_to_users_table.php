<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom detail personal, pekerjaan, dan bank ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Pendidikan Terakhir
            if (!Schema::hasColumn('users', 'last_education')) {
                // Gunakan religion jika ada, jika tidak ada letakkan setelah email
                $target = Schema::hasColumn('users', 'religion') ? 'religion' : 'email';
                $table->string('last_education')->nullable()->after($target);
            }

            // 2. Tempat Lahir
            if (!Schema::hasColumn('users', 'birth_place')) {
                $table->string('birth_place')->nullable()->after('last_education');
            }

            // 3. Tanggal Bergabung
            if (!Schema::hasColumn('users', 'join_date')) {
                // Cek shift_id, jika tidak ada letakkan di paling bawah (tanpa after)
                if (Schema::hasColumn('users', 'shift_id')) {
                    $table->date('join_date')->nullable()->after('shift_id');
                } else {
                    $table->date('join_date')->nullable();
                }
            }

            // 4. Rekening Bank
            if (!Schema::hasColumn('users', 'bank_account')) {
                if (Schema::hasColumn('users', 'bank_name')) {
                    $table->string('bank_account')->nullable()->after('bank_name');
                } else {
                    // Jika bank_name juga belum ada, buatkan keduanya
                    $table->string('bank_name')->nullable();
                    $table->string('bank_account')->nullable()->after('bank_name');
                }
            }
        });
    }

    /**
     * Reverse the migrations (Rollback).
     * Menghapus kolom secara aman satu per satu.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['last_education', 'birth_place', 'join_date', 'bank_account', 'bank_name'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};