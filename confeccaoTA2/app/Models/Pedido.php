<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    public const STATUS_PENDENTE = 'Pendente';
    public const STATUS_EM_PRODUCAO = 'Em Produção';
    public const STATUS_FINALIZADO = 'Finalizado';
    public const STATUS_CANCELADO = 'Cancelado';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'email_enviado_em' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDENTE => self::STATUS_PENDENTE,
            self::STATUS_EM_PRODUCAO => self::STATUS_EM_PRODUCAO,
            self::STATUS_FINALIZADO => self::STATUS_FINALIZADO,
            self::STATUS_CANCELADO => self::STATUS_CANCELADO,
        ];
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            self::STATUS_FINALIZADO => 'success',
            self::STATUS_EM_PRODUCAO => 'info',
            self::STATUS_PENDENTE => 'warning',
            self::STATUS_CANCELADO => 'danger',
            default => 'gray',
        };
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    public function emailsPedidos(): HasMany
    {
        return $this->hasMany(EmailPedido::class);
    }

    public function isFinalizado(): bool
    {
        return $this->status === self::STATUS_FINALIZADO;
    }

    public function recalcularValorTotal(): void
    {
        $total = $this->itens()
            ->get()
            ->sum(fn (ItemPedido $item): float => (float) $item->quantidade * (float) $item->preco_unitario);

        $this->forceFill([
            'valor_total' => $total,
        ])->saveQuietly();
    }
}
