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
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();

            // Visitor info
            $table->string('visitor_name');
            $table->string('visitor_email');

            // Visit details
            $table->foreignId('host_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');

            // Check in/out
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();

            // Status
            $table->string('status')->default('checked_in'); // 'checked_in', 'checked_out'

            // Audit trail
            $table->timestamps();

            // Indexes
            $table->index('host_user_id');
            $table->index('department_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
