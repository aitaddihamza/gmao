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
        Schema::table('interventions_pieces', function (Blueprint $table) {

            $table->unsignedBigInteger('maintenance_corr_id')->nullable();
            $table->foreign('maintenance_corr_id')->references('id')->on('tickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interventions_pieces', function (Blueprint $table) {
            $table->dropForeign(['maintenance_corr_id']);
        });
    }
};
