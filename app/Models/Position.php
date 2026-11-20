<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    /**
     * Kolom yang diizinkan untuk diisi massal.
     * Ditambahkan: base_salary (untuk memperbaiki error Seeder), 
     * daily_salary, allowance, dan overtime_rate.
     */
    protected $fillable = [
        'name',
        'department_id',
        'level',         // staff, pic (supervisor), manager, director
        'description',
        'base_salary',   // Fix: Sesuai field yang diminta oleh PositionSeeder
        'daily_salary',  // Poin 12: Upah harian
        'allowance',     // Poin 2: Tunjangan tetap jabatan
        'overtime_rate', // Poin 9: Upah lembur per jam
    ];

    /**
     * Casting tipe data otomatis.
     * Menggunakan integer atau decimal agar perhitungan payroll akurat.
     */
    protected $casts = [
        'base_salary'   => 'integer',
        'daily_salary'  => 'integer',
        'allowance'     => 'integer',
        'overtime_rate' => 'integer',
    ];

    // =========================================================================
    // RELATIONS (HUBUNGAN ANTAR TABEL)
    // =========================================================================

    /**
     * Relasi: Jabatan ini milik 1 Departemen.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi: 1 Jabatan dimiliki oleh BANYAK User (Karyawan).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // =========================================================================
    // ACCESSORS (ATTRIBUTE CUSTOM UNTUK TAMPILAN)
    // =========================================================================

    /**
     * Poin 7: Menyesuaikan label level agar mendukung PIC (Supervisor).
     */
    public function getLevelLabelAttribute()
    {
        return match ($this->level) {
            'director'   => 'Director / C-Level',
            'manager'    => 'Manager',
            'pic'        => 'PIC / Supervisor', // Poin 7: PIC Role
            'staff'      => 'Staff / Officer',
            default      => ucfirst($this->level),
        };
    }

    /**
     * Helper format rupiah untuk Gaji Pokok (Base Salary)
     */
    public function getFormatBaseSalaryAttribute()
    {
        return 'Rp ' . number_format($this->base_salary, 0, ',', '.');
    }

    /**
     * Helper untuk format rupiah di tampilan Blade.
     */
    public function getFormatDailySalaryAttribute()
    {
        return 'Rp ' . number_format($this->daily_salary, 0, ',', '.');
    }

    public function getFormatAllowanceAttribute()
    {
        return 'Rp ' . number_format($this->allowance, 0, ',', '.');
    }

    /**
     * Mengambil warna badge berdasarkan level (Tailwind Classes).
     */
    public function getLevelColorAttribute()
    {
        return match ($this->level) {
            'director'   => 'bg-rose-50 text-rose-600 border-rose-100',
            'manager'    => 'bg-purple-50 text-purple-600 border-purple-100',
            'pic'        => 'bg-indigo-50 text-indigo-600 border-indigo-100',
            'staff'      => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            default      => 'bg-slate-100 text-slate-500 border-slate-200',
        };
    }
}