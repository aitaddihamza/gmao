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
        Schema::table('tickets', function (Blueprint $table) {
            $table->dateTime('date_attribution')->nullable()->change();
            $table->dateTime('date_cloture')->nullable()->change();
            $table->dateTime('date_resolution')->nullable()->change();
            $table->dateTime('date_intervention')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->date('date_attribution')->nullable()->change();
            $table->date('date_cloture')->nullable()->change();
            $table->date('date_resolution')->nullable()->change();
            $table->date('date_intervention')->nullable()->change();
        });
    }
};
