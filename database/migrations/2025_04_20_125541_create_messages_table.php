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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sender_deleted_at')->nullable()->comment('When sender soft-deleted the message');
            $table->timestamp('receiver_deleted_at')->nullable()->comment('When receiver soft-deleted the message');
            $table->timestamp('sender_archived_at')->nullable()->comment('When sender archived the message');
            $table->timestamp('receiver_archived_at')->nullable()->comment('When receiver archived the message');
            $table->timestamp('sender_permanently_deleted_at')->nullable()->comment('Sender confirmed permanent deletion from their trash');
            $table->timestamp('receiver_permanently_deleted_at')->nullable()->comment('Receiver confirmed permanent deletion from their trash');
            $table->timestamps();
            $table->softDeletes(); // For soft deletes

            // Add indexes for performance on these new columns
            $table->index('sender_deleted_at');
            $table->index('receiver_deleted_at');
            $table->index('sender_archived_at');
            $table->index('receiver_archived_at');
            $table->index('sender_permanently_deleted_at');
            $table->index('receiver_permanently_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
