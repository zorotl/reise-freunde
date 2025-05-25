<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->string('code', 5)->primary(); // e.g. 'en'
            $table->string('name_en');            // English
            $table->string('name_de')->nullable(); // German
            $table->string('name_fr')->nullable(); // French
            $table->string('name_es')->nullable(); // Spanish
            $table->string('name_it')->nullable(); // Italian
            $table->timestamps();
        });

        Schema::create('language_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('language_code', 5);
            $table->foreign('language_code')->references('code')->on('languages')->cascadeOnDelete();
            $table->primary(['user_id', 'language_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_user');
        Schema::dropIfExists('languages');
    }
};
