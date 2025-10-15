<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Recipients and sender
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Notification content
            $table->string('subject');
            $table->text('message');

            // Notification classification
            $table->string('type'); // 'document_routed', 'document_opened', 'visitor_checked_in', 'announcement', 'system_alert'

            // Broadcast fields
            $table->boolean('is_broadcast')->default(false);
            $table->string('broadcast_scope')->nullable(); // 'department', 'all_departments'
            $table->foreignId('broadcast_department_id')->nullable()->constrained('departments')->onDelete('set null');

            // Delivery method
            $table->string('sent_via')->default('both'); // 'email', 'in_app', 'both'

            // Read status
            $table->string('read_status')->default('unread'); // 'read', 'unread'

            // Audit trail
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('read_status');
            $table->index('broadcast_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
