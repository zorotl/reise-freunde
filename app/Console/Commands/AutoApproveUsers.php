<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoApproveUsers extends Command
{
    protected $signature = 'users:auto-approve';
    protected $description = 'Approve users who have waited more than 36 hours';

    public function handle(): void
    {
        $users = User::where('status', 'pending')
            ->where('email_verified_at', '!=', null)
            ->where('created_at', '<=', now()->subHours(36))
            ->get();

        foreach ($users as $user) {
            $user->status = 'approved';
            $user->approved_at = now();
            $user->save();
            $this->info("Auto-approved user: {$user->email}");
        }
    }
}
