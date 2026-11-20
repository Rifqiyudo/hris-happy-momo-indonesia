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
        Schema::table('branches', function (Blueprint $table) {
            
            // Cek dulu: Kalau kolom 'phone' BELUM ada, baru buat.
            if (!Schema::hasColumn('branches', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }

            // Cek dulu: Kalau kolom 'latitude' BELUM ada, baru buat.
            if (!Schema::hasColumn('branches', 'latitude')) {
                $table->string('latitude')->nullable()->after('address');
            }

            // Cek dulu: Kalau kolom 'longitude' BELUM ada, baru buat.
            if (!Schema::hasColumn('branches', 'longitude')) {
                $table->string('longitude')->nullable()->after('latitude');
            }

            // Cek dulu: Kalau kolom 'radius' BELUM ada, baru buat.
            if (!Schema::hasColumn('branches', 'radius')) {
                $table->integer('radius')->default(50)->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Hapus kolom hanya jika ada
            $columns = ['phone', 'latitude', 'longitude', 'radius'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('branches', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};