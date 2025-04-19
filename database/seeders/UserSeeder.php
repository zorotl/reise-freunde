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
            'name' => 'Martin Striednig',
            'email' => 'info@stws.ch',
            'password' => Hash::make('test1234'),
        ]);
        User::factory()->create([
            'name' => 'Zorotl von Zorot',
            'email' => 'zorotl@stws.ch',
            'password' => Hash::make('test1234'),
        ]);
        User::factory(8)->create();
    }
}
