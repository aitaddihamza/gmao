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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloc_id')->constrained();
            $table->string('designation');
            $table->string('marque');
            $table->string('modele');
            $table->string('numero_serie')->unique();
            $table->date('date_acquisition');
            $table->date('date_mise_en_service');
            $table->string('etat');
            $table->string('fournisseur');
            $table->string('contact_fournisseur');
            $table->string('type_equipement');
            $table->date('date_fin_garantie');
            $table->boolean('sous_contrat')->default(false);
            $table->string('type_contrat')->nullable();
            $table->string('numero_contrat')->nullable();
            $table->string('criticite');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
