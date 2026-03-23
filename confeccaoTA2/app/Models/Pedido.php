<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $guarded = [];

    // Um pedido pertence a um Cliente
    public function cliente() {
        return $this->belongsTo(Cliente::class);
    }

    public function itens() {
        return $this->hasMany(ItemPedido::class);
    }
}
