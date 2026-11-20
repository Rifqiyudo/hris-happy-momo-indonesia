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
            // 1. Tambahkan kolom status (active/inactive) jika belum ada
            if (!Schema::hasColumn('announcements', 'status')) {
                $table->string('status')->default('active')->after('content');
            }

            // 2. Tambahkan kolom department_id untuk filter divisi jika belum ada
            if (!Schema::hasColumn('announcements', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('status');
                
                // Menambahkan foreign key agar data konsisten dengan tabel departments
                $table->foreign('department_id')
                      ->references('id')
                      ->on('departments')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Menghapus foreign key dan kolom saat rollback
            if (Schema::hasColumn('announcements', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }

            if (Schema::hasColumn('announcements', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};