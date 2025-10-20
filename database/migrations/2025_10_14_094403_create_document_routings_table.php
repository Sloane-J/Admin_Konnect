<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_routings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->string('status')->default('sent'); // 'sent', 'opened', 'forwarded'
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();

            $table->index('to_user_id');
            $table->index('document_id');
            $table->index('status');
            $table->index(['document_id', 'to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_routings');
    }
};
