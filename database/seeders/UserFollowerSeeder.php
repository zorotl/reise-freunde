<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserFollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Stelle sicher, dass genügend Benutzer existieren
        if (User::count() < 5) {
            User::factory(5)->create();
        }

        User::whereIn('id', range(1, 5))->get()->each(function ($user) {
            // Erstelle zufällige Follow-Beziehungen (akzeptiert)
            $usersToFollow = User::where('id', '!=', $user->id)
                ->inRandomOrder()
                ->take(rand(0, 3))
                ->get();

            foreach ($usersToFollow as $userToFollow) {
                // Verhindere doppelte Einträge
                if (!$user->isFollowing($userToFollow)) {
                    $user->following()->attach($userToFollow->id, ['accepted_at' => now()]);
                }
            }

            // Erstelle zufällige ausstehende Follow-Anfragen (gesendet von diesem Benutzer)
            $pendingFollows = User::where('id', '!=', $user->id)
                ->whereNotIn('id', $user->following()->pluck('following_user_id')) // Korrigierte Abfrage
                ->inRandomOrder()
                ->take(rand(0, 2))
                ->get();

            foreach ($pendingFollows as $pendingUser) {
                // Verhindere doppelte Anfragen
                if (!$user->hasSentFollowRequestTo($pendingUser)) {
                    $user->pendingFollowingRequests()->attach($pendingUser->id);
                }
            }
        });
    }
}