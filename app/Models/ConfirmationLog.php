<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfirmationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'confirmation_id',
        'action',
        'comment',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function confirmation()
    {
        return $this->belongsTo(UserConfirmation::class, 'confirmation_id');
    }
}
