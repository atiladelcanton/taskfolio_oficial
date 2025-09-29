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
        Schema::create('task_tracking_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('collaborator_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('start_at');
            $table->timestamp('stop_at')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['task_id', 'collaborator_id']);
            $table->index(['collaborator_id', 'start_at']);
            $table->index(['start_at', 'stop_at']);
            $table->index(['task_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_tracking_times');
    }
};
