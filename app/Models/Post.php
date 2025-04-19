<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'expiry_date',
        'is_active',
        'from_date',
        'to_date',
        'country',
        'city',
    ];
    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
