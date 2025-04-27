<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            $table->foreignId('type_equipement_id')->after('id')->constrained('type_equipements')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            $table->dropForeign(['type_equipement_id']);
            $table->dropColumn('type_equipement_id');
        });
    }
}; 