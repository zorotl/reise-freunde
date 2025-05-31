<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Illuminate\Http\UploadedFile;

test('profile page is displayed', function () {
    $this->actingAs(createApprovedUser());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = createApprovedUser();
    $this->actingAs($user);

    $user->additionalInfo()->firstOrCreate([
        'user_id' => $user->id,
    ], [
        'username' => 'initial_username',
        'birthday' => '1990-01-01',
    ]);

    $response = Volt::test('settings.profile')
        ->set('firstname', 'Test')
        ->set('lastname', 'User')
        ->set('email', 'test@example.com')
        ->set('username', 'testuser')
        ->set('birthday', '1984-08-28')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh()->load('additionalInfo');

    expect($user->firstname)->toBe('Test');
    expect($user->lastname)->toBe('User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
    expect($user->additionalInfo->birthday->format('Y-m-d'))->toBe('1984-08-28');
    expect($user->additionalInfo->username)->toBe('testuser');
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = createApprovedUser();
    $this->actingAs($user);

    $user->additionalInfo()->firstOrCreate([
        'user_id' => $user->id,
    ], [
        'username' => 'unchangeduser',
        'birthday' => '1990-01-01',
    ]);

    $response = Volt::test('settings.profile')
        ->set('firstname', 'Test')
        ->set('lastname', 'User')
        ->set('email', $user->email)
        ->set('username', 'unchangeduser')
        ->set('birthday', '1984-08-28')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = createApprovedUser();
    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response->assertHasNoErrors()->assertRedirect('/');

    expect($user->fresh()->trashed())->toBeTrue();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = createApprovedUser();
    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);
    expect($user->fresh()->trashed())->toBeFalse();
});

test('nationality can be updated', function () {
    $user = createApprovedUser();
    $this->actingAs($user);

    $user->additionalInfo()->firstOrCreate([
        'user_id' => $user->id,
    ], [
        'username' => 'nationalityuser',
        'birthday' => '1990-01-01',
    ]);

    $response = Volt::test('settings.profile')
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'nationalityuser')
        ->set('birthday', '1990-01-01')
        ->set('nationality', 'DE')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();
    expect($user->refresh()->additionalInfo->nationality)->toBe('DE');

    // Invalid size
    Volt::test('settings.profile')
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'nationalityuser')
        ->set('birthday', '1990-01-01')
        ->set('nationality', 'XYZ')
        ->call('updateProfileInformation')
        ->assertHasErrors(['nationality' => 'size']);

    // Invalid code
    Volt::test('settings.profile')
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'nationalityuser')
        ->set('birthday', '1990-01-01')
        ->set('nationality', 'XX')
        ->call('updateProfileInformation')
        ->assertHasErrors(['nationality' => 'in']);
});

test('profile picture can be uploaded and updated', function () {
    Storage::fake('public');
    $user = createApprovedUser();
    $this->actingAs($user);

    $user->additionalInfo()->create([
        'username' => 'testuser',
        'birthday' => '1990-01-01',
    ]);

    $file1 = UploadedFile::fake()->image('avatar1.jpg')->size(100);
    $file2 = UploadedFile::fake()->image('avatar2.png')->size(150);

    Volt::test('settings.profile')
        ->set('photo', $file1)
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'testuser1')
        ->set('birthday', '1990-01-01')
        ->call('updateProfileInformation')
        ->assertHasNoErrors(['photo']);

    $user->refresh()->load('additionalInfo');
    $firstPath = $user->additionalInfo->profile_picture_path;
    expect($firstPath)->not->toBeNull();
    Storage::disk('public')->assertExists($firstPath);

    Volt::test('settings.profile')
        ->set('photo', $file2)
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'testuser2')
        ->set('birthday', '1990-01-01')
        ->call('updateProfileInformation')
        ->assertHasNoErrors(['photo']);

    $user->refresh()->load('additionalInfo');
    $secondPath = $user->additionalInfo->profile_picture_path;
    expect($secondPath)->not->toBeNull()->and($secondPath)->not->toBe($firstPath);
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists($secondPath);
});

test('profile picture validation works', function () {
    Storage::fake('public');
    $user = createApprovedUser();
    $this->actingAs($user);

    $user->additionalInfo()->create([
        'username' => 'validationtest',
        'birthday' => '1990-01-01',
    ]);

    Volt::test('settings.profile')
        ->set('photo', UploadedFile::fake()->image('large.jpg')->size(6000))
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'validationtest')
        ->set('birthday', '1990-01-01')
        ->call('updateProfileInformation')
        ->assertHasErrors(['photo' => 'max']);

    Volt::test('settings.profile')
        ->set('photo', UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'))
        ->set('firstname', $user->firstname)
        ->set('lastname', $user->lastname)
        ->set('email', $user->email)
        ->set('username', 'validationtest2')
        ->set('birthday', '1990-01-01')
        ->call('updateProfileInformation')
        ->assertHasErrors(['photo' => 'image']);
});

test('user can delete their account and profile picture is removed', function () {
    Storage::fake('public');

    $user = createApprovedUser();
    $path = 'profile-pictures/' . $user->id . '/avatar.jpg';
    Storage::disk('public')->put($path, 'dummy');

    $user->additionalInfo()->create([
        'username' => 'deletepic',
        'profile_picture_path' => $path,
        'birthday' => '1990-01-01',
    ]);

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response->assertHasNoErrors()->assertRedirect('/');
    Storage::disk('public')->assertMissing($path);
    expect($user->fresh()->trashed())->toBeTrue();
    expect(auth()->check())->toBeFalse();
});
