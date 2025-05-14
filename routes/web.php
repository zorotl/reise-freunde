<?php

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
use App\Livewire\Verification\Wait;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Livewire\User\FollowRequestsList;
use App\Livewire\User\TravelStylePreferences;
use App\Http\Middleware\AdminOrModeratorMiddleware;

// Publicly accessible routes
Route::get('/', function () {
    return view('frontend.homepage');
})->name('home');

Route::post('/set-locale', function () {
    $locale = request('locale');
    if (in_array($locale, ['en', 'de'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('set-locale');

Volt::route('/users', 'user.search')->name('user.directory');

Route::get('/verification/wait', Wait::class)->middleware(['auth'])->name('verification.wait');
Route::get('/user/profile/{id}', UserProfile::class)->name('user.profile');

// --- Banned User Route
Volt::route('/banned', 'pages.banned')->middleware('auth', 'check_banned')->name('banned');    // Route for banned users

// --- Authenticated Routes ---
Route::middleware(['auth', 'verified', 'check_banned'])->group(function () {

    Volt::route('/dashboard', 'pages.dashboard.overview')->name('dashboard');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('settings/preferences', 'settings.preferences')->name('settings.preferences');
    Volt::route('settings/privacy-settings', 'settings.privacy-settings')->name('settings.privacy-settings');
    Volt::route('settings/account-deletion', 'settings.account-deletion')->name('settings.account-deletion');

    // Post routes
    Route::get('/post/myown', MyPosts::class)->name('post.myown');
    Route::get('/post/create', CreatePost::class)->name('post.create');
    Route::get('/post/edit/{id}', EditPost::class)->name('post.edit');

    // Mail routes
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

}); // End of 'auth', 'verified', 'check_banned' group

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/profile/verify', \App\Livewire\Profile\Verify::class)->name('profile.verify');
    Route::get('/profile/confirmations', \App\Livewire\Profile\ConfirmationInbox::class)->middleware(['auth'])->name('profile.confirmations');
});


Route::get('/post/show', PostList::class)->name('post.show');
Route::get('/post', function () {
    return redirect()->route('post.show');
})->name('post');
Route::get('/post/{post}', ShowPost::class)->name('post.single');

// --- Admin/Moderator Routes ---
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'check_banned', AdminOrModeratorMiddleware::class]) // Use the custom middleware alias
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
    
        Route::get('/user-approvals', \App\Livewire\Admin\UserApproval\Index::class)->name('user-approvals');
        Route::get('/verifications', \App\Livewire\Admin\Verifications\Index::class)->name('verifications');
        Route::get('/confirmations', \App\Livewire\Admin\Confirmations\Index::class)->name('confirmations');
        Route::get('/confirmation-logs', \App\Livewire\Admin\ConfirmationLogs\Index::class)->name('confirmation-logs');
    }); // End of Admin/Moderator Routes


require __DIR__ . '/auth.php'; // Auth routes (login, logout etc.) are defined here






// ToDo for the whole project:

//     Fix the following issues:
//         - [ ] ...

//     Messages:
//         - [ ] Add a report system for messages

//     Overall:
//         - [ ] Implement the feature verify email (absolutely in the end of project)
//         - [ ] Implement a verified User System

//     Language:
//         - [ ] Add more and more translations
//         - [ ] Add FR and IT language, extend the language switcher
//         - [ ] For save in more than one language, use an addon for laravel (e.g. spatie/laravel-translatable)
//         - [ ] Install a translater tool for pre-translation (e.g. Google Translate, DeepL API, LibreTranslate) for dynamic content

//     Security:
//         - [ ] Test the security of the app

//     Testing:
//         - [ ] ...

//     Frontend / not logged-in user:
//         - [ ] ...