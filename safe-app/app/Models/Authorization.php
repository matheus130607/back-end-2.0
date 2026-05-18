<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Authorization extends Model
{
    protected $fillable = ['student_id', 'autorizador_id', 'portaria_id', 'type', 'status', 'validated_at'];

    // Garante que o Laravel trate o campo validated_at como uma data/hora carbon
    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function autorizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizador_id');
    }

    public function portaria(): BelongsTo
    {
        return $this->belongsTo(User::class, 'portaria_id');
    }
}
