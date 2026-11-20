<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Salary
 * Class ini bertanggung jawab mengelola logika penggajian (Payroll) 
 * termasuk tunjangan tetap, tidak tetap, lembur, dan potongan BPJS secara real-time.
 */
class Salary extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara masal.
     * Diselaraskan dengan skema database hris_momo dan master kolektif.
     */
    protected $fillable = [
        'user_id', 
        'base_salary',           // Gaji Pokok
        'position_allowance',    // Tunjangan Jabatan (Tetap)
        'family_allowance',      // Tunjangan Keluarga (Tetap)
        'meal_allowance',        // Uang Makan (Harian)
        'transport_allowance',   // Uang Transport (Harian)
        'other_allowance',       // Tunjangan Lainnya
        'thr_amount',            // Tunjangan Hari Raya
        'bonus_amount',          // Bonus / Insentif
        'overtime_amount',       // Uang Lembur (BARU)
        'jht_percent',           // % BPJS JHT (Sekarang diset 0)
        'kes_percent',           // % BPJS Kesehatan (Sekarang diset 0)
        'jht_deduction',         // Nominal Rupiah BPJS JHT
        'kes_deduction',         // Nominal Rupiah BPJS Kesehatan
        'jp_deduction',          // Nominal Rupiah BPJS Pensiun / JP (BARU)
        'routine_deduction'      // Potongan Rutin (Koperasi/Iuran)
    ];

    /**
     * Nilai default untuk mencegah error kalkulasi pada field bernilai NULL.
     */
    protected $attributes = [
        'base_salary'         => 0,
        'position_allowance'  => 0,
        'family_allowance'    => 0,
        'meal_allowance'      => 0,
        'transport_allowance' => 0,
        'other_allowance'     => 0,
        'thr_amount'          => 0,
        'bonus_amount'        => 0,
        'overtime_amount'     => 0, // Default nominal Lembur
        'jht_percent'         => 0, // Default diubah ke 0 karena menggunakan nominal
        'kes_percent'         => 0, // Default diubah ke 0 karena menggunakan nominal
        'jht_deduction'       => 0, // Default nominal JHT
        'kes_deduction'       => 0, // Default nominal Kesehatan
        'jp_deduction'        => 0, // Default nominal JP
        'routine_deduction'   => 0,
    ];

    /**
     * Casting tipe data agar konsisten saat proses perhitungan matematis.
     * Menggunakan 'double' untuk mendukung nilai desimal akurat.
     */
    protected $casts = [
        'user_id'             => 'integer',
        'base_salary'         => 'double',
        'position_allowance'  => 'double',
        'family_allowance'    => 'double',
        'meal_allowance'      => 'double',
        'transport_allowance' => 'double',
        'other_allowance'     => 'double',
        'thr_amount'          => 'double',
        'bonus_amount'        => 'double',
        'overtime_amount'     => 'double', // Cast nominal Lembur
        'jht_percent'         => 'double',
        'kes_percent'         => 'double',
        'jht_deduction'       => 'double', // Cast nominal JHT
        'kes_deduction'       => 'double', // Cast nominal Kesehatan
        'jp_deduction'        => 'double', // Cast nominal JP
        'routine_deduction'   => 'double',
    ];

    /**
     * Menambahkan atribut virtual (computed) ke dalam format JSON/Array.
     */
    protected $appends = [
        'dpi',
        'nominal_jht',
        'nominal_kes',
        'nominal_jp',
        'total_allowance',
        'total_bruto',
        'total_deduction',
        'take_home_pay'
    ];

    // =========================================================================
    // RELASI (RELATIONSHIPS)
    // =========================================================================

    /**
     * Menghubungkan data gaji dengan pemilik akun (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // =========================================================================
    // AKSESOR (COMPUTED PROPERTIES)
    // =========================================================================

    /**
     * Dasar Perhitungan Iuran (DPI) untuk BPJS.
     * Aturan: Gaji Pokok + Tunjangan Tetap (Jabatan & Keluarga).
     * (Masih dipertahankan jika sewaktu-waktu dibutuhkan referensi)
     */
    public function getDpiAttribute(): float
    {
        return (double) ($this->base_salary + $this->position_allowance + $this->family_allowance);
    }

    /**
     * Nominal Rupiah BPJS Jaminan Hari Tua (JHT).
     * Telah diubah: Mengambil langsung dari input form nominal (bukan dari persen lagi).
     */
    public function getNominalJhtAttribute(): float
    {
        return (double) $this->jht_deduction;
    }

    /**
     * Nominal Rupiah BPJS Kesehatan.
     * Telah diubah: Mengambil langsung dari input form nominal (bukan dari persen lagi).
     */
    public function getNominalKesAttribute(): float
    {
        return (double) $this->kes_deduction;
    }

    /**
     * Nominal Rupiah BPJS Pensiun (JP).
     * Mengambil langsung dari input form nominal.
     */
    public function getNominalJpAttribute(): float
    {
        return (double) $this->jp_deduction;
    }

    /**
     * Menghitung total seluruh tunjangan (Fixed + Variable + Bonus + Lembur).
     */
    public function getTotalAllowanceAttribute(): float
    {
        return (double) (
            $this->position_allowance + 
            $this->family_allowance + 
            $this->meal_allowance + 
            $this->transport_allowance + 
            $this->other_allowance +
            $this->thr_amount +
            $this->bonus_amount +
            $this->overtime_amount // <-- Ditambahkan ke kalkulasi total tunjangan (Bruto)
        );
    }

    /**
     * Pendapatan Kotor (Bruto).
     */
    public function getTotalBrutoAttribute(): float
    {
        return (double) ($this->base_salary + $this->total_allowance);
    }

    /**
     * Total semua potongan (BPJS + Rutin).
     * Tetap menggunakan nominal_jht, nominal_kes, nominal_jp karena sudah membaca data Rupiah.
     */
    public function getTotalDeductionAttribute(): float
    {
        return (double) ($this->nominal_jht + $this->nominal_kes + $this->nominal_jp + $this->routine_deduction);
    }

    /**
     * Gaji Bersih yang diterima karyawan (Take Home Pay).
     */
    public function getTakeHomePayAttribute(): float
    {
        $thp = $this->total_bruto - $this->total_deduction;
        return (double) max(0, round($thp)); 
    }
}