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
        Schema::create('user_additional_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique(); // One-to-one with users
            $table->string('username')->unique();
            $table->date('birthday')->nullable();
            $table->string('nationality')->nullable();
            $table->string('profile_picture')->nullable(); // Store path or URL
            $table->longText('about_me')->nullable();
            $table->json('custom_travel_styles')->nullable();
            $table->json('custom_hobbies')->nullable();
            $table->boolean('is_private')->default(false); // Add the is_private column, default to public (false)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_additional_infos');
    }
};
