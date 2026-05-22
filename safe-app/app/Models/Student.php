<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'classroom_id',
        'classroom',
        'registration_number',
        'responsible_id',
    ];

    public function classroomGroup(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function autorizador(): BelongsTo
    {
        return $this->responsible();
    }

    public function authorizations(): HasMany
    {
        return $this->hasMany(Authorization::class);
    }

    public function classroomName(): string
    {
        return $this->classroomGroup?->name ?? $this->classroom ?? 'Turma nao informada';
    }
}
