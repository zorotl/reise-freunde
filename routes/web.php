<?php

use App\Models\Message;
use Livewire\Volt\Volt;
use App\Livewire\Mail\Inbox;
use App\Livewire\Mail\Outbox;
use App\Livewire\Post\MyPosts;
use App\Livewire\Post\EditPost;
use App\Livewire\Post\PostList;
use App\Livewire\Post\ShowPost;
use App\Livewire\Post\CreatePost;
use App\Livewire\Mail\MessageView;
use App\Livewire\User\UserProfile;
use App\Livewire\User\FollowersList;
use App\Livewire\User\FollowingList;
use App\Livewire\Mail\MessageCompose;
use Illuminate\Support\Facades\Route;
use App\Livewire\User\FollowRequestsList;
use App\Livewire\User\TravelStylePreferences;


Route::get('/', function () {
    return view('frontend.homepage');
})->name('home');

Route::get('/users', function () {
    return view('frontend.user-directory');
})->name('user.directory');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::get('/dashboard', function () {
    return redirect()->route('post.show');
})->name('dashboard');


Route::middleware(['auth'])->group(function () {
    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('settings/preferences', 'settings.preferences')->name('settings.preferences');
    Volt::route('settings/privacy-settings', 'settings.privacy-settings')->name('settings.privacy-settings');


    // Post routes (assuming they require auth)
    Route::get('/post/myown', MyPosts::class)->name('post.myown');
    Route::get('/post/create', CreatePost::class)->name('post.create');
    Route::get('/post/edit/{id}', EditPost::class)->name('post.edit');

    // Mail routes (assuming they require auth)
    Route::get('/mail/inbox', Inbox::class)->name('mail.inbox');
    Route::get('/mail/outbox', Outbox::class)->name('mail.outbox');
    Route::get('/mail/messages/{message}/{fromWhere}', MessageView::class)->name('mail.messages.view');
    Route::get('/mail/compose/{receiverId?}/{fixReceiver?}', MessageCompose::class)->name('mail.compose');
    Route::get('/mail', function () {
        return redirect()->route('mail.inbox');
    })->name('mail');

    // Follower/Following routes (require auth to view requests)
    Route::get('/user/follow-requests', FollowRequestsList::class)->name('user.follow-requests');
    Route::get('/user/{id}/followers', FollowersList::class)->name('user.followers'); // Keep ID for viewing others' lists
    Route::get('/user/{id}/following', FollowingList::class)->name('user.following'); // Keep ID for viewing others' lists
});

// Publicly accessible routes (or adjust middleware as needed)
Route::get('/post/show', PostList::class)->name('post.show');
Route::get('/post/{post}', ShowPost::class)->name('post.single');
Route::get('/post', function () {
    return redirect()->route('post.show');
})->name('post');

Route::get('/user/profile/{id}', UserProfile::class)->name('user.profile');

require __DIR__ . '/auth.php';






// ToDo for the whole project:

//     Fix the following issues:
//         - [ ] Fix redirect after editing a post from the my posts page
//         - [ ] Fix SiteTitle on all pages
//         - [ ] Fix header.blade.php - remove the php code and check out alternative
//         - [ ] Fix settings/preferences - custom travel styles and hobbies have to be loaded from the database and make them editable
//         - [ ] Fix user profile - make profile picture editable
//         - [ ] Fix user profile - reomve name and surname from the profile
//         - [ ] Optimize the new post form, especially the length of the text area with alpine.js
//         - [ ] Fix user hobby und travel style - view error in the form, save ist okay
//         - [ ] !!! Currently, I can deactivte, edit and delete a post, even if I am not the owner of the post

//     Overall:
//         - [ ] Implement a backend for the admin
//         - [ ] Admin should be able to edit and delete all posts
//         - [ ] Admin should be able to edit and delete all users
//         - [ ] Admin should be able to ban and unban users
//         - [ ] Admin should be able to create, update and delete hobbies
//         - [ ] Admin should be able to create, update and delete travel styles
//         - [ ] Implement a backend for the moderator
//         - [ ] Implement the feature verify email
//         - [ ] Implement a verified User System

//     Language (Instruction in Gemini and ChatGPT):
//         - [ ] Install laravel localization 
//         - [ ] Install german language pack
//         - [ ] Add a language switcher
//         - [ ] Replace all hardcoded strings with translatable strings 
//         - [ ] Translate all strings to german 

//     Security:
//         - [ ] Implement Laravel Gates to protect routes and admin functions
//         - [ ] Implement Laravel Policies to protect models
//         - [ ] Implement Laravel Socialite for social authentication ???
//         - [ ] Implement Laravel CSRF protection for forms

//     Testing:
//         - [x] Fix existing Pest-Testing
//         - [ ] Implement Pest-Testing for the whole project

//     Logged in user:
//         - [ ] Use is_admin 
//         - [ ] Use is_moderator 
//         - [ ] Use is_banned 
//         - [ ] Use is_banned_until 
//         - [ ] Use a profile picture 

//         - [ ] Add a notification system
//         - [ ] Implement a User Finding system
//         - [ ] Implement user picture upload

//      Posts
//         - [ ] Add picture from user to the post
//         - [ ] Add a comment system
//         - [ ] Add a like system
//         - [ ] Add a report system
//         - [ ] Add a search bar to the post
//         - [ ] Add a filter to the post

//     Frontend / not logged in user:
//         - [ ] Add more information to the homepage
//         - [ ] Make it difficult to use the app without being logged in