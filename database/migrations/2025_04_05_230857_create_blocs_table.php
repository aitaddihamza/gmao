<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_bloc');
            $table->string('description');
            $table->string('localisation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocs');
    }
};