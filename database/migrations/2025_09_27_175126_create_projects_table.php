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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->enum('payment_type', ['monthly', 'sprint', 'hours']);
            $table->enum('payment_method', ['monthly', 'sprint', 'fixed']);
            $table->string('payment_day')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['negociation', 'pending', 'doing', 'canceled', 'finished'])
                ->default('negociation');
            $table->timestamps();

            // Índices para performance
            $table->index(['status', 'created_at']);
            $table->index(['client_id', 'status']);
            $table->index('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
