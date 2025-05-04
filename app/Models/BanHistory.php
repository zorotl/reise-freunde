<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'banned_by',
        'reason',
        'banned_at', // Add banned_at here if you set it manually
        'expires_at',
    ];

    protected $casts = [
        'banned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user who was banned.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (admin/mod) who initiated the ban.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}