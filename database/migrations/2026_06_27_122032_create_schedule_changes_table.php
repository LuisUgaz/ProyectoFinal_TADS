<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_changes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->cascadeOnDelete();

            $table->foreignId('reason_id')
                ->nullable()
                ->constrained('reasons')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('old_shift')->nullable();
            $table->string('new_shift')->nullable();

            $table->string('old_vehicle')->nullable();
            $table->string('new_vehicle')->nullable();

            $table->string('old_driver')->nullable();
            $table->string('new_driver')->nullable();

            $table->text('old_helpers')->nullable();
            $table->text('new_helpers')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_changes');
    }
};