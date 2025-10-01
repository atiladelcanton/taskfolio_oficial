<?php

declare(strict_types=1);

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprint_id')->constrained('sprints')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('collaborator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->enum('type_task', ['epic', 'bug', 'task','improvement','feature'])->default('task');
            $table->string('total_time_worked')->nullable()->comment('formato HH:MM:SS');
            $table->timestamps();

            // Índices para performance
            $table->index(['sprint_id', 'created_at']);
            $table->index(['collaborator_id', 'created_at']);
            $table->index(['applicant_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
            $table->index(['type_task', 'created_at']);
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
