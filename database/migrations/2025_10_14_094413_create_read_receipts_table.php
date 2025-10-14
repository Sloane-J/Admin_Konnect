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
        Schema::create('read_receipts', function (Blueprint $table) {
            $table->id();

            // Core receipt info
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('routing_id')->constrained('document_routings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Status tracking
            $table->string('status')->default('opened'); // 'opened', 'downloaded', 'viewed'

            // Timestamp
            $table->timestamp('opened_at')->useCurrent();

            // Audit trail
            $table->timestamps();

            // Indexes
            $table->index('routing_id');
            $table->index('document_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('read_receipts');
    }
};
