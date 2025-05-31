<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAdditionalInfo extends Model
{
    /** @use HasFactory<\Database\Factories\UserAdditionalInfoFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'birthday',
        'nationality',
        'profile_picture_path',
        'about_me',
        'is_private',
        // 'custom_travel_styles',
        // 'custom_hobbies',
    ];

    protected $casts = [
        'birthday' => 'date',
        'is_private' => 'boolean',
        // 'custom_travel_styles' => 'array',
        // 'custom_hobbies' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}