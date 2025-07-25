<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Relacionamento com serviço
            $table->unsignedBigInteger('service_id')->nullable()->after('user_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');

            // Relacionamento com produtos (array de IDs)
            $table->json('products_ids')->nullable()->after('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->dropColumn('products_ids');
        });
    }
};