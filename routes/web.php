<?php

// ToDo for the whole project:

//     Fix the following issues:
//         - [ ] Fix redirect after editing a post from the my posts page
//         - [ ] Fix SiteTitle on all pages

//     Overall:
//         - [ ] Install german language pack
//         - [ ] Add a language switcher
//         - [ ] Replace all hardcoded strings with translatable strings 
//         - [ ] Translate all strings to german 
//         - [ ] Add a favicon

//     Backend:
//         - [ ] Use is_admin 
//         - [ ] Use is_moderator 
//         - [ ] Use is_banned 
//         - [ ] Use is_banned_until 
//         - [ ] Use a user_add_info model in posts
//         - [ ] Use a user_add_info model in settings
//         - [ ] Use a username 
//         - [ ] Use birthday 
//         - [ ] Use nationality 
//         - [ ] Use a profile picture 
//         - [ ] Use about_me 

//         - [x] Add From, To, City and Country to the post-blades create, edit?
//         - [x] Add From, To, City and Country to post-list?
//         - [ ] Add a search bar to the post
//         - [ ] Add a filter to the post

//         - [ ] Add a mail system
//         - [ ] Add a notification system
//         - [ ] Add a follow system
//         - [ ] Implement user picture upload
//         - [ ] Add picture from user to the post

//         - [ ] Add a Travel-Styles table und system
//         - [ ] Add a Travel-Styles pivot table
//         - [ ] OPTIONAL: Add a Sports&Fun table und system
//         - [ ] OPTIONAL: Add a Sports&Fun pivot-table


//     Frontend:
//         - [ ] Add a Homepage
//         - [ ] Add the Posts-Overview to the homepage


use App\Livewire\User\UserProfile;
use Livewire\Volt\Volt;
use App\Livewire\Post\PostList;
use App\Livewire\Post\MyPosts;
use App\Livewire\Post\CreatePost;
use App\Livewire\Post\EditPost;
use Illuminate\Support\Facades\Route;


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
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';


// Post routes
Route::get('/post/show', PostList::class)->name('post.show');
Route::get('/post/myown', MyPosts::class)->name('post.myown');
Route::get('/post/create', CreatePost::class)->name('post.create');
Route::get('/post/edit/{id}', EditPost::class)->name('post.edit');
Route::get('/post', function () {
    return redirect()->route('post.show');
})->name('post');

//User routes
Route::get('/user/profile/{id}', UserProfile::class)->name('user.profile');