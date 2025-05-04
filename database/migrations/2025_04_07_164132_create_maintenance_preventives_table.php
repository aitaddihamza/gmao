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
            $table->foreignId('user_id')->nullable()->constrained();
            $table->date('date_planifiee');
            $table->date('date_reelle')->nullable();
            $table->string('statut');
            $table->text('description');
            $table->integer('periodicite_jours');
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
