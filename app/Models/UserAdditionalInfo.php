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
        'profile_picture',
        'about_me',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
