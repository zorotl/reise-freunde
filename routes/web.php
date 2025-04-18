<?php

use App\Livewire\Pinboard\EditPinboard;
use App\Livewire\Pinboard\MyPinboard;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pinboard\ShowPinboard;
use App\Livewire\Pinboard\CreatePinboard;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';


// Pinboard routes
Route::get('/pinboard/show', ShowPinboard::class)->name('pinboard.show');
Route::get('/pinboard/myown', MyPinboard::class)->name('pinboard.myown');
Route::get('/pinboard/create', CreatePinboard::class)->name('pinboard.create');
Route::get('/pinboard/edit/{id}', EditPinboard::class)->name('pinboard.edit');
Route::get('/pinboard', function () {
    return redirect()->route('pinboard.show');
})->name('pinboard');
