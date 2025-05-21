<?php

namespace Tests\Feature;

use App\Livewire\Post\PostList;
use App\Livewire\PostFilters;
use App\Models\Post;
use App\Models\User;
use App\Models\UserAdditionalInfo;
use Carbon\Carbon;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user1 = User::factory()->create();
    $this->user1->additionalInfo()->save(UserAdditionalInfo::factory()->make([
        'nationality' => 'CH',
        'birthday' => now()->subYears(25)->toDateString(),
    ]));

    $this->user2 = User::factory()->create();
    $this->user2->additionalInfo()->save(UserAdditionalInfo::factory()->make([
        'nationality' => 'DE',
        'birthday' => now()->subYears(30)->toDateString(),
    ]));

    $this->user3 = User::factory()->create();
    $this->user3->additionalInfo()->save(UserAdditionalInfo::factory()->make([
        'nationality' => 'FR',
        'birthday' => now()->subYears(35)->toDateString(),
    ]));

    $this->post1 = Post::factory()->for($this->user1)->create([
        'title' => 'Post One - US',
        'country' => 'US',
        'city' => 'New York',
        'from_date' => now()->addDays(5)->toDateString(),
        'to_date' => now()->addDays(10)->toDateString(),
        'is_active' => true,
        'expiry_date' => now()->addDays(30)->toDateString(),
    ]);

    $this->post2 = Post::factory()->for($this->user2)->create([
        'title' => 'Post Two - DE',
        'country' => 'DE',
        'city' => 'Berlin',
        'from_date' => now()->addDays(15)->toDateString(),
        'to_date' => now()->addDays(20)->toDateString(),
        'is_active' => true,
        'expiry_date' => now()->addDays(30)->toDateString(),
    ]);

    $this->post3 = Post::factory()->for($this->user3)->create([
        'title' => 'Post Three - FR',
        'country' => 'FR',
        'city' => 'Paris',
        'from_date' => now()->addDays(2)->toDateString(),
        'to_date' => now()->addDays(7)->toDateString(),
        'is_active' => true,
        'expiry_date' => now()->addDays(30)->toDateString(),
    ]);

    $this->inactivePost = Post::factory()->for($this->user1)->create([
        'title' => 'Inactive Post',
        'country' => 'CH',
        'city' => 'Bern',
        'is_active' => false,
        'expiry_date' => now()->addDays(30)->toDateString(),
    ]);

    $this->expiredPost = Post::factory()->for($this->user1)->create([
        'title' => 'Expired Post',
        'country' => 'CH',
        'city' => 'Zurich',
        'is_active' => true,
        'expiry_date' => now()->subDay()->toDateString(),
    ]);
});

test('post list page contains livewire post filters component', function () {
    get(route('post.show'))
        ->assertOk()
        ->assertSeeLivewire(PostList::class)
        ->assertSeeLivewire(PostFilters::class);
});

test('post list initially displays active and non-expired posts', function () {
    Livewire::test(PostList::class)
        ->assertSee($this->post1->title)
        ->assertSee($this->post2->title)
        ->assertSee($this->post3->title)
        ->assertDontSee($this->inactivePost->title)
        ->assertDontSee($this->expiredPost->title);
});