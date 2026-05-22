<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Authorization extends Model
{
    public const TYPE_ENTRADA = 'entrada';
    public const TYPE_SAIDA = 'saida';

    public const STATUS_AGUARDANDO_PROFESSOR = 'aguardando_professor';
    public const STATUS_AGUARDANDO_PORTARIA = 'aguardando_portaria';
    public const STATUS_ENTRADA_REGISTRADA = 'entrada_registrada';
    public const STATUS_SAIDA_REALIZADA = 'saida_realizada';
    public const STATUS_CANCELADO = 'cancelado';

    protected $fillable = [
        'student_id',
        'responsible_id',
        'secretary_id',
        'teacher_id',
        'portaria_id',
        'type',
        'reason',
        'notes',
        'status',
        'requested_at',
        'teacher_acknowledged_at',
        'validated_at',
        'notification_sent_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'teacher_acknowledged_at' => 'datetime',
        'validated_at' => 'datetime',
        'notification_sent_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function autorizador(): BelongsTo
    {
        return $this->responsible();
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function portaria(): BelongsTo
    {
        return $this->belongsTo(User::class, 'portaria_id');
    }

    public function isEntrada(): bool
    {
        return $this->type === self::TYPE_ENTRADA;
    }

    public function isSaida(): bool
    {
        return $this->type === self::TYPE_SAIDA;
    }

    public function typeLabel(): string
    {
        return $this->isSaida() ? 'Saida antecipada' : 'Entrada tardia';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_AGUARDANDO_PROFESSOR => 'Aguardando professor',
            self::STATUS_AGUARDANDO_PORTARIA => 'Aguardando portaria',
            self::STATUS_ENTRADA_REGISTRADA => 'Entrada registrada',
            self::STATUS_SAIDA_REALIZADA => 'Saida realizada',
            self::STATUS_CANCELADO => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function typeBadgeClasses(): string
    {
        return $this->isSaida()
            ? 'bg-orange-100 text-orange-700 ring-orange-200'
            : 'bg-cyan-100 text-cyan-700 ring-cyan-200';
    }

    public function statusBadgeClasses(): string
    {
        return match ($this->status) {
            self::STATUS_AGUARDANDO_PROFESSOR => 'bg-indigo-100 text-indigo-700 ring-indigo-200',
            self::STATUS_AGUARDANDO_PORTARIA => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            self::STATUS_ENTRADA_REGISTRADA,
            self::STATUS_SAIDA_REALIZADA => 'bg-slate-100 text-slate-700 ring-slate-200',
            self::STATUS_CANCELADO => 'bg-red-100 text-red-700 ring-red-200',
            default => 'bg-gray-100 text-gray-700 ring-gray-200',
        };
    }
}
