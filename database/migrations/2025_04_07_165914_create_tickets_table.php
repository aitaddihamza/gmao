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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->foreignId('user_createur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_assignee_id')->constrained('users')->onDelete('cascade');
            $table->date('date_creation');
            $table->date('date_attribution')->nullable();
            $table->date('date_cloture')->nullable();
            $table->string('priorite');
            $table->text('description');
            $table->string('statut');
            $table->integer('gravite_panne');
            $table->integer('frequence_occurrence');
            $table->integer('detectabilite');
            $table->string('type_ticket');
            $table->string('chemin_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
