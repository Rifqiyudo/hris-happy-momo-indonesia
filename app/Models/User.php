<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// --- IMPORT SPATIE ACTIVITY LOG ---
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    // Tambahkan trait LogsActivity di sini
    use HasFactory, Notifiable, LogsActivity;

    /**
     * Mass Assignable Attributes.
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'nik', 'phone', 
        'address', 'domicile', 'avatar', 'sex', 'religion', 
        'last_education', 'educational_major', 'marital_status', 'children_count', 
        'family_name', 'emergency_contact_name', 'emergency_contact_phone',
        'language', 'birth_place', 'birth_date', 'branch_id', 
        'position_id', 'department_id', 'shift_id', 'join_date', 
        'resign_date', 'is_active', 'employee_state', 'employee_status', 
        'contract_duration', 'ktp_no', 'npwp_no', 'bank_name', 'bank_account_no', 
        'bpjs_kesehatan_no', 'bpjs_ketenagakerjaan_no', 'bpjs_kesehatan_deduction',
        'bpjs_ketenagakerjaan_deduction', 'late_deduction',          
        'late_tolerance', 'barcode_token' 
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'barcode_token', 
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'join_date' => 'date',
        'birth_date' => 'date',
        'resign_date' => 'date',
        'contract_duration' => 'integer',
        'bpjs_kesehatan_deduction' => 'decimal:2',
        'bpjs_ketenagakerjaan_deduction' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'children_count' => 'integer',
        'late_tolerance' => 'integer',
        'nik' => 'string',
    ];

    protected $appends = [
        'avatar_url', 
        'age', 
        'service_period', 
        'unit_kerja',      // Atribut dinamis cabang
        'lokasi_cabang',   // Atribut dinamis lokasi cabang
        'qrcode_url',
        'today_attendance',
        'contract_expiry_date',
        'remaining_leave',
        'total_asset_value'
    ];

    // =================================================================
    // KONFIGURASI AUDIT LOG (SPATIE ACTIVITYLOG)
    // =================================================================
    
    /**
     * Mengatur bagaimana aktivitas perubahan data user ini dicatat.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()          // Otomatis mencatat perubahan pada semua atribut di dalam $fillable
            ->logOnlyDirty()         // Hanya mencatat kolom yang datanya benar-benar berubah (memperhemat storage)
            ->dontSubmitEmptyLogs()  // Mencegah log kosong jika user menekan tombol save tanpa mengubah data
            ->useLogName('employee_data') // Nama modul log untuk mempermudah filter di halaman Audit
            ->setDescriptionForEvent(fn(string $eventName) => "Data profil karyawan telah di-{$eventName}");
    }

    // =================================================================
    // RELASI DATABASE
    // =================================================================

    public function branch(): BelongsTo 
    { 
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withDefault([
            'name' => 'UNIT BELUM TERDAFTAR',
            'code' => 'N/A'
        ]); 
    }

    public function branchDetail(): BelongsTo 
    { 
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withDefault([
            'name' => 'UNIT BELUM TERDAFTAR',
            'code' => 'N/A'
        ]); 
    }

    public function position(): BelongsTo 
    { 
        return $this->belongsTo(Position::class, 'position_id', 'id')->withDefault([
            'name' => 'STAFF'
        ]); 
    }
    
    public function department(): BelongsTo 
    { 
        return $this->belongsTo(Department::class, 'department_id', 'id')->withDefault([
            'name' => 'UMUM'
        ]); 
    }
    
    public function shift(): BelongsTo { return $this->belongsTo(Shift::class, 'shift_id'); }
    public function salary(): HasOne { return $this->hasOne(Salary::class); }
    public function payrolls(): HasMany { return $this->hasMany(Payroll::class); }
    public function attendances(): HasMany { return $this->hasMany(Attendance::class); }
    public function leaves(): HasMany { return $this->hasMany(Leave::class); }
    public function documents(): HasMany { return $this->hasMany(EmployeeDocument::class); }
    
    /**
     * [TAMBAHAN BARU/PENGEMBALIAN] 
     * Relasi bawaan 'assets' dikembalikan agar view index karyawan tidak error
     */
    public function assets(): HasMany 
    { 
        return $this->hasMany(EmployeeAsset::class, 'user_id'); 
    }

    /**
     * Relasi ke History Aset Karyawan (Lama)
     */
    public function employeeAssets(): HasMany 
    { 
        return $this->hasMany(EmployeeAsset::class, 'user_id'); 
    }

    public function currentAssets(): HasMany
    {
        return $this->hasMany(EmployeeAsset::class, 'user_id')->where('status', 'used');
    }

    /**
     * RELASI BARU: Ke Master Manajemen Aset (Assets)
     * Mengambil daftar aset perusahaan yang saat ini sedang dipegang oleh karyawan ini.
     */
    public function masterAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'user_id', 'id');
    }

    public function messages(): HasMany { return $this->hasMany(Message::class, 'sender_id'); }
    public function receivedMessages(): HasMany { return $this->hasMany(Message::class, 'receiver_id'); }

    public function leaveAllowance(): HasOne 
    { 
        return $this->hasOne(LeaveAllowance::class)->where('year', date('Y')); 
    }

    // =================================================================
    // AKSESOR (DYNAMIC ATTRIBUTES)
    // =================================================================

    public function getUnitKerjaAttribute(): string
    {
        $targetBranch = $this->branch ?? $this->branchDetail;
        return $targetBranch && $targetBranch->name ? strtoupper($targetBranch->name) : 'UNIT BELUM TERDAFTAR';
    }

    public function getLokasiCabangAttribute(): string
    {
        $targetBranch = $this->branch ?? $this->branchDetail;
        if ($targetBranch) {
            $location = $targetBranch->location ?? $targetBranch->city ?? $targetBranch->address ?? null;
            return $location ? strtoupper($location) : 'N/A';
        }
        return 'N/A';
    }

    public function getServicePeriodAttribute(): string
    {
        if (!$this->join_date) return '0 Hari';
        $endDate = $this->resign_date ?? now();
        $diff = $this->join_date->diff($endDate);
        
        $parts = [];
        if ($diff->y > 0) $parts[] = "{$diff->y} Thn";
        if ($diff->m > 0) $parts[] = "{$diff->m} Bln";
        if ($diff->d > 0 && $diff->y == 0) $parts[] = "{$diff->d} Hari";
        
        return count($parts) > 0 ? implode(', ', $parts) : 'Baru Bergabung';
    }

    public function getRemainingLeaveAttribute(): int
    {
        $quota = $this->leaveAllowance->quota ?? 0;
        
        $used = $this->leaves()
            ->where('type', 'Cuti')
            ->where('status', 'approved')
            ->whereYear('start_date', date('Y'))
            ->get()
            ->sum(function($leave) {
                return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            });

        return max(0, $quota - $used);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=4f46e5&color=fff&bold=true";
    }

    public function getContractExpiryDateAttribute()
    {
        if ($this->employee_status === 'Contract' && $this->join_date && $this->contract_duration) {
            return Carbon::parse($this->join_date)->addMonths($this->contract_duration);
        }
        return null;
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    public function getQrcodeUrlAttribute(): string
    {
        return "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=" . urlencode($this->nik);
    }

    public function getTodayAttendanceAttribute()
    {
        return $this->attendances()->whereDate('date', Carbon::today())->first();
    }

    /**
     * Mengkalkulasi nilai total aset berdasarkan master aset perusahaan.
     */
    public function getTotalAssetValueAttribute(): float
    {
        // Menjumlahkan harga perolehan (purchase_price) dari aset yang sedang dipegang
        return $this->masterAssets()->sum('purchase_price') ?? 0;
    }

    // =================================================================
    // QUERY SCOPES (FILTERING & SEARCHING)
    // =================================================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNull('resign_date');
    }

    public function scopeSearch(Builder $query, $term): Builder
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('nik', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('educational_major', 'like', "%{$term}%");
        });
    }

    // ROLE HELPERS
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isPic(): bool { return $this->role === 'pic'; }
    public function isEmployee(): bool { return $this->role === 'employee'; }
}