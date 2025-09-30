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
        Schema::table('collaborators', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_type')->index()->comment('1 - Quinzenalmente, 2 - Mensal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborators', function (Blueprint $table) {
            //
        });
    }
};
