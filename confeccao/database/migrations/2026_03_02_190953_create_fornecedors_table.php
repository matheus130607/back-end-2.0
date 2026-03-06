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
        Schema::create('fornecedors', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj')->nullable();
            $table->string('telefone');
            $table->string('email')->nullable();
            $table->string('empresa')->nullable();
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // havia um erro de digitação no nome da tabela ao fazer rollback
        // ("fornecedores" em vez de "fornecedores").
        Schema::dropIfExists('fornecedors');
    }
};
