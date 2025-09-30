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
        Schema::create('project_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('collaborator_id')->constrained('collaborators')->onDelete('cascade');
            $table->decimal('collaborator_value', 18, 2)->default(0);
            $table->timestamps();

            // Índice único para evitar duplicatas
            $table->unique(['project_id', 'collaborator_id']);
            $table->index('project_id');
            $table->index('collaborator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_collaborators');
    }
};
