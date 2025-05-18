<?php

namespace App\Models;

use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'body',
        'read_at',
        'sender_deleted_at',
        'receiver_deleted_at',
        'sender_archived_at',
        'receiver_archived_at',
        'sender_permanently_deleted_at',
        'receiver_permanently_deleted_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sender_deleted_at' => 'datetime',
        'receiver_deleted_at' => 'datetime',
        'sender_archived_at' => 'datetime',
        'receiver_archived_at' => 'datetime',
        'sender_permanently_deleted_at' => 'datetime',
        'receiver_permanently_deleted_at' => 'datetime',
    ];

    public function sender()
    {
        // Eager load additionalInfo by default when accessing sender
        return $this->belongsTo(User::class, 'sender_id')->withDefault(function ($user) {
            $user->firstname = 'Unknown'; // Provide default values if sender is somehow missing
            $user->lastname = 'User';
        })->withTrashed(); // Include soft-deleted senders if you want to see their names
    }

    public function receiver()
    {
        // Eager load additionalInfo by default when accessing receiver
        return $this->belongsTo(User::class, 'receiver_id')->withDefault(function ($user) {
            $user->firstname = 'Unknown';
            $user->lastname = 'User';
        })->withTrashed(); // Include soft-deleted receivers
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // Helper method to check if a message is deleted by the current authenticated user
    public function isDeletedByCurrentUser(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        if (Auth::id() === $this->sender_id) {
            return (bool) $this->sender_deleted_at;
        }
        if (Auth::id() === $this->receiver_id) {
            return (bool) $this->receiver_deleted_at;
        }
        return false; // User is neither sender nor receiver
    }

    // Helper method to check if a message is archived by the current authenticated user
    public function isArchivedByCurrentUser(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        if (Auth::id() === $this->sender_id) {
            return (bool) $this->sender_archived_at;
        }
        if (Auth::id() === $this->receiver_id) {
            return (bool) $this->receiver_archived_at;
        }
        return false;
    }
}