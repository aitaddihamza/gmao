<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('blocs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_bloc');
            $table->text('description')->nullable();
            $table->string('type_bloc');
            $table->string('localisation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocs');
    }
};
