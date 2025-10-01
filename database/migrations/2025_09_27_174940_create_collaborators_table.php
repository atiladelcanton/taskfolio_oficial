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
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('document')->unique();
            $table->string('cellphone');
            $table->string('address')->nullable();
            $table->enum('payment_method', ['pix', 'transf']);
            $table->string('pix_key')->nullable();
            $table->string('bb_account')->nullable();
            $table->string('bb_agency')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('name');
            $table->index(['payment_method']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
