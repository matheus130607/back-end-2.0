<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails_pedidos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('email_destinatario')->nullable();
            $table->string('assunto');
            $table->string('status_envio')->default('pendente');
            $table->timestamp('enviado_em')->nullable();
            $table->text('conteudo_resumo')->nullable();
            $table->text('erro_envio')->nullable();
            $table->timestamps();

            $table->index(['status_envio', 'enviado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails_pedidos');
    }
};
