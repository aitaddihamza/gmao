<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('equipements', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            
            // Clé étrangère vers blocs
            $table->unsignedBigInteger('id_bloc');
            $table->foreign('id_bloc')
                  ->references('id')
                  ->on('blocs')
                  ->onDelete('cascade');
            
            // Colonnes de l'équipement
            $table->string('nom_equipement');
            $table->string('modele');
            $table->string('marque_fabricant');
            $table->string('numero_serie')->unique();
            $table->string('type');
            $table->date('date_installation');
            $table->date('date_fin_garantie');
            $table->boolean('sous_contrat')->default(false);
            $table->string('type_contrat')->nullable();
            $table->enum('statut', ['En service', 'Hors service', 'En maintenance'])->default('En service');
            $table->string('qr_code')->unique()->nullable();
            $table->string('criticite')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Optionnel pour le soft delete
        });
    }

    public function down()
    {
        Schema::table('equipements', function (Blueprint $table) {
            $table->dropForeign(['id_bloc']);
        });
        
        Schema::dropIfExists('equipements');
    }
};