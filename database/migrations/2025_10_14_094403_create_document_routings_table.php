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
        Schema::create('document_routings', function (Blueprint $table) {
            $table->id();

            // Core routing info
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');

            // Routing context
            $table->text('message')->nullable(); // optional note: "Please review and sign"
            $table->string('status')->default('sent'); // 'sent', 'opened', 'forwarded'

            // Tracking
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('opened_at')->nullable();

            // Confidentiality
            $table->boolean('is_confidential')->default(false);

            // Audit trail
            $table->timestamps();

            // Indexes
            $table->index('to_user_id');
            $table->index('document_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_routings');
    }
};
