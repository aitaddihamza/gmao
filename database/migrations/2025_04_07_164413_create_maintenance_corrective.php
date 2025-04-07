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
        Schema::create('maintenances_correctives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('date_signalement');
            $table->date('date_intervention')->nullable();
            $table->date('date_resolution')->nullable();
            $table->text('description_panne');
            $table->string('statut');
            $table->text('diagnostic')->nullable();
            $table->text('solution')->nullable();
            $table->integer('temps_arret')->nullable();
            $table->timestamps();
        });
        ;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_corrective');
    }
};
