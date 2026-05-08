<?php
// database/migrations/xxxx_add_stats_to_custom_pokemons_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            $table->decimal('height', 5, 1)->default(1.0)->after('type'); // Altura em metros
            $table->decimal('weight', 6, 1)->default(10.0)->after('height'); // Peso em kg
            $table->string('ability')->default('Habilidade Especial')->after('weight'); // Habilidade
            $table->json('stats')->nullable()->after('ability'); // Estatísticas opcionais
        });
    }

    public function down()
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'ability', 'stats']);
        });
    }
};