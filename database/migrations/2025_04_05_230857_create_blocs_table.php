<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blocs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nom_bloc');
            $table->string('description')->nullable();
            $table->string('type_bloc');
            $table->string('localisation');
            $table->timestamps();
            $table->softDeletes(); // Optionnel pour le soft delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocs');
    }
};