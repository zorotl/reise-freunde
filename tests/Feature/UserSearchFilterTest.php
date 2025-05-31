<?php

use App\Models\User;
use App\Models\Language;
use App\Models\Hobby;
use App\Models\TravelStyle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper to create a user with required fields and relations
function createUserWithFilters(array $attrs = [], array $relations = []): User
{
    $user = User::factory()->create($attrs);

    // Important: attach() expects pivot keys matching the pivot table
    if (isset($relations['languages'])) {
        // Since language_user expects language_code, pass the codes
        $user->spokenLanguages()->attach($relations['languages']);
    }

    if (isset($relations['hobbies'])) {
        $user->hobbies()->attach($relations['hobbies']);
    }

    if (isset($relations['travelStyles'])) {
        $user->travelStyles()->attach($relations['travelStyles']);
    }

    return $user;
}

it('can filter users by spoken language', function () {
    $language = Language::factory()->create(['code' => 'en']);
    $user = createUserWithFilters([], [
        'languages' => ['en']
    ]);

    Livewire\Livewire::test(\App\Livewire\User\Search::class)
        ->set('filterLanguages', ['en'])
        ->assertSee($user->firstname); // or full name if available
});

it('can filter users by hobby', function () {
    $hobby = Hobby::factory()->create(['name' => 'Fishing']);
    $user = createUserWithFilters([], ['hobbies' => [$hobby->id]]);

    Livewire\Livewire::test(\App\Livewire\User\Search::class)
        ->set('filterHobbies', [$hobby->id])
        ->assertSee($user->firstname);
});

it('can filter users by travel style', function () {
    $style = TravelStyle::factory()->create(['name' => 'Backpacking']);
    $user = createUserWithFilters([], ['travelStyles' => [$style->id]]);

    Livewire\Livewire::test(\App\Livewire\User\Search::class)
        ->set('filterTravelStyles', [$style->id])
        ->assertSee($user->firstname);
});

it('can filter users by gender and age', function () {
    $user = User::factory()->create([
        'firstname' => 'Paula',
        'lastname' => 'Stroman',
        'gender' => 'female',
        'status' => 'approved',
    ]);

    $user->additionalInfo()->create([
        'birthday' => now()->subYears(25)->toDateString(),
    ]);

    Livewire\Livewire::test(\App\Livewire\User\Search::class)
        ->set('filterGender', 'female')
        ->set('filterMinAge', 18)
        ->set('filterMaxAge', 30)
        ->assertSee('Paula');
});
