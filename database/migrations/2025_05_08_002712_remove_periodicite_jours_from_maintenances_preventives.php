<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenances_preventives', function (Blueprint $table) {
            $table->dropCOlumn('periodicite_jours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances_preventives', function (Blueprint $table) {
            $table->integer('periodicite_jours')->nullable()->after('periodicite');
        });
    }
};
