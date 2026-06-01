<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPedido extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'preco_unitario' => 'decimal:2',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->quantidade * (float) $this->preco_unitario;
    }
}
