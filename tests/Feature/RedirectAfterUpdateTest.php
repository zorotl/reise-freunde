<?php

use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

it('redirects to my-posts after editing from my-posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Post\EditPost::class, ['id' => $post->id, 'origin' => 'my'])
        ->set('title', 'Updated Title')
        ->set('content', str_repeat('Updated content ', 5))
        ->set('expiryDate', now()->addDays(10)->format('Y-m-d'))
        ->set('fromDate', now()->addDays(1)->format('Y-m-d'))
        ->set('toDate', now()->addDays(5)->format('Y-m-d'))
        ->call('update')
        ->assertRedirect('/myown');
});

it('redirects to posts after editing from all-posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Post\EditPost::class, ['id' => $post->id, 'origin' => 'all'])
        ->set('title', 'Updated Title')
        ->set('content', str_repeat('Updated content ', 5))
        ->set('expiryDate', now()->addDays(10)->format('Y-m-d'))
        ->set('fromDate', now()->addDays(1)->format('Y-m-d'))
        ->set('toDate', now()->addDays(5)->format('Y-m-d'))
        ->call('update')
        ->assertRedirect('/post/show');
});