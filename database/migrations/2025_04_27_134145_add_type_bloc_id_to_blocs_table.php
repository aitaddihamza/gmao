<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocs', function (Blueprint $table) {
            $table->foreignId('type_bloc_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('blocs', function (Blueprint $table) {
            $table->dropForeign(['type_bloc_id']);
            $table->dropColumn('type_bloc_id');
        });
    }
}; 