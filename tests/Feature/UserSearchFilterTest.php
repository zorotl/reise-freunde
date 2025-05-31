<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Hobby;
use App\Models\Language;
use App\Models\TravelStyle;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Create user with interests + approved/granted state
function createUserWithFilters(array $attrs = [], array $relations = []): User
{
    $user = User::factory()->create(array_merge([
        'status' => 'approved',
        'email_verified_at' => now(),
        'approved_at' => now(),
    ], $attrs));

    UserGrant::factory()->create([
        'user_id' => $user->id,
        'is_banned' => false,
    ]);

    // Create username via additionalInfo
    $user->additionalInfo()->create([
        'username' => strtolower($user->firstname),
        'birthday' => now()->subYears(25),
        'nationality' => 'CH',
    ]);

    if (isset($relations['languages'])) {
        $user->spokenLanguages()->syncWithoutDetaching($relations['languages']);
    }

    if (isset($relations['hobbies'])) {
        $user->hobbies()->syncWithoutDetaching($relations['hobbies']);
    }

    if (isset($relations['travelStyles'])) {
        $user->travelStyles()->syncWithoutDetaching($relations['travelStyles']);
    }

    return $user->fresh(['grant', 'additionalInfo']);
}

// Viewer = neutral test user not meant to appear in filters
function createViewerUser(): User
{
    $viewer = User::factory()->create([
        'status' => 'approved',
        'email_verified_at' => now(),
        'approved_at' => now(),
    ]);

    UserGrant::factory()->create([
        'user_id' => $viewer->id,
        'is_banned' => false,
    ]);

    $viewer->additionalInfo()->create([
        'username' => 'viewer',
        'birthday' => now()->subYears(30),
        'nationality' => 'DE',
    ]);

    return $viewer;
}

it('can filter users by spoken language', function () {
    $language = Language::factory()->create(['code' => 'en']);

    $filteredUser = createUserWithFilters(['firstname' => 'Margarett'], [
        'languages' => ['en']
    ]);

    $viewer = createViewerUser();

    Livewire::actingAs($viewer)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterLanguages', ['en'])
        ->assertSee($filteredUser->additionalInfo->username);
});

it('can filter users by hobby', function () {
    $hobby = Hobby::factory()->create(['name' => 'Fishing']);

    $filteredUser = createUserWithFilters(['firstname' => 'Cyril'], [
        'hobbies' => [$hobby->id]
    ]);

    $viewer = createViewerUser();

    Livewire::actingAs($viewer)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterHobbies', [$hobby->id])
        ->assertSee($filteredUser->additionalInfo->username);
});

it('can filter users by travel style', function () {
    $style = TravelStyle::factory()->create(['name' => 'Backpacking']);

    $filteredUser = createUserWithFilters(['firstname' => 'Rogers'], [
        'travelStyles' => [$style->id]
    ]);

    $viewer = createViewerUser();

    Livewire::actingAs($viewer)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterTravelStyles', [$style->id])
        ->assertSee($filteredUser->additionalInfo->username);
});

it('can filter users by gender and age', function () {
    $filteredUser = createUserWithFilters(['firstname' => 'Paula', 'gender' => 'female']);

    $viewer = createViewerUser();

    Livewire::actingAs($viewer)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterGender', 'female')
        ->set('filterMinAge', 18)
        ->set('filterMaxAge', 30)
        ->assertSee($filteredUser->additionalInfo->username);
});
