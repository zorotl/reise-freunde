<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'firstname' => 'Martin',
            'lastname' => 'Striednig',
            'email' => 'info@stws.ch',
            'password' => Hash::make('test1234'),
            'status' => 'approved',
        ]);
        User::factory()->create([
            'firstname' => 'Zorotl',
            'lastname' => 'Von Zorot',
            'email' => 'zorotl@stws.ch',
            'password' => Hash::make('test1234'),
            'status' => 'approved',
        ]);
        User::factory(3)->create();
    }
}
