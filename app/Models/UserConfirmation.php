<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'confirmer_id',
        'status',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmer_id');
    }
}
