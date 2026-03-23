<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    protected $guarded = [];

    // Um item pertence a um produto
    public function produto() {
        return $this->belongsTo(Produto::class);
    }
}
