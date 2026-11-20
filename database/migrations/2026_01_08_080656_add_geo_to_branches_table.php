<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan koordinat dan radius geofencing pada kantor cabang.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Gunakan hasColumn agar tidak error "Duplicate Column"
            if (!Schema::hasColumn('branches', 'latitude')) {
                $table->double('latitude')->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'longitude')) {
                $table->double('longitude')->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('branches', 'radius')) {
                // Radius dalam satuan meter
                $table->integer('radius')->default(100)->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations (Rollback).
     * Menggunakan looping dan pengecekan hasColumn agar tidak macet saat DROP.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $columns = ['latitude', 'longitude', 'radius'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('branches', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};