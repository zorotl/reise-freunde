<?php

namespace App\Models;

use App\Observers\UserGrantObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(UserGrantObserver::class)]
class UserGrant extends Model
{
    /** @use HasFactory<\Database\Factories\UserGrantFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_admin',
        'is_moderator',
        'is_banned',
        'is_banned_until',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_moderator' => 'boolean',
        'is_banned' => 'boolean',
        'is_banned_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
