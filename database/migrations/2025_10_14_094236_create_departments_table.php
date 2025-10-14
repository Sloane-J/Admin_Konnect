<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('code', 10)->unique()->nullable();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deputy_head_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('head_user_id');
            $table->index('deputy_head_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
