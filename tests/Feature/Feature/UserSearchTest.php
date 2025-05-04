<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use Livewire\Livewire;

// Helper to create a user with specific details
function createUserWithDetails(array $details = []): User
{
    $user = User::factory()->create(Arr::only($details, ['firstname', 'lastname']));

    if (isset($details['additionalInfo'])) {
        $username = $details['additionalInfo']['username'] ?? 'testuser' . $user->id;
    } else {
        $username = 'testuser' . $user->id;
    }

    $user->additionalInfo()->create(array_merge(
        [
            'username' => $username,
            'birthday' => now()->subYears(30)->toDateString(), // Default age 30
            'nationality' => 'CH', // Default Swiss
        ],
        Arr::only($details, ['username', 'birthday', 'nationality'])
    ));

    return $user->load('additionalInfo');
}

test('user search page is accessible to guests', function () {
    get(route('user.directory'))
        ->assertOk()
        // Assert the Livewire component by its view name
        ->assertSeeLivewire('user.search')
        ->assertSee(__('Login Required!'));
});

test('user search page is accessible to authenticated users', function () {
    $user = User::factory()->create();
    actingAs($user)
        ->get(route('user.directory'))
        ->assertOk()
        // Assert the Livewire component by its view name
        ->assertSeeLivewire('user.search')
        ->assertDontSee(__('Login Required!'));
});

test('authenticated users can see search results', function () {
    $user = User::factory()->create();
    $searchUser = createUserWithDetails(['firstname' => 'Searchable']);

    actingAs($user);

    // Test the component using its view name
    Livewire::test('user.search')
        ->assertSee($searchUser->additionalInfo->username);
});

test('filtering by name works', function () {
    $user = User::factory()->create();
    $user1 = createUserWithDetails(['firstname' => 'Alice']);
    $user2 = createUserWithDetails(['lastname' => 'Bobson']);
    $user3 = createUserWithDetails(['firstname' => 'Charlie', 'lastname' => 'Hagenes', 'additionalInfo' => ['username' => 'search_me']]);

    actingAs($user);

    // --- Test filtering for 'Ali' ---
    Livewire::test('user.search')
        ->set('search', 'Ali')
        ->assertSee($user1->additionalInfo->username)
        ->assertDontSee($user2->additionalInfo->username)
        ->assertDontSee($user3->additionalInfo->username); // Re-check this assertion

    // --- Test filtering for 'bson' ---
    Livewire::test('user.search')
        ->set('search', 'bson') // Apply filter directly
        ->assertDontSee($user1->additionalInfo->username)
        ->assertSee($user2->additionalInfo->username)
        ->assertDontSee($user3->additionalInfo->username);

    // --- Test filtering for 'search_me' (username) ---
    Livewire::test('user.search')
        ->set('search', 'search_me') // Apply filter directly
        ->assertDontSee($user1->additionalInfo->username)
        ->assertDontSee($user2->additionalInfo->username)
        ->assertSee($user3->additionalInfo->username);
});

// Apply the change Livewire::test('user.search') to ALL subsequent tests in this file...

test('filtering by nationality works', function () {
    $user = User::factory()->create();
    $swissUser = createUserWithDetails(['nationality' => 'CH', 'firstname' => 'Swiss']);
    $germanUser = createUserWithDetails(['nationality' => 'DE', 'firstname' => 'German']);

    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->set('nationality', 'DE')
        ->assertSee($germanUser->additionalInfo->username)
        ->assertDontSee($swissUser->additionalInfo->username);
});

test('filtering by minimum age works', function () {
    $user = User::factory()->create();
    $user25 = createUserWithDetails(['birthday' => now()->subYears(25)->toDateString(), 'firstname' => 'TwentyFive']);
    $user35 = createUserWithDetails(['birthday' => now()->subYears(35)->toDateString(), 'firstname' => 'ThirtyFive']);

    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->set('min_age', 30)
        ->assertSee($user35->additionalInfo->username)
        ->assertDontSee($user25->additionalInfo->username);
});

test('filtering by maximum age works', function () {
    $user = User::factory()->create();
    $user25 = createUserWithDetails(['birthday' => now()->subYears(25)->toDateString(), 'firstname' => 'TwentyFive']);
    $user35 = createUserWithDetails(['birthday' => now()->subYears(35)->toDateString(), 'firstname' => 'ThirtyFive']);

    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->set('max_age', 30)
        ->assertSee($user25->additionalInfo->username)
        ->assertDontSee($user35->additionalInfo->username);
});

test('filtering by age range works', function () {
    $user = User::factory()->create();
    $user25 = createUserWithDetails(['birthday' => now()->subYears(25)->toDateString(), 'firstname' => 'TwentyFive']);
    $user35 = createUserWithDetails(['birthday' => now()->subYears(35)->toDateString(), 'firstname' => 'ThirtyFive']);
    $user45 = createUserWithDetails(['birthday' => now()->subYears(45)->toDateString(), 'firstname' => 'FortyFive']);

    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->set('min_age', 30)
        ->set('max_age', 40)
        ->assertDontSee($user25->additionalInfo->username)
        ->assertSee($user35->additionalInfo->username)
        ->assertDontSee($user45->additionalInfo->username);
});

test('combining filters works', function () {
    $user = User::factory()->create();
    $swissUserYoung = createUserWithDetails(['firstname' => 'YoungSwiss', 'nationality' => 'CH', 'birthday' => now()->subYears(22)->toDateString()]);
    $swissUserOld = createUserWithDetails(['firstname' => 'OldSwiss', 'nationality' => 'CH', 'birthday' => now()->subYears(40)->toDateString()]);
    $germanUserYoung = createUserWithDetails(['firstname' => 'YoungGerman', 'nationality' => 'DE', 'birthday' => now()->subYears(23)->toDateString()]);

    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->set('search', 'Swiss')
        ->set('nationality', 'CH')
        ->set('min_age', 30)
        ->assertDontSee($swissUserYoung->additionalInfo->username)
        ->assertSee($swissUserOld->additionalInfo->username)
        ->assertDontSee($germanUserYoung->additionalInfo->username);
});

test('clear filters button works', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->set('search', 'test')
        ->set('nationality', 'DE')
        ->set('min_age', 20)
        ->set('max_age', 30)
        ->call('resetFilters')
        ->assertSet('search', '')
        ->assertSet('nationality', null)
        ->assertSet('min_age', null)
        ->assertSet('max_age', null)
        ->assertDispatched('reset-nationality-select');
});

test('pagination works', function () {
    $user = User::factory()->create();
    User::factory(20)->create()->each(function ($u) {
        $u->additionalInfo()->create(UserAdditionalInfo::factory()->raw());
    });

    actingAs($user);

    Livewire::test('user.search') // Changed here
        ->assertViewHas('users', function ($users) {
            return $users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->count() === 15;
        })
        ->call('gotoPage', 2, 'page')
        ->assertViewHas('users', function ($users) {
            return $users->count() > 0 && $users->count() <= 15;
        });
});