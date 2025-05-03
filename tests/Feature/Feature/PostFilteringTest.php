<?php

// use App\Models\User;
// use App\Models\Post;
// use App\Models\UserAdditionalInfo;
// use Livewire\Livewire;
// use App\Livewire\Post\PostList;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Str;
// use Illuminate\Foundation\Testing\RefreshDatabase;

// use function Pest\Laravel\actingAs;
// use function Pest\Laravel\assertDatabaseHas;
// use function Pest\Laravel\assertDatabaseMissing; // Hinzugefügt für Negativtests

// uses(RefreshDatabase::class);

// // Helper function (bleibt gleich)
// function createUserWithDetailsForFilterTest(array $details = []): User
// {
//     $baseUsername = 'testuser_' . Str::random(5);
//     $user = User::factory()->create(Arr::only($details, ['firstname', 'lastname']));
//     UserAdditionalInfo::factory()->create(array_merge(
//         [
//             'user_id' => $user->id,
//             'username' => $baseUsername . $user->id,
//             'birthday' => now()->subYears(30)->toDateString(), // Factory gibt nur Datumsteil zurück
//             'nationality' => 'CH',
//         ],
//         Arr::only($details, ['username', 'birthday', 'nationality'])
//     ));
//     return $user->load('additionalInfo');
// }


// test('can filter posts by user nationality', function () {
//     // Arrange
//     $user = User::factory()->create();
//     $user->additionalInfo()->create(['username' => 'acting_user_nat_' . $user->id]);
//     $userCH = createUserWithDetailsForFilterTest(['nationality' => 'CH', 'firstname' => 'Swiss']);
//     $userDE = createUserWithDetailsForFilterTest(['nationality' => 'DE', 'firstname' => 'German']);
//     $postCH = Post::factory()->create(['user_id' => $userCH->id, 'title' => 'Swiss Post', 'is_active' => true, 'expiry_date' => now()->addYear()]);
//     $postDE = Post::factory()->create(['user_id' => $userDE->id, 'title' => 'German Post', 'is_active' => true, 'expiry_date' => now()->addYear()]);

//     // Assert DB state before Livewire interaction
//     assertDatabaseHas('user_additional_infos', ['user_id' => $userDE->id, 'nationality' => 'DE']);
//     assertDatabaseHas('posts', ['id' => $postDE->id, 'user_id' => $userDE->id]);
//     assertDatabaseHas('user_additional_infos', ['user_id' => $userCH->id, 'nationality' => 'CH']);
//     assertDatabaseHas('posts', ['id' => $postCH->id, 'user_id' => $userCH->id]);


//     actingAs($user);

//     // Test the Livewire component
//     Livewire::test(PostList::class)
//         ->set('filterUserNationality', 'DE') // Filter setzen
//         ->assertSee('German Post') // Sollte deutschen Post sehen
//         ->assertDontSee('Swiss Post'); // Sollte schweizer Post NICHT sehen
// });


// test('can filter posts by user age range', function () {
//     // Arrange
//     $user = User::factory()->create();
//     $user->additionalInfo()->create(['username' => 'acting_user_age_' . $user->id]);

//     $today = Carbon::parse('2025-05-03'); // Festes Datum für Konsistenz
//     $birthday25 = $today->copy()->subYears(25)->toDateString(); // Ist 25
//     $birthday35 = $today->copy()->subYears(35)->toDateString(); // Ist 35

//     $user25 = createUserWithDetailsForFilterTest(['birthday' => $birthday25, 'firstname' => 'TwentyFive']);
//     $user35 = createUserWithDetailsForFilterTest(['birthday' => $birthday35, 'firstname' => 'ThirtyFive']);

//     $post25 = Post::factory()->create(['user_id' => $user25->id, 'title' => 'Post by 25yo', 'is_active' => true, 'expiry_date' => now()->addYear()]);
//     $post35 = Post::factory()->create(['user_id' => $user35->id, 'title' => 'Post by 35yo', 'is_active' => true, 'expiry_date' => now()->addYear()]);

//     // Assert DB state before Livewire interaction
//     assertDatabaseHas('user_additional_infos', ['user_id' => $user35->id, 'birthday' => $birthday35]); // Verwende den String 'YYYY-MM-DD'
//     assertDatabaseHas('posts', ['id' => $post35->id, 'user_id' => $user35->id]);
//     assertDatabaseHas('user_additional_infos', ['user_id' => $user25->id, 'birthday' => $birthday25]);
//     assertDatabaseHas('posts', ['id' => $post25->id, 'user_id' => $user25->id]);


//     actingAs($user);

//     // Test the Livewire component
//     Livewire::test(PostList::class)
//         ->set('filterMinAge', 30) // Filter setzen
//         ->set('filterMaxAge', 40)
//         ->assertSee('Post by 35yo') // Sollte 35j sehen
//         ->assertDontSee('Post by 25yo'); // Sollte 25j NICHT sehen
// }); 