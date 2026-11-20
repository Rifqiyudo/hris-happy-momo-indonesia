<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel payrolls.
     * Menggunakan ->default(0) pada semua kolom numerik untuk mencegah error SQL 1364.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // --- PERIODE GAJI ---
            $table->string('month', 2); 
            $table->string('year', 4); 
            $table->date('generated_date'); 
            $table->date('payment_date')->nullable(); 
            
            // --- REKAP KEHADIRAN (Audit Trail) ---
            $table->integer('total_days_worked')->default(0);
            $table->integer('total_late_minutes')->default(0); 
            $table->integer('total_absent')->default(0); 
            $table->integer('total_leave')->default(0); 
            
            // --- KOMPONEN PENDAPATAN TETAP ---
            $table->decimal('base_salary', 15, 2)->default(0); 
            $table->decimal('position_allowance', 15, 2)->default(0); 
            $table->decimal('family_allowance', 15, 2)->default(0); 
            
            // --- KOMPONEN PENDAPATAN VARIABEL ---
            $table->decimal('meal_allowance_per_day', 15, 2)->default(0);
            $table->decimal('transport_allowance_per_day', 15, 2)->default(0);
            $table->decimal('meal_allowance_total', 15, 2)->default(0);
            $table->decimal('transport_allowance_total', 15, 2)->default(0);
            
            // --- KOMPONEN INSIDENTIL ---
            $table->decimal('thr_amount', 15, 2)->default(0);
            $table->decimal('bonus_amount', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            
            // --- KOMPONEN POTONGAN (Audit Transparansi %) ---
            $table->float('jht_percent_snapshot')->default(0);
            $table->float('kes_percent_snapshot')->default(0);
            $table->float('jp_percent_snapshot')->default(0);
            
            $table->decimal('jht_deduction', 15, 2)->default(0);
            $table->decimal('kes_deduction', 15, 2)->default(0);
            $table->decimal('jp_deduction', 15, 2)->default(0);
            
            $table->decimal('routine_deduction', 15, 2)->default(0);
            $table->decimal('late_deduction', 15, 2)->default(0);
            
            // --- RINGKASAN AKHIR (TOTAL) ---
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            
            // --- STATUS & METADATA ---
            $table->enum('status', ['draft', 'published', 'paid'])->default('draft');
            $table->text('notes')->nullable(); 
            $table->timestamps();
            
            // Indexing agar laporan bulanan cepat di-load
            $table->index(['month', 'year']);
            $table->index('status');
        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};