<?php

// ToDo for the whole project:

//     Fix the following issues:
//         - [ ] Fix redirect after editing a post from the my posts page
//         - [ ] Fix SiteTitle on all pages
//         - [ ] Fix header.blade.php - remove the php code and check out alternative

//     Overall:
//         - [ ] 

//     Language:
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

//     Backend:
//         - [ ] Use is_admin 
//         - [ ] Use is_moderator 
//         - [ ] Use is_banned 
//         - [ ] Use is_banned_until 
//         - [ ] Use a user_add_info model in posts
//         - [ ] Use a user_add_info model in settings
//         - [ ] Use a profile picture 

//         - [ ] Add a search bar to the post
//         - [ ] Add a filter to the post

//         - [ ] Add a notification system
//         - [ ] OPTIONAL: Add a follow system
//         - [ ] Implement user picture upload
//         - [ ] Add picture from user to the post

//         - [ ] Add a Travel-Styles table und system
//         - [ ] Add a Travel-Styles pivot table
//         - [ ] OPTIONAL: Add a Sports&Fun table und system
//         - [ ] OPTIONAL: Add a Sports&Fun pivot-table


//     Frontend:
//         - [ ] Add a Homepage
//         - [ ] Add the Posts-Overview to the homepage


use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\User\UserProfile;
use App\Livewire\User\FollowersList;
use App\Livewire\User\FollowingList;
use App\Livewire\User\FollowRequestsList;
use App\Livewire\User\TravelStylePreferences;
use App\Livewire\Post\PostList;
use App\Livewire\Post\MyPosts;
use App\Livewire\Post\CreatePost;
use App\Livewire\Post\EditPost;
use App\Livewire\Mail\Inbox;
use App\Livewire\Mail\Outbox;
use App\Livewire\Mail\MessageView;
use App\Livewire\Mail\MessageCompose;
use App\Models\Message;


Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    // Route::get('/settings/preferences', TravelStylePreferences::class)->name('settings.preferences');

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
Route::get('/post', function () {
    return redirect()->route('post.show');
})->name('post');

Route::get('/user/profile/{id}', UserProfile::class)->name('user.profile');

require __DIR__ . '/auth.php';



