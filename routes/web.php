<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Middleware\AdminOrModeratorMiddleware;
use Livewire\Volt\Volt;

// 📌 Public routes
Route::get('/', fn() => view('frontend.homepage'))->name('home');

Route::post('/set-locale', function () {
    $locale = request('locale');
    if (in_array($locale, ['en', 'de'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('set-locale');

Volt::route('/users', 'user.search')->name('user.directory');

Route::get('/user/profile/{id}', \App\Livewire\User\UserProfile::class)->name('user.profile');
Route::get('/post/show', \App\Livewire\Post\PostList::class)->name('post.show');
Route::get('/post', fn() => redirect()->route('post.show'))->name('post');

Volt::route('/banned', 'pages.banned')->middleware(['auth', 'check_banned'])->name('banned');
Route::get('/verification/wait', \App\Livewire\Verification\Wait::class)->middleware('auth')->name('verification.wait');


// 🔐 Authenticated group
Route::middleware(['auth', 'verified', 'check_banned', 'approved'])->group(function () {

    // Dashboard
    Volt::route('/dashboard', 'pages.dashboard.overview')->name('dashboard');

    // Settings
    Route::redirect('settings', 'settings/profile');
    foreach (['profile', 'password', 'appearance', 'preferences', 'privacy-settings', 'account-deletion', 'notifications'] as $page) {
        Volt::route("settings/$page", "settings.$page")->name("settings.$page");
    }

    // Posts
    Route::get('/post/myown', \App\Livewire\Post\MyPosts::class)->name('post.myown');
    Route::get('/post/create', \App\Livewire\Post\CreatePost::class)->name('post.create');
    Route::get('/post/edit/{id}', \App\Livewire\Post\EditPost::class)->name('post.edit');

    // Mail
    Route::get('/mail', fn() => redirect()->route('mail.inbox'))->name('mail');
    Route::get('/mail/inbox', \App\Livewire\Mail\Inbox::class)->name('mail.inbox');
    Route::get('/mail/outbox', \App\Livewire\Mail\Outbox::class)->name('mail.outbox');
    Route::get('/mail/archive', \App\Livewire\Mail\ArchivedBox::class)->name('mail.archive');
    Route::get('/mail/trash', \App\Livewire\Mail\TrashBox::class)->name('mail.trash');
    Route::get('/mail/messages/{message}/{fromWhere?}', \App\Livewire\Mail\MessageView::class)->name('mail.messages.view');
    Route::get('/mail/compose/{receiverId?}/{fixReceiver?}', \App\Livewire\Mail\MessageCompose::class)->name('mail.compose');

    // Follower system
    Route::get('/user/follow-requests', \App\Livewire\User\FollowRequestsList::class)->name('user.follow-requests');
    Route::get('/user/{id}/followers', \App\Livewire\User\FollowersList::class)->name('user.followers');
    Route::get('/user/{id}/following', \App\Livewire\User\FollowingList::class)->name('user.following');

    // Prifile Verification
    Route::get('/profile/verify', \App\Livewire\Profile\Verify::class)->name('profile.verify');
    Route::get('/profile/confirmations', \App\Livewire\Profile\ConfirmationInbox::class)->name('profile.confirmations');

    // Notifications
    Route::get('/notifications', \App\Livewire\Profile\Notifications::class)->name('notifications');
});

// Public, must be under the auth group because of Route Conflicts
Route::get('/post/{post}', \App\Livewire\Post\ShowPost::class)->name('post.single');

// 🛠 Admin + Moderator routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'check_banned', AdminOrModeratorMiddleware::class])
    ->group(function () {
        // Admin dashboard
        Volt::route('/', 'pages.admin.dashboard')->name('dashboard');

        // Admin panels
        foreach (['users' => 'users.index', 'posts' => 'posts.index', 'messages' => 'messages.index', 'hobbies' => 'hobbies.index', 'travel-styles' => 'travel-styles.index', 'reports' => 'reports.index',] as $path => $view) {
            Volt::route("/$path", "pages.admin.$view")->name($path);
        }

        Volt::route('/posts/{post}/edit', 'admin.posts.edit-post')->name('posts.edit');
        Volt::route('/messages/{messageId}', 'pages.admin.messages.show')->name('messages.show');

        // Livewire admin panels
        Route::get('/user-approvals', \App\Livewire\Admin\UserApproval\Index::class)->name('user-approvals');
        Route::get('/verifications', \App\Livewire\Admin\Verifications\Index::class)->name('verifications');
        Route::get('/confirmations', \App\Livewire\Admin\Confirmations\Index::class)->name('confirmations');
        Route::get('/confirmation-logs', \App\Livewire\Admin\ConfirmationLogs\Index::class)->name('confirmation-logs');
    });

// Auth system
require __DIR__ . '/auth.php';





// ToDo for the whole project:

// Für Projekt in Gemini:
// - kurz und bündig bezüglich Erklärungen, ganz besonders bei bug-fixing...da erklärt er sich aktuell dumm und dämlich
// - CLI Command statt konsole
// - übersetzungsfiles
// - unter 3 erstellt er den code, unter 4 die zusammenfassung der änderungen...irgendwie brauch ich das so nicht
// - prüfen ob es sinnvoll ist, seeder und factorys fürs testing und für die dev version zu ergänzen oder ändern
// - nach 1. verstehe meine anfrage, sollten rückfragen gestellt werden, erst dann mit 2 weitermachen

//     Fix the following issues:
//         - [ ] message: if i response to an answer, the titel should be the same, maybe with a prefix, what you recommande and can you find the bug?
//         - [ ] admin panel: earlyer i could report only post, if i accapt a report, the post was deleted, now it is not and the reason is, that i extendet reports to user an messages. 
//         - [ ] Ban Sender from main page open modal, from message-view ban directly permanently...i will also the modal

//     Messages:
//         - [ ] Delete messages and Archive messages

//     Overall:
//         - [ ] Implement the feature verify email (absolutely in the end of project)


//     Language:
//         - [ ] Add FR and IT language, extend the language switcher
//         - [ ] For save in more than one language, use an addon for laravel (e.g. spatie/laravel-translatable)
//         - [ ] Install a translater tool for pre-translation (e.g. Google Translate, DeepL API, LibreTranslate) for dynamic content
//         - [ ] Translate more and more content

//     Security:
//         - [ ] Test the security of the app

//     Testing:
//         - [ ] ...

//     Frontend / not logged-in user:
//         - [ ] ...