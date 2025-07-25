<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Para MySQL
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', [
                'scheduled',
                'completed',
                'canceled',
                'in_progress',
                'absent'
            ])->default('completed')->change();
        });
    }

    public function down(): void {
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', [
                'scheduled',
                'completed',
                'canceled'
            ])->default('completed')->change();
        });
    }
};