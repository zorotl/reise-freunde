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
use App\Http\Middleware\AdminOrModeratorMiddleware;


Route::get('/', function () {
    return view('frontend.homepage');
})->name('home');

Volt::route('/users', 'user.search')->name('user.directory');

Volt::route('/dashboard', 'pages.dashboard.overview') // Points to the new Volt component
    ->middleware(['auth', 'verified']) // Keep existing middleware
    ->name('dashboard');

Volt::route('/banned', 'pages.banned')->middleware('auth')->name('banned');    // Route for banned users

Route::middleware(['auth'])->group(function () {
    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('settings/preferences', 'settings.preferences')->name('settings.preferences');
    Volt::route('settings/privacy-settings', 'settings.privacy-settings')->name('settings.privacy-settings');
    Volt::route('settings/account-deletion', 'settings.account-deletion')->name('settings.account-deletion');

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

// --- Admin/Moderator Routes ---
// Grouped under /admin and protected by the custom middleware
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', AdminOrModeratorMiddleware::class]) // Use the custom middleware alias
    ->group(function () {
        Volt::route('/', 'pages.admin.dashboard')->name('dashboard'); // Admin Dashboard    
        Volt::route('/users', 'pages.admin.users.index')->name('users');  // User Management Route
        Volt::route('/posts', 'pages.admin.posts.index')->name('posts');  // Post Management Route
        Volt::route('/posts/{post}/edit', 'admin.posts.edit-post')->name('posts.edit');
        Volt::route('/messages', 'pages.admin.messages.index')->name('messages');  // Message Management Route
        Volt::route('/messages/{messageId}', 'pages.admin.messages.show')->name('messages.show');  // Route for viewing a single message in admin panel
        Volt::route('/hobbies', 'pages.admin.hobbies.index')->name('hobbies'); // Hobby Management Route
        Volt::route('/travel-styles', 'pages.admin.travel-styles.index')->name('travel-styles'); // TravelStyle Management Route 
        Volt::route('/reports', 'pages.admin.reports.index')->name('reports'); // Add reports route       
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
//         - [ ] Fix SiteTitle on all pages
//         - [ ] Fix user hobby und travel style - view error in the form, save ist okay
//         - [ ] Fix FindUser find User by name, not exactly what i wanted

//     Overall:
//         - [ ] Implement the feature verify email (absolutely in the end of project)
//         - [ ] Implement a verified User System

//     Language (Instruction in Gemini and ChatGPT):
//         - [ ] Install laravel localization (static content)
//         - [ ] Add a language switcher front- and backend
//         - [ ] For save in more than one language, use an addon for laravel (e.g. spatie/laravel-translatable)
//         - [ ] Install a translater tool for pre-translation (e.g. Google Translate, DeepL API, LibreTranslate) for dynamic content

//     Security:
//         - [ ] Test the security of the app

//     Testing:
//         - [ ] Test continuing

//     Logged in user:
//         - [ ] Use is_banned 
//         - [ ] Use is_banned_until 
//         - [ ] Add a notification system

//      Posts
//         - [ ] ADMIN: Add from reported posts to ban the user

//      Inbox
//         - [ ] Change style to be more like post list or simply nicer than now
//         - [ ] Add a report system for messages

//     Frontend / not logged in user:
//         - [ ] Make it difficult to use the app without being logged in