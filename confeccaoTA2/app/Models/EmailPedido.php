<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailPedido extends Model
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_ENVIADO = 'enviado';
    public const STATUS_SEM_EMAIL = 'sem_email';
    public const STATUS_ERRO = 'erro';

    protected $table = 'emails_pedidos';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enviado_em' => 'datetime',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_ENVIADO => 'Enviado',
            self::STATUS_SEM_EMAIL => 'Sem e-mail',
            self::STATUS_ERRO => 'Erro',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusOptions()[$status] ?? 'Desconhecido';
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            self::STATUS_ENVIADO => 'success',
            self::STATUS_PENDENTE => 'warning',
            self::STATUS_SEM_EMAIL => 'gray',
            self::STATUS_ERRO => 'danger',
            default => 'gray',
        };
    }
}
