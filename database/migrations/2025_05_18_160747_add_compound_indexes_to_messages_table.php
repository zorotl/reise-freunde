<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // For Inbox view
            $table->index(['receiver_id', 'receiver_deleted_at', 'receiver_archived_at', 'created_at'], 'messages_inbox_index');

            // For Outbox view
            $table->index(['sender_id', 'sender_deleted_at', 'sender_archived_at', 'created_at'], 'messages_outbox_index');

            // For ArchivedBox (covering both sender and receiver scenarios, consider splitting if queries are always separate)
            $table->index(['receiver_id', 'receiver_archived_at', 'receiver_permanently_deleted_at', 'receiver_deleted_at'], 'messages_archive_receiver_flags_index');
            $table->index(['sender_id', 'sender_archived_at', 'sender_permanently_deleted_at', 'sender_deleted_at'], 'messages_archive_sender_flags_index');

            // For TrashBox
            $table->index(['receiver_id', 'receiver_deleted_at', 'receiver_permanently_deleted_at'], 'messages_trash_receiver_flags_index');
            $table->index(['sender_id', 'sender_deleted_at', 'sender_permanently_deleted_at'], 'messages_trash_sender_flags_index');

            // For Unread Count in Inbox
            $table->index(['receiver_id', 'read_at', 'receiver_deleted_at', 'receiver_archived_at'], 'messages_unread_inbox_index');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_inbox_index');
            $table->dropIndex('messages_outbox_index');
            $table->dropIndex('messages_archive_receiver_flags_index');
            $table->dropIndex('messages_archive_sender_flags_index');
            $table->dropIndex('messages_trash_receiver_flags_index');
            $table->dropIndex('messages_trash_sender_flags_index');
            $table->dropIndex('messages_unread_inbox_index');
        });
    }
};