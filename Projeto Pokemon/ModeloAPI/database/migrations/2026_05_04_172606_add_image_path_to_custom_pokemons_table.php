<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            // Renomear a coluna image_url para image_path (opcional)
            if (Schema::hasColumn('custom_pokemons', 'image_url')) {
                $table->renameColumn('image_url', 'image_path');
            } else {
                $table->string('image_path')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('custom_pokemons', function (Blueprint $table) {
            if (Schema::hasColumn('custom_pokemons', 'image_path')) {
                $table->renameColumn('image_path', 'image_url');
            }
        });
    }
};