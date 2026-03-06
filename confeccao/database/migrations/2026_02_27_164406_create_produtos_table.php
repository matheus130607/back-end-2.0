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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // nome do produto
            $table->string('descricao')->nullable(); // descrição do produto
            $table->decimal('preco', 10, 2); // preço do produto
            $table->integer('quantidade')->default(0); // quantidade disponível
            $table->string('tamanho')->nullable(); // tamanho da roupa
            $table->string('cor')->nullable(); // cor do produto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};