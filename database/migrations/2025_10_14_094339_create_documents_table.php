<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->string('document_type'); // letter, memo, referral, receipt, etc.
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->boolean('is_confidential')->default(false);
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index('document_type');
            $table->index('department_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
