<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('piece_weight', 10, 3)->default(0)->after('units_per_pack')->comment('وزن القطعة الواحدة');
            $table->string('weight_unit', 10)->default('kg')->after('piece_weight')->comment('وحدة الوزن (kg, g, lb)');
            $table->decimal('carton_weight', 10, 3)->default(0)->after('weight_unit')->comment('وزن الكارتون (محسوب تلقائياً)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['piece_weight', 'weight_unit', 'carton_weight']);
        });
    }
};
