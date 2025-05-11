<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'id_document_path',
        'social_links',
        'note',
        'status',
        'reviewed_by',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
