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
        // Gunakan if statement agar lebih aman saat migrate:refresh
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                
                // Nama Divisi (e.g., Information Technology, Human Resources)
                $table->string('name')->unique(); 

                /**
                 * =============================================================
                 * TAMBAHAN TERBARU UNTUK SINKRONISASI MONITORING & UI
                 * PERBAIKAN: Menghapus ->after() karena ini fungsi Create Table
                 * =============================================================
                 */

                // 1. Penanggung Jawab Departemen (PIC)
                $table->string('manager_name')->nullable()->comment('Nama Kepala Departemen');

                // 2. Kode Divisi (e.g., IT, HRD, FIN) - Berguna untuk penomoran NIK/Surat
                $table->string('code', 10)->nullable()->unique();

                // 3. Identitas Visual (Color Label)
                // Digunakan untuk mewarnai badge departemen di tabel monitoring karyawan
                $table->string('color_code', 10)->default('#4f46e5')->comment('Hex color untuk UI');

                // 4. Deskripsi singkat divisi
                $table->text('description')->nullable();

                // 5. Status Departemen
                $table->boolean('is_active')->default(true);

                /**
                 * =============================================================
                 * EKSTENSI STRUKTUR ORGANISASI & BUDGETING
                 * =============================================================
                 */
                
                // 6. Sub-Departemen (Self-Referencing)
                // Memungkinkan departemen memiliki sub-unit di bawahnya
                $table->foreignId('parent_id')->nullable()->constrained('departments')->onDelete('set null');

                // 7. Alokasi Budget Operasional (Optional)
                $table->decimal('monthly_budget', 15, 2)->default(0);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sebelum menghapus master, pastikan constraint tidak terkunci
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('departments');
        Schema::enableForeignKeyConstraints();
    }
};