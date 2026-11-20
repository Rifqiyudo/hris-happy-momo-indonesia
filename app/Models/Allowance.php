<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import SoftDeletes
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahan: Import BelongsTo untuk relasi Jabatan & Karyawan

class Allowance extends Model
{
    use HasFactory, SoftDeletes; // 2. Gunakan Trait SoftDeletes

    /**
     * Kolom yang diizinkan untuk diisi massal.
     * Sudah disesuaikan dengan struktur tabel terbaru.
     */
    protected $fillable = [
        'name',
        'type',        // Enum: 'fixed' (Bulanan) atau 'daily' (Harian)
        'amount',      // Nominal Rupiah
        'is_taxable',  // Boolean: Apakah kena PPh21?
        'is_active',   // Boolean: Status Aktif
        'description', // Text: Keterangan
        'position_id', // TAMBAHAN: Kolom Target Jabatan (Bisa Null untuk Global/Karyawan Spesifik)
        'user_id',     // TAMBAHAN TERBARU: Kolom Target Karyawan Spesifik (Bisa Null)
    ];

    /**
     * Casting Tipe Data.
     * Mengubah format data database menjadi tipe data PHP yang sesuai.
     */
    protected $casts = [
        'amount'     => 'double',  // Pastikan nominal dianggap angka (bukan string)
        'is_taxable' => 'boolean', // Ubah 1/0 jadi true/false
        'is_active'  => 'boolean', // Ubah 1/0 jadi true/false
    ];

    // =========================================================================
    // RELASI DATABASE
    // =========================================================================

    /**
     * Relasi ke tabel Positions (Jabatan)
     * Mengambil data target jabatan untuk tunjangan ini.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * RELASI BARU: Ke tabel Users (Karyawan)
     * Mengambil data target karyawan spesifik untuk tunjangan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    // =========================================================================
    // ACCESSORS (HELPER UNTUK VIEW / BLADE)
    // =========================================================================

    /**
     * Helper untuk menampilkan Label Tipe di UI.
     * Panggil di Blade: {{ $allowance->type_label }}
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'fixed' => 'Tetap (Bulanan)',
            'daily' => 'Harian (Kehadiran)',
            default => ucfirst($this->type),
        };
    }

    /**
     * Helper untuk menampilkan Label Pajak di UI.
     * Panggil di Blade: {{ $allowance->tax_label }}
     */
    public function getTaxLabelAttribute()
    {
        return $this->is_taxable ? 'Kena Pajak' : 'Non-Pajak';
    }
}