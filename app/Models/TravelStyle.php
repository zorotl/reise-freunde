<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelStyle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'icon'];

    protected $casts = [
        'deleted_at' => 'datetime', // <-- Cast deleted_at to a Carbon instance
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
