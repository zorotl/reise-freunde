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
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->comment('User who reported')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->comment('Post being reported')->onDelete('cascade');
            $table->text('reason')->nullable()->comment('Optional reason provided by the reporter');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->index();
            $table->foreignId('processed_by')->nullable()->constrained('users')->comment('Admin/Mod who processed')->onDelete('set null'); // Optional: track who handled it
            $table->timestamp('processed_at')->nullable();
            $table->timestamps(); // created_at (when reported), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reports');
    }
};