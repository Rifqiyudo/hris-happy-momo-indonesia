<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal.
     * Diselaraskan dengan migrasi terbaru untuk mendukung integrasi penuh.
     */
    protected $fillable = [
        'code', 
        'name', 
        'pic_name',
        'location',     // Ditambahkan untuk kompatibilitas relasi
        'address', 
        'city',
        'province',
        'phone', 
        'email',
        'latitude', 
        'longitude', 
        'radius',
        'timezone',
        'is_active'
    ];

    /**
     * Konversi tipe data otomatis.
     * decimal:8 memastikan koordinat GPS tidak terpotong saat diproses Eloquent.
     */
    protected $casts = [
        'radius'    => 'integer',
        'latitude'  => 'decimal:8', 
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Menambahkan atribut virtual ke dalam serialisasi JSON/Array.
     */
    protected $appends = [
        'maps_link',
        'full_branch_name',
        'coordinates',
        'status_label',
        'full_location' // Aksesor tambahan untuk UI
    ];

    // =========================================================================
    // RELASI (RELATIONSHIPS)
    // =========================================================================

    /**
     * Relasi ke data Karyawan (User).
     * One-to-Many: Satu cabang memiliki banyak karyawan.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }

    /**
     * Relasi ke data Departemen.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'branch_id', 'id');
    }

    // =========================================================================
    // AKSESOR (ACCESSORS)
    // =========================================================================

    /**
     * Mengamankan pemanggilan 'location' oleh Model User.
     * Jika kolom 'location' kosong, otomatis mengambil dari city/province/address.
     */
    public function getLocationAttribute($value)
    {
        if (!empty($value)) return $value;
        
        $parts = array_filter([$this->city, $this->province]);
        if (count($parts) > 0) {
            return implode(', ', $parts);
        }

        return $this->address ?? 'Lokasi Belum Diset';
    }

    /**
     * Atribut untuk menampilkan alamat lengkap dan rapi di UI.
     */
    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->province]);
        return count($parts) > 0 ? implode(', ', $parts) : 'Alamat belum lengkap';
    }

    /**
     * Link Google Maps yang valid dan akurat.
     */
    public function getMapsLinkAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return '#';
    }

    /**
     * Menggabungkan Latitude dan Longitude untuk keperluan label peta.
     */
    public function getCoordinatesAttribute(): string
    {
        return ($this->latitude && $this->longitude) 
            ? "{$this->latitude}, {$this->longitude}" 
            : 'Koordinat belum diset';
    }

    /**
     * Nama Cabang lengkap dengan kode untuk dropdown UI atau Monitoring.
     * Output: "[HMI-SBY] Happy Momo Surabaya"
     */
    public function getFullBranchNameAttribute(): string
    {
        $code = $this->code ? "[{$this->code}] " : '';
        return "{$code}{$this->name}";
    }

    /**
     * Label Status untuk UI yang lebih informatif.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Non-Aktif';
    }

    // =========================================================================
    // SCOPES (FILTERING)
    // =========================================================================

    /**
     * Scope untuk mengambil cabang yang aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}