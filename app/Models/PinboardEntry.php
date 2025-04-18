<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PinboardEntry extends Model
{
    /** @use HasFactory<\Database\Factories\PinboardEntryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'expiry_date',
        'is_active',
    ];
    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
