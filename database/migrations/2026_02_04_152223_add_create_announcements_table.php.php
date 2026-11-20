<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel announcements.
     * Mendukung pengumuman Global, Per-Divisi, dan Target Role tertentu.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            
            // Konten Utama
            $table->string('title');
            $table->text('content');
            
            // Visual Type: info (biru), warning (kuning), danger (merah)
            $table->enum('type', ['info', 'warning', 'danger'])->default('info');
            
            // Target Audience: all (Semua), employee (Staff), pic (Atasan/PIC)
            $table->enum('target', ['all', 'employee', 'pic'])->default('all');
            
            /**
             * Relasi ke Divisi (department_id)
             * Jika NULL = Pengumuman Global (Lintas Divisi). 
             * Jika berisi ID = Hanya muncul untuk divisi tersebut.
             */
            $table->foreignId('department_id')
                  ->nullable() 
                  ->constrained()
                  ->onDelete('set null'); // Jika departemen dihapus, pengumuman tetap ada (jadi global)

            /**
             * Author (user_id)
             * Mencatat siapa yang membuat pengumuman (Admin/PIC)
             */
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // Jika user dihapus, pengumuman ikut terhapus
            
            /**
             * Status Publikasi
             * 1 = Aktif (Muncul di Dashboard), 0 = Draft
             */
            $table->boolean('is_active')->default(true);

            /**
             * Timestamps
             * Menyediakan created_at & updated_at bertipe TIMESTAMP.
             * Membantu pengurutan "Pengumuman Terbaru".
             */
            $table->timestamps();
        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};