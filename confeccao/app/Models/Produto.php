<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'quantidade',
        'tamanho',
        'cor'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'quantidade' => 'integer'
    ];
}