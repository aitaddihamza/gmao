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
        Schema::create('interventions_pieces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_prev_id')->nullable();
            $table->foreign('maintenance_prev_id')->references('id')->on('maintenances_preventives')->onDelete('cascade');
            $table->foreignId('piece_id')->constrained()->onDelete('cascade');
            $table->integer('quantite_utilisee')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions_pieces');
    }
};
