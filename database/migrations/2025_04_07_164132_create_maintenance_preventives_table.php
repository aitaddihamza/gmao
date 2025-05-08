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
        Schema::create('maintenances_preventives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained();
            $table->string('statut');
            $table->text('description');
            $table->foreignId('user_createur_id')->nullable()->constrained('users');
            $table->foreignId('user_assignee_id')->nullable()->constrained('users');
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->boolean('type_externe')->default(false);
            $table->string('fournisseur')->nullable();
            $table->text('remarques')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_preventives');
    }
};
