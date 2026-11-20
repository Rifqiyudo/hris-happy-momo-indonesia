<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
        'asset_id', 
        'assigned_date', 
        'return_date', 
        'condition_notes'
    ];

    /**
     * The attributes that should be cast to native types.
     * * @var array<string, string>
     */
    protected $casts = [
        'assigned_date' => 'date',
        'return_date'   => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Get the user that owns the assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the asset associated with the assignment.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Scope: Hanya menampilkan aset yang belum dikembalikan (masih dipinjam).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('return_date');
    }
}