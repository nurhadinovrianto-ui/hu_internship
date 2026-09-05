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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('host_user_id')->constrained('users')->cascadeOnDelete(); // The DPL or Supervisor who created it
            $table->string('topic');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->string('jitsi_room_id')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
