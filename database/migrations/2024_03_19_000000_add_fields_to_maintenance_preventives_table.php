<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenances_preventives', function (Blueprint $table) {
            // Supprimer la colonne date_planifiee
            $table->dropColumn('date_planifiee');

            // Ajouter les nouvelles colonnes
            $table->foreignId('user_createur_id')->nullable()->constrained('users');
            $table->foreignId('user_assignee_id')->nullable()->constrained('users');
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances_preventives', function (Blueprint $table) {
            // Restaurer la colonne date_planifiee
            $table->date('date_planifiee');
            // Supprimer les nouvelles colonnes
            $table->dropForeign(['user_createur_id']);
            $table->dropForeign(['user_assignee_id']);
            $table->dropColumn([
                'user_createur_id',
                'user_assignee_id',
                'date_debut',
                'date_fin'
            ]);
        });
    }
}; 