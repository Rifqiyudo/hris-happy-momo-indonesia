<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name', 
        'serial_number', 
        'category', 
        'status', 
        'purchase_price', 
        'purchase_date',
        
        // --- TAMBAHAN BARU UNTUK SINKRONISASI DENGAN PROFIL KARYAWAN ---
        'user_id',       // ID karyawan pemegang aset saat ini
        'assigned_date'  // Tanggal penyerahan aset kepada karyawan
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date'  => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        
        // --- TAMBAHAN BARU ---
        'assigned_date'  => 'datetime',
        'user_id'        => 'integer',
    ];

    // =================================================================
    // RELASI DATABASE
    // =================================================================

    /**
     * Get the assignments for the asset. (History Lama)
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    /**
     * Helper: Mendapatkan riwayat penugasan terakhir. (History Lama)
     */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class)->latest();
    }

    /**
     * RELASI BARU: Karyawan (User) yang sedang memegang aset ini.
     * Terhubung langsung dengan master profil karyawan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // =================================================================
    // SCOPES (FILTERING)
    // =================================================================

    /**
     * Scope: Hanya aset yang tersedia (available / belum ada pemegangnya).
     * Diperbarui agar lebih akurat mengecek kolom user_id yang kosong.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->orWhereNull('user_id');
    }
}