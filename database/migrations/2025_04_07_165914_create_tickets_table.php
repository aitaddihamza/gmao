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
            $table->foreignId('user_assignee_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('priorite');
            $table->text('description');
            $table->string('statut');
            $table->string('gravite_panne')->nullable();
            $table->string('type_ticket');
            $table->string('chemin_image')->nullable();
            // resent fields
            $table->text('solution')->nullable();
            $table->text('diagnostic')->nullable();
            $table->dateTime('date_resolution')->nullable();
            $table->dateTime('date_intervention')->nullable();
            $table->integer('temps_arret')->nullable();
            $table->boolean('type_externe')->default(false);
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
