<?php
// database/migrations/xxxx_update_custom_pokemons_for_multiple_types.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            // Mudar coluna type para comportar múltiplos tipos (JSON)
            $table->json('types')->nullable()->after('name');
            // Manter a coluna antiga por enquanto para compatibilidade
        });
    }

    public function down()
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            $table->dropColumn('types');
        });
    }
};