<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participant', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['registered', 'waitlisted'])->default('registered');
            $table->timestamps();

            $table->primary(['event_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participant');
    }
};