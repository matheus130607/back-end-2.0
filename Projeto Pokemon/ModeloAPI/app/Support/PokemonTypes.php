<?php

namespace App\Support;

class PokemonTypes
{
    public const TYPES = [
        'normal', 'fire', 'water', 'electric', 'grass', 'ice',
        'fighting', 'poison', 'ground', 'flying', 'psychic', 'bug',
        'rock', 'ghost', 'dragon', 'dark', 'steel', 'fairy',
    ];

    public static function colors(): array
    {
        return [
            'normal' => '#A8A77A',
            'fire' => '#EE8130',
            'water' => '#6390F0',
            'electric' => '#F7D02C',
            'grass' => '#7AC74C',
            'ice' => '#96D9D6',
            'fighting' => '#C22E28',
            'poison' => '#A33EA1',
            'ground' => '#E2BF65',
            'flying' => '#A98FF3',
            'psychic' => '#F95587',
            'bug' => '#A6B91A',
            'rock' => '#B6A136',
            'ghost' => '#735797',
            'dragon' => '#6F35FC',
            'dark' => '#705746',
            'steel' => '#B7B7CE',
            'fairy' => '#D685AD',
        ];
    }

    public static function labels(): array
    {
        return [
            'normal' => 'Normal',
            'fire' => 'Fogo',
            'water' => 'Agua',
            'electric' => 'Eletrico',
            'grass' => 'Planta',
            'ice' => 'Gelo',
            'fighting' => 'Lutador',
            'poison' => 'Veneno',
            'ground' => 'Terra',
            'flying' => 'Voador',
            'psychic' => 'Psiquico',
            'bug' => 'Inseto',
            'rock' => 'Pedra',
            'ghost' => 'Fantasma',
            'dragon' => 'Dragao',
            'dark' => 'Sombrio',
            'steel' => 'Metal',
            'fairy' => 'Fada',
        ];
    }

    public static function tcgMap(): array
    {
        return [
            'Colorless' => 'normal',
            'Fire' => 'fire',
            'Water' => 'water',
            'Lightning' => 'electric',
            'Grass' => 'grass',
            'Psychic' => 'psychic',
            'Fighting' => 'fighting',
            'Darkness' => 'dark',
            'Metal' => 'steel',
            'Dragon' => 'dragon',
            'Fairy' => 'fairy',
        ];
    }

    public static function normalize(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        return in_array($type, self::TYPES, true) ? $type : 'normal';
    }

    public static function normalizeMany(array $types): array
    {
        $normalized = [];

        foreach ($types as $type) {
            $type = self::normalize((string) $type);

            if (!in_array($type, $normalized, true)) {
                $normalized[] = $type;
            }
        }

        return array_slice($normalized ?: ['normal'], 0, 2);
    }

    public static function color(string $type): string
    {
        return self::colors()[self::normalize($type)] ?? '#A8A77A';
    }

    public static function label(string $type): string
    {
        return self::labels()[self::normalize($type)] ?? 'Normal';
    }

    public static function fromPokemonPayload(array $pokemon): array
    {
        $types = [];

        foreach (($pokemon['types'] ?? []) as $type) {
            $types[] = $type['type']['name'] ?? $type['name'] ?? $type;
        }

        return self::normalizeMany($types);
    }

    public static function fromTcgCards(array $cards): array
    {
        $map = self::tcgMap();
        $types = [];

        foreach ($cards as $card) {
            foreach (($card['types'] ?? []) as $tcgType) {
                if (isset($map[$tcgType])) {
                    $types[] = $map[$tcgType];
                }
            }
        }

        return self::normalizeMany($types ?: ['normal']);
    }

    public static function typeBackgroundUrl(array $types): string
    {
        return route('type.background', ['types' => implode(',', self::normalizeMany($types))]);
    }

    public static function backgroundMeta(string $type): array
    {
        $type = self::normalize($type);

        $meta = [
            'normal' => ['sky' => '#8fb8d8', 'horizon' => '#f2d6a2', 'ground' => '#8aa35b', 'deep' => '#38513a', 'scene' => 'field'],
            'fire' => ['sky' => '#522126', 'horizon' => '#e45d2f', 'ground' => '#3d1c1a', 'deep' => '#120c10', 'scene' => 'volcano'],
            'water' => ['sky' => '#75b9e8', 'horizon' => '#bfe7ff', 'ground' => '#2477aa', 'deep' => '#0d3355', 'scene' => 'lake'],
            'electric' => ['sky' => '#1b2140', 'horizon' => '#f7d84f', 'ground' => '#34373f', 'deep' => '#111827', 'scene' => 'storm'],
            'grass' => ['sky' => '#7fb6a6', 'horizon' => '#c7e889', 'ground' => '#4c8f45', 'deep' => '#173b28', 'scene' => 'forest'],
            'ice' => ['sky' => '#b6eaff', 'horizon' => '#f3ffff', 'ground' => '#86cfdc', 'deep' => '#315c7a', 'scene' => 'glacier'],
            'fighting' => ['sky' => '#c9865c', 'horizon' => '#ffe2a6', 'ground' => '#8e4b35', 'deep' => '#37241f', 'scene' => 'arena'],
            'poison' => ['sky' => '#5b386e', 'horizon' => '#b46bc0', 'ground' => '#436044', 'deep' => '#181a24', 'scene' => 'swamp'],
            'ground' => ['sky' => '#dba762', 'horizon' => '#ffe4b0', 'ground' => '#b67535', 'deep' => '#53341e', 'scene' => 'desert'],
            'flying' => ['sky' => '#7ab9ff', 'horizon' => '#eaf8ff', 'ground' => '#7d9ac4', 'deep' => '#283d61', 'scene' => 'clouds'],
            'psychic' => ['sky' => '#402a6d', 'horizon' => '#f68ac1', 'ground' => '#47306e', 'deep' => '#191226', 'scene' => 'aurora'],
            'bug' => ['sky' => '#9cbf68', 'horizon' => '#f0e79c', 'ground' => '#577a35', 'deep' => '#26351e', 'scene' => 'meadow'],
            'rock' => ['sky' => '#c79568', 'horizon' => '#f6d49b', 'ground' => '#7f6d45', 'deep' => '#30281f', 'scene' => 'canyon'],
            'ghost' => ['sky' => '#262139', 'horizon' => '#6e5c9d', 'ground' => '#2b3142', 'deep' => '#090b12', 'scene' => 'mist'],
            'dragon' => ['sky' => '#4a2a82', 'horizon' => '#de7af2', 'ground' => '#253560', 'deep' => '#101225', 'scene' => 'mountain'],
            'dark' => ['sky' => '#151923', 'horizon' => '#52556b', 'ground' => '#25231e', 'deep' => '#060708', 'scene' => 'night'],
            'steel' => ['sky' => '#a9b4c6', 'horizon' => '#e4e7ef', 'ground' => '#596274', 'deep' => '#242936', 'scene' => 'foundry'],
            'fairy' => ['sky' => '#f4a9d4', 'horizon' => '#ffe7f6', 'ground' => '#8dbf78', 'deep' => '#513b61', 'scene' => 'glade'],
        ];

        return $meta[$type];
    }
}
