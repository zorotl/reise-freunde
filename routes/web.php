<!-- 
ToDo for the whole project:

Fix the following issues:
    - [ ] Fix the pinboard ==> Post
    - [ ] Fix sidebar to header

Backend:
    - [ ] Add birthday to the user model
    - [ ] Add nationality to the user model
    - [ ] Add a username to the user model
    - [ ] Add a profile picture to the user model
    - [ ] Add about_me to the user model
    - [ ] Add is_admin to the user model
    
    - [ ] Add From, To, Area and Country to the post-migration, model and controller
    - [ ] Add From, To, Area and Country to the post-blades create, edit and show views and myown?
    - [ ] Add a search bar to the pinboard
    - [ ] Add a filter to the pinboard

    - [ ] Add a mail system
    - [ ] Add a notification system
    - [ ] Add a follow system

    - [ ] Add a Travel-Styles table und system
    - [ ] Add a Travel-Styles pivot table
    - [ ] OPTIONAL: Add a Sports&Fun table und system
    - [ ] OPTIONAL: Add a Sports&Fun pivot-table
    

Frontend:
    - [ ] Add a Homepage
    - [ ] Add the Pinboard to the homepage

-->

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

