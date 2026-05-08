<?php
// app/Models/CustomPokemon.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPokemon extends Model
{
    use HasFactory;

    protected $table = 'custom_pokemons';
    
    protected $fillable = [
        'pokemon_id',
        'name',
        'types',
        'type',
        'height',
        'weight',
        'description',
        'ability',
        'abilities',
        'stats',
        'image_path',
    ];
    
    protected $casts = [
        'types' => 'array',
        'abilities' => 'array',
        'stats' => 'array',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];
    
    public function getTypeListAttribute()
    {
        if (is_array($this->types) && $this->types) {
            return $this->types;
        }

        return [$this->type ?: 'normal'];
    }

    public function getAbilityListAttribute(): array
    {
        if (is_array($this->abilities) && $this->abilities) {
            return array_values(array_filter($this->abilities));
        }

        if ($this->ability) {
            return array_values(array_filter(array_map('trim', explode(',', $this->ability))));
        }

        return ['Habilidade Especial'];
    }

    public function getPublicImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }
}
