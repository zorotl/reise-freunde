<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use Livewire\Livewire;

function createUserWithDetails(array $details = []): User
{
    $user = User::factory()->create([
        'firstname' => $details['firstname'] ?? fake()->firstName(),
        'lastname' => $details['lastname'] ?? fake()->lastName(),
        'status' => 'approved',
    ]);

    $user->additionalInfo()->create(array_merge(
        [
            'username' => $details['username'] ?? 'testuser' . $user->id,
            'birthday' => $details['birthday'] ?? now()->subYears(30)->toDateString(),
            'nationality' => $details['nationality'] ?? 'CH',
        ],
        $details['additionalInfo'] ?? []
    ));

    return $user->load('additionalInfo');
}


test('user search page is accessible to guests', function () {
    get(route('user.directory'))->assertOk();
});

test('user search page is accessible to authenticated users', function () {
    $user = User::factory()->create();
    actingAs($user)->get(route('user.directory'))->assertOk();
});

test('authenticated users can see search results', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'visible_user']);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->assertSee('visible_user');
});

test('filtering by name works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'look_me']);
    createUserWithDetails(['username' => 'hide_me']);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('search', 'look_me')
        ->assertSee('look_me')
        ->assertDontSee('hide_me');
});

test('filtering by nationality works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'ch_user', 'nationality' => 'CH']);
    createUserWithDetails(['username' => 'us_user', 'nationality' => 'US']);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterUserNationality', 'CH')
        ->assertSee('ch_user')
        ->assertDontSee('us_user');
});

test('filtering by minimum age works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'young_user', 'birthday' => now()->subYears(18)->toDateString()]);
    createUserWithDetails(['username' => 'old_user', 'birthday' => now()->subYears(50)->toDateString()]);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterMinAge', 30)
        ->assertSee('old_user')
        ->assertDontSee('young_user');
});

test('filtering by maximum age works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'young_user', 'birthday' => now()->subYears(18)->toDateString()]);
    createUserWithDetails(['username' => 'old_user', 'birthday' => now()->subYears(50)->toDateString()]);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterMaxAge', 25)
        ->assertSee('young_user')
        ->assertDontSee('old_user');
});

test('filtering by age range works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'young_user', 'birthday' => now()->subYears(20)->toDateString()]);
    createUserWithDetails(['username' => 'middle_user', 'birthday' => now()->subYears(35)->toDateString()]);
    createUserWithDetails(['username' => 'old_user', 'birthday' => now()->subYears(60)->toDateString()]);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterMinAge', 30)
        ->set('filterMaxAge', 40)
        ->assertSee('middle_user')
        ->assertDontSee('young_user')
        ->assertDontSee('old_user');
});

test('combining filters works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails([
        'username' => 'match',
        'birthday' => now()->subYears(30)->toDateString(),
        'nationality' => 'CH',
    ]);

    createUserWithDetails([
        'username' => 'no_match',
        'birthday' => now()->subYears(20)->toDateString(),
        'nationality' => 'US',
    ]);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterMinAge', 25)
        ->set('filterMaxAge', 35)
        ->set('filterUserNationality', 'CH')
        ->assertSee('match')
        ->assertDontSee('no_match');
});

test('clear filters button works', function () {
    $authUser = User::factory()->create();
    actingAs($authUser);

    createUserWithDetails(['username' => 'filtered_out', 'nationality' => 'US']);
    createUserWithDetails(['username' => 'visible', 'nationality' => 'CH']);

    Livewire::actingAs($authUser)
        ->test(\App\Livewire\User\Search::class)
        ->set('filterUserNationality', 'CH')
        ->assertSee('visible')
        ->call('updateFilters', [
            'filterUserNationality' => null,
        ])
        ->assertSee('filtered_out');
});


