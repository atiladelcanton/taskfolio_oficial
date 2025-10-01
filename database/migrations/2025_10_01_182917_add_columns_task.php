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
        Schema::table('tasks', function (Blueprint $table) {
            $table->longText('accept_criteria')->nullable()->after('description');
            $table->longText('scene_test')->nullable()->after('accept_criteria');
            $table->longText('ovservations')->nullable()->after('scene_test');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['accept_criteria', 'scene_test', 'ovservations']);
        });
    }
};
