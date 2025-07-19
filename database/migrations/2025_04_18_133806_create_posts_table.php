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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // New title field
            $table->text('content');
            $table->timestamp('expiry_date')->nullable(); // New expiry date field
            $table->boolean('is_active')->default(true); // New active status, defaults to true
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->char('country', 2)->nullable()->index();
            $table->string('city')->nullable();
            $table->string('language_code', 5)->default('en');
            $table->foreign('language_code')->references('code')->on('languages')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); // Enable soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
