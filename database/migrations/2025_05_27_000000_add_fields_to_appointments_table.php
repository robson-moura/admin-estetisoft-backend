<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('price');
            $table->string('plan')->nullable()->after('payment_method');
            $table->text('signature')->nullable()->after('plan');
        });
    }

    public function down(): void {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'plan', 'signature']);
        });
    }
};