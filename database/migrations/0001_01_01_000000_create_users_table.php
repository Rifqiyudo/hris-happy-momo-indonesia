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
        // 1. TABEL USERS (KARYAWAN PT. HAPPY MOMO INDONESIA)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // --- [RELASI DATA] ---
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->unsignedBigInteger('position_id')->nullable()->index(); 
            $table->unsignedBigInteger('costcenter_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable(); 

            // --- [DATA PRIBADI & KREDENSIAL] ---
            $table->string('nik')->unique()->nullable(); // Kunci Utama Sinkronisasi
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('sex', 10)->nullable(); // M / F
            $table->string('religion')->nullable();
            $table->string('phone')->nullable();
            $table->string('birth_place')->nullable();      
            $table->date('birth_date')->nullable();        
            $table->text('address')->nullable();
            $table->text('domicile')->nullable(); 

            // --- [DATA KEPEGAWAIAN & PENDIDIKAN] ---
            $table->string('last_education')->nullable(); 
            $table->string('educational_major')->nullable(); // Idx 27 pada CSV: Bidang Jurusan
            $table->date('join_date')->nullable(); // Idx 22 pada CSV: Date In
            $table->date('resign_date')->nullable(); 
            $table->string('employee_status')->nullable(); // Permanent / Contract
            $table->integer('contract_duration')->nullable(); // Dalam Bulan
            
            // --- [DATA LEGALITAS, PAJAK & BANK] ---
            $table->string('ktp_no')->nullable();
            $table->string('npwp_no')->nullable(); 
            $table->string('tax_type')->nullable(); // TK/0, K/1, dsb
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable(); 
            
            // Asuransi / BPJS
            $table->string('bpjs_ketenagakerjaan_no')->nullable(); 
            $table->string('bpjs_kesehatan_no')->nullable(); 
            
            // --- [PENGATURAN ABSENSI & PAYROLL] ---
            $table->decimal('late_deduction', 12, 2)->default(0); // Potongan per menit
            $table->integer('late_tolerance')->default(0); // Toleransi keterlambatan (Menit)
            $table->string('attendance_mode')->default('IP'); 
            $table->boolean('allow_outstation_attendance')->default(false); 
            $table->boolean('allow_other_branch_attendance')->default(false); 
            
            // --- [SYSTEM ROLE & STATUS] ---
            $table->string('role')->default('employee'); // admin, pic, employee
            $table->boolean('is_active')->default(true); 
            $table->string('avatar')->nullable();        
            $table->string('barcode_token')->nullable()->unique(); // Digunakan untuk Kiosk Absensi

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. TABEL PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. TABEL SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};