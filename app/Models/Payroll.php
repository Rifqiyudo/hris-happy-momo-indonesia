<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * Snapshot data dari Master Gaji dan rekap Absensi saat payroll di-generate.
     */
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'generated_date',
        'payment_date',
        // Rekap Absensi
        'total_days_worked',
        'total_late_minutes',
        'total_absent',
        'total_leave',
        // Snapshot Pendapatan (Master Gaji saat itu)
        'base_salary',
        'position_allowance',
        'family_allowance',
        // Rincian Tunjangan Harian
        'meal_allowance_per_day',
        'meal_allowance_total',
        'transport_allowance_per_day',
        'transport_allowance_total',
        // Pendapatan Non-Rutin
        'thr_amount',
        'bonus_amount',
        'other_allowance',
        // Snapshot Potongan & Persentase (Mencegah Error 1364)
        'jht_deduction',
        'kes_deduction',
        'jp_deduction',                // Tambahan: Nominal JP
        'jht_percent_snapshot',
        'kes_percent_snapshot',
        'jp_percent_snapshot',         // Tambahan: Persentase JP
        'routine_deduction',
        'late_deduction',
        // Hasil Akhir
        'total_allowance',
        'total_deduction',
        'net_salary',
        'notes',
        'status', // 'draft', 'published', 'paid'
    ];

    /**
     * Casting atribut untuk integritas tipe data matematis.
     */
    protected $casts = [
        'generated_date' => 'date',
        'payment_date'   => 'date',
        'base_salary'    => 'double',
        'position_allowance' => 'double',
        'family_allowance'   => 'double',
        'meal_allowance_per_day' => 'double',
        'meal_allowance_total'   => 'double',
        'transport_allowance_per_day' => 'double',
        'transport_allowance_total'   => 'double',
        'thr_amount'     => 'double',
        'bonus_amount'   => 'double',
        'other_allowance' => 'double',
        'jht_deduction'  => 'double',
        'kes_deduction'  => 'double',
        'jp_deduction'   => 'double',
        'jht_percent_snapshot' => 'double',
        'kes_percent_snapshot' => 'double',
        'jp_percent_snapshot'  => 'double',
        'routine_deduction' => 'double',
        'late_deduction' => 'double',
        'total_allowance' => 'double',
        'total_deduction' => 'double',
        'net_salary'     => 'double',
    ];

    /**
     * Otomatis memuat atribut virtual saat model diubah menjadi array/JSON.
     */
    protected $appends = [
        'formatted_net_salary',
        'month_label',
        'status_html',
        'meal_detail',
        'transport_detail',
        'gross_salary',
        'total_bpjs_deduction'
    ];

    /**
     * Relasi ke User (Karyawan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Karyawan Tidak Aktif',
            'nik'  => 'N/A'
        ]);
    }

    // =========================================================================
    // ACCESSORS (VIRTUAL ATTRIBUTES)
    // =========================================================================

    /**
     * Menghitung total penghasilan bruto (Gaji Pokok + Semua Tunjangan).
     */
    public function getGrossSalaryAttribute(): float
    {
        return $this->base_salary + $this->total_allowance;
    }

    /**
     * Menghitung akumulasi potongan BPJS (JHT + KES + JP).
     */
    public function getTotalBpjsDeductionAttribute(): float
    {
        return $this->jht_deduction + $this->kes_deduction + $this->jp_deduction;
    }

    /**
     * Format Rupiah untuk tampilan.
     */
    public function getFormattedNetSalaryAttribute(): string
    {
        return 'Rp ' . number_format($this->net_salary, 0, ',', '.');
    }

    public function getFormattedBaseSalaryAttribute(): string
    {
        return 'Rp ' . number_format($this->base_salary, 0, ',', '.');
    }

    public function getFormattedTotalAllowanceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_allowance, 0, ',', '.');
    }

    public function getFormattedTotalDeductionAttribute(): string
    {
        return 'Rp ' . number_format($this->total_deduction, 0, ',', '.');
    }

    /**
     * Detail rincian tunjangan harian untuk slip gaji.
     */
    public function getMealDetailAttribute(): string
    {
        return 'Rp ' . number_format($this->meal_allowance_per_day, 0, ',', '.') . ' x ' . $this->total_days_worked . ' Hari';
    }

    public function getTransportDetailAttribute(): string
    {
        return 'Rp ' . number_format($this->transport_allowance_per_day, 0, ',', '.') . ' x ' . $this->total_days_worked . ' Hari';
    }

    /**
     * Mengambil label nama bulan (Bahasa Indonesia).
     */
    public function getMonthLabelAttribute(): string
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F');
    }

    /**
     * Visualisasi Label Status menggunakan Badge Tailwind CSS.
     */
    public function getStatusHtmlAttribute(): string
    {
        return match ($this->status) {
            'paid'      => '<span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-black uppercase border border-emerald-200 shadow-sm"><i class="fas fa-check-circle mr-1"></i> Terbayar</span>',
            'published' => '<span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-lg text-[10px] font-black uppercase border border-indigo-200 shadow-sm"><i class="fas fa-bullhorn mr-1"></i> Terbit</span>',
            default     => '<span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase border border-slate-200 shadow-sm"><i class="fas fa-file-signature mr-1"></i> Draft</span>',
        };
    }

    // =========================================================================
    // SCOPES & LOGIC
    // =========================================================================

    /**
     * Scope filter periode bulan/tahun.
     */
    public function scopePeriode($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}