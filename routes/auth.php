<?php

use App\Models\User;
use Livewire\Volt\Volt;
use App\Livewire\Mail\MessageView;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('register', 'auth.register')
        ->name('register');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');

});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');



// ***** Routs only for tests *****
// for UserFollowTest
Route::post('/user/{user}/follow', function (User $user) {
    abort_if(auth()->id() === $user->id, 403);
    auth()->user()->follow($user);
    return back();
})->name('user.follow');
Route::post('/user/{user}/unfollow', function (User $user) {
    auth()->user()->unfollow($user);
    return back();
})->name('user.unfollow');
// for MessageViewTest
Route::get('/mail/messages/{message}/{fromWhere}', MessageView::class)
    ->name('mail.messages.view');
