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
        Schema::create('user_follower', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('The user who is following')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_user_id')->comment('The user being followed')->constrained('users')->onDelete('cascade');
            $table->timestamp('accepted_at')->nullable()->comment('Timestamp when the follow request was accepted (null if pending or direct follow)');
            $table->timestamps(); // Optional: created_at for request time, updated_at

            $table->unique(['user_id', 'following_user_id']); // Prevent duplicate follow entries/requests
            $table->index('following_user_id'); // Index for faster lookups of followers        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_follower');
    }
};
