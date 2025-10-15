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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            // Incident details
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();

            // Reporting & assignment
            $table->foreignId('reported_by')->constrained('users')->onDelete('set null')->nullable();
            $table->foreignId('assigned_department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            // Status tracking
            $table->string('status')->default('open'); // 'open', 'in_progress', 'resolved', 'closed'
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();

            // Audit trail
            $table->timestamps();

            // Indexes
            $table->index('assigned_department_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('reported_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
