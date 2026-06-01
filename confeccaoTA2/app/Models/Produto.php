<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'preco_venda' => 'decimal:2',
        ];
    }

    public function itensPedido(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }
}
