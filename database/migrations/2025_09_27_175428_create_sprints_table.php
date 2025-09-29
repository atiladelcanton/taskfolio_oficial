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
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('title');
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['PLANNING', 'ACTIVE', 'PAUSED', 'COMPLETED', 'CANCELLED'])
                ->default('PLANNING');
            $table->timestamps();

            // Índices para performance
            $table->index(['project_id', 'status']);
            $table->index(['status', 'start_at']);
            $table->index(['start_at', 'end_at']);
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
