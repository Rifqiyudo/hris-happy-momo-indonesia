<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiIndicator extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'name', 
        'description', 
        'weight', 
        'is_active'
    ];

    /**
     * Casting tipe data kolom agar integritas data terjaga.
     */
    protected $casts = [
        'weight'    => 'integer',
        'is_active' => 'boolean',
    ];

    // =========================================================================
    // ACCESSORS (VIRTUAL ATTRIBUTES)
    // =========================================================================

    /**
     * Menampilkan bobot dengan simbol persen (Contoh: 20%).
     */
    public function getFormattedWeightAttribute(): string
    {
        return $this->weight . '%';
    }

    /**
     * Menampilkan label status aktif dengan badge Tailwind CSS.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active 
            ? '<span class="px-2 py-1 bg-emerald-100 text-emerald-600 rounded-md text-[10px] font-black uppercase border border-emerald-200">Aktif</span>'
            : '<span class="px-2 py-1 bg-slate-100 text-slate-400 rounded-md text-[10px] font-black uppercase border border-slate-200">Non-Aktif</span>';
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope untuk mengambil hanya indikator yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper untuk menghitung total bobot yang sudah digunakan di database.
     * Total weight dari seluruh indikator aktif idealnya berjumlah 100%.
     */
    public static function getTotalWeight(): int
    {
        return self::where('is_active', true)->sum('weight');
    }
}