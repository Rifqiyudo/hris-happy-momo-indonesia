<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel pengajuan cuti dan izin dengan workflow approval.
     */
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (karyawan)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Kategori Izin
            $table->enum('type', ['Cuti', 'Sakit', 'Izin', 'Dispensation', 'Maternity'])
                  ->comment('Tipe pengajuan ketidakhadiran');
            
            // Rentang Waktu
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->comment('Jumlah hari yang diajukan');
            
            // Alasan & Bukti Fisik
            $table->text('reason')->comment('Alasan pengajuan');
            $table->string('attachment')->nullable()->comment('Path file pendukung');
            
            // Workflow Approval (Status Terkini)
            // Alur: Pending -> approved_by_pic -> approved_by_admin (Final)
            $table->enum('status', ['Pending', 'Approved_by_pic', 'Rejected_by_pic', 'Approved_by_admin', 'Rejected_by_admin', 'Cancelled'])
                  ->default('Pending');
            
            // Approval by PIC (Supervisor)
            $table->foreignId('approved_by_pic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_by_pic_at')->nullable();
            
            // Approval by Admin (HRD)
            $table->foreignId('approved_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_by_admin_at')->nullable();
            
            // Catatan Feedback
            $table->text('pic_note')->nullable()->comment('Catatan dari Supervisor');
            $table->text('admin_note')->nullable()->comment('Catatan dari HRD');
            
            $table->timestamps();
            
            // Indexing untuk performa
            $table->index(['start_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};