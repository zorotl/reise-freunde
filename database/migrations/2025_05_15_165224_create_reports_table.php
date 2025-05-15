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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade'); // User who submitted the report
            $table->morphs('reportable'); // Polymorphic fields: post, user, message, etc.
            $table->string('reason'); // Predefined reason (e.g. spam, harassment, etc.)
            $table->text('comment')->nullable(); // Optional additional comment
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->index(); // Moderation status
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // Who handled it
            $table->timestamp('processed_at')->nullable(); // Moderation timestamp
            $table->timestamps(); // created_at (when submitted), updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
