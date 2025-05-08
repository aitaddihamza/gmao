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
        Schema::table('maintenance_preventives', function (Blueprint $table) {
            $table->string('rapport_path')->nullable();
            $table->string('rapport_type')->nullable();
            $table->text('observations')->nullable();
            $table->text('actions_realisees')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_preventives', function (Blueprint $table) {
            $table->dropColumn(['rapport_path', 'rapport_type', 'observations', 'actions_realisees']);
        });
    }
};
