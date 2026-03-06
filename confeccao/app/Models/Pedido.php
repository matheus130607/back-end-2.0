<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{

    use HasFactory;

    protected $fillable = ['nome', 'numero', 'preco', 'status', 'quantidade'];
}
