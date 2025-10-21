<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();

            $table->string('visitor_name');
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->string('visitor_company')->nullable();

            $table->foreignId('host_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->text('purpose');
            $table->text('notes')->nullable();

            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();

            $table->timestamps();

            $table->index('host_user_id');
            $table->index('department_id');
            $table->index('check_in_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
