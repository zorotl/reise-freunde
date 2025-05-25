<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'en', 'name_en' => 'English', 'name_de' => 'Englisch'],
            ['code' => 'de', 'name_en' => 'German', 'name_de' => 'Deutsch'],
            ['code' => 'fr', 'name_en' => 'French', 'name_de' => 'Französisch'],
            ['code' => 'es', 'name_en' => 'Spanish', 'name_de' => 'Spanisch'],
            ['code' => 'it', 'name_en' => 'Italian', 'name_de' => 'Italienisch'],
            // Add more languages here...
        ];

        DB::table('languages')->insert($languages);
    }
}
