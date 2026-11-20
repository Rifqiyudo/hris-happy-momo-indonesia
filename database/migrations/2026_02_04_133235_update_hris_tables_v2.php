<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update Tabel Users (Poin 5, 7, 10)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'resign_date')) {
                $table->date('resign_date')->nullable()->after('join_date'); // Poin 5
            }
            if (!Schema::hasColumn('users', 'bpjs_kesehatan')) {
                $table->decimal('bpjs_kesehatan', 12, 2)->default(0)->after('bank_account'); // Poin 10
            }
            if (!Schema::hasColumn('users', 'bpjs_ketenagakerjaan')) {
                $table->decimal('bpjs_ketenagakerjaan', 12, 2)->default(0)->after('bpjs_kesehatan'); // Poin 10
            }
        });

        // Update Tabel Attendances (Poin 1, 3)
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'type')) {
                // 'office' untuk barcode, 'visit' untuk absen luar (Poin 1 & 3)
                $table->string('type')->default('office')->after('status'); 
            }
            if (!Schema::hasColumn('attendances', 'location_name')) {
                $table->string('location_name')->nullable()->after('type'); // Nama lokasi visit
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['resign_date', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['type', 'location_name']);
        });
    }
};