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
        Schema::create('indicateurs_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloc_id')->nullable()->constrained();
            $table->foreignId('equipement_id')->nullable()->constrained();
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->float('mtbf')->nullable();
            $table->float('mttr')->nullable();
            $table->integer('nombre_interventions');
            $table->float('taux_disponibilite');
            $table->timestamps();
        });
        ;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicateur_performances');
    }
};
