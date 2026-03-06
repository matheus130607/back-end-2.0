<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'quantidade_estoque',
        'tamanho',
        'cor',
    ];

    // O $casts serve para definir automaticamente o tipo
    // de dado quando o Laravel buscar ou salvar informações no banco
    protected $casts = [

        // Faz com que o campo 'preco' sempre seja tratado como decimal
        // com 2 casas decimais (ex: 10.00, 59.90)
        'preco' => 'decimal:2',

        // Garante que 'quantidade_estoque' sempre seja inteiro
        // evitando que venha como string do banco
        'quantidade_estoque' => 'integer',
    ];

    // shorthand accessor to allow using $estoque->quantidade
    public function getQuantidadeAttribute()
    {
        return $this->quantidade_estoque;
    }

    public function setQuantidadeAttribute($value)
    {
        $this->attributes['quantidade_estoque'] = $value;
    }
}