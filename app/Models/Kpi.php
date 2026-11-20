<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Kpi extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'user_id', 
        'period_month', 
        'period_year', 
        'score_details', 
        'final_score', 
        'grade', 
        'feedback', 
        'rated_by', 
        'status'
    ];

    /**
     * Casting tipe data kolom agar otomatis dikonversi oleh Eloquent.
     */
    protected $casts = [
        'score_details' => 'array', // Konversi JSON ke Array PHP otomatis
        'final_score'   => 'double',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Menambahkan atribut virtual (Accessors) ke dalam output array/JSON.
     */
    protected $appends = [
        'status_html',
        'grade_label',
        'period_label',
        'score_color'
    ];

    /**
     * Booted function untuk menghitung grade otomatis sebelum data disimpan ke database.
     */
    protected static function booted()
    {
        static::saving(function ($kpi) {
            $score = $kpi->final_score;
            
            // Logic Penentuan Grade (Standard Performance Corporate)
            if ($score >= 90) $kpi->grade = 'A';
            elseif ($score >= 80) $kpi->grade = 'B';
            elseif ($score >= 70) $kpi->grade = 'C';
            elseif ($score >= 60) $kpi->grade = 'D';
            else $kpi->grade = 'E';
        });
    }

    // =========================================================================
    // RELATIONS (Hubungan Antar Tabel)
    // =========================================================================

    /**
     * Relasi ke Karyawan yang dinilai.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Atasan/Admin yang memberikan penilaian (Reviewer).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

    // =========================================================================
    // ACCESSORS (Atribut Virtual untuk Keperluan UI)
    // =========================================================================

    /**
     * Label Bulan Periode yang rapi (Contoh: "Februari 2026").
     */
    public function getPeriodLabelAttribute(): string
    {
        if (!$this->period_month || !$this->period_year) return '-';
        
        return Carbon::createFromDate($this->period_year, $this->period_month, 1)
            ->translatedFormat('F Y');
    }

    /**
     * Mendapatkan class warna Tailwind berdasarkan skor (untuk progress bar/teks).
     */
    public function getScoreColorAttribute(): string
    {
        $score = $this->final_score;
        if ($score >= 80) return 'emerald';
        if ($score >= 70) return 'indigo';
        if ($score >= 60) return 'amber';
        return 'rose';
    }

    /**
     * Visualisasi Status menggunakan Badge Tailwind CSS yang konsisten dengan sistem.
     */
    public function getStatusHtmlAttribute(): string
    {
        return match ($this->status) {
            'published' => '<span class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-black uppercase border border-emerald-200">Published</span>',
            default     => '<span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase border border-slate-200">Draft</span>',
        };
    }

    /**
     * Visualisasi Grade dengan format lingkaran warna warni sesuai tingkatan.
     */
    public function getGradeLabelAttribute(): string
    {
        $color = match ($this->grade) {
            'A' => 'emerald',
            'B' => 'blue',
            'C' => 'amber',
            'D' => 'orange',
            'E' => 'rose',
            default => 'slate',
        };

        $val = $this->grade ?? '-';

        return "<div class='flex items-center justify-center w-8 h-8 rounded-full bg-{$color}-50 border border-{$color}-200'>
                    <span class='font-black text-{$color}-600 text-sm'>{$val}</span>
                </div>";
    }
}