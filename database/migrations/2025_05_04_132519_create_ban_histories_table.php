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
        Schema::create('ban_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The user who was banned
            $table->foreignId('banned_by')->nullable()->constrained('users')->onDelete('set null'); // Admin/Mod who initiated the ban
            $table->text('reason')->nullable(); // Reason for the ban
            $table->timestamp('banned_at')->useCurrent(); // When the ban started
            $table->timestamp('expires_at')->nullable(); // When the ban expires (null for permanent)
            // No 'unbanned_at' needed, history is just a log of bans starting
            $table->timestamps(); // created_at will be the same as banned_at, updated_at if modified later
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ban_histories');
    }
};