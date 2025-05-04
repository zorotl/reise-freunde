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
        Schema::create('post_likes', function (Blueprint $table) {
            // Define the columns for the pivot table
            $table->id(); // Primary key for the pivot table itself
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key for users table
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // Foreign key for posts table
            $table->timestamps(); // Optional: created_at for when the like happened

            // Add a unique constraint to prevent a user from liking the same post multiple times
            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_likes');
    }
};