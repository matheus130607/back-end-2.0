<?php
// database/migrations/xxxx_create_custom_pokemons_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_pokemons', function (Blueprint $table) {
            $table->id();
            $table->integer('pokemon_id')->unique(); // ID customizado
            $table->string('name');
            $table->string('type');
            $table->string('image_url')->nullable(); // URL da imagem
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_pokemons');
    }
};