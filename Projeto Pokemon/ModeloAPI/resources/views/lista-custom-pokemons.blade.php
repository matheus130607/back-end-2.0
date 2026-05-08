{{-- resources/views/lista-custom-pokemons.blade.php --}}
@extends('layouts.pokedex')

@section('title', 'Meus Pokémon Customizados')

@push('styles')
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .header {
        text-align: center;
        margin-bottom: 40px;
    }

    .header h1 {
        color: white;
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        background: rgba(227, 53, 53, 0.9);
        display: inline-block;
        padding: 15px 30px;
        border-radius: 50px;
        border: 2px solid #aa2222;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .stats-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0,0,0,0.5);
        padding: 15px 25px;
        border-radius: 50px;
        margin-bottom: 30px;
        backdrop-filter: blur(5px);
        flex-wrap: wrap;
        gap: 15px;
    }

    .total-count {
        color: #ffeb3b;
        font-size: 1.2rem;
        font-weight: bold;
    }

    .total-count span {
        color: white;
        font-size: 1.5rem;
    }

    .pokemon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .pokemon-card {
        background: #e33535;
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid #aa2222;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }

    .pokemon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ffd700, #ffed4e, #ffd700);
    }

    .pokemon-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        border-color: #ffd700;
    }

    .pokemon-image {
        width: 150px;
        height: 150px;
        margin: 0 auto 15px;
        background: rgba(0,0,0,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 3px solid #ffd700;
        transition: all 0.3s ease;
    }

    .pokemon-card:hover .pokemon-image {
        transform: scale(1.05);
        border-color: #fff;
    }

    .pokemon-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .pokemon-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffeb3b;
        margin-bottom: 10px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .pokemon-id {
        color: white;
        font-size: 0.9rem;
        margin-bottom: 10px;
        background: rgba(0,0,0,0.3);
        display: inline-block;
        padding: 3px 10px;
        border-radius: 15px;
    }

    .types-container {
        margin-bottom: 15px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    .type-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: white;
        transition: all 0.3s ease;
    }

    .type-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .custom-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffd700;
        color: #333;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .pokemon-stats {
        background: rgba(0,0,0,0.3);
        border-radius: 15px;
        padding: 10px;
        margin: 15px 0;
        font-size: 0.8rem;
        color: white;
    }

    .stat-line {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .stat-line:last-child {
        border-bottom: none;
    }

    .stat-label {
        font-weight: bold;
        color: #ffeb3b;
    }

    .ability-text {
        color: #00ff41;
        font-style: italic;
    }

    .button-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
    }

    .btn {
        padding: 8px 20px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }

    .btn-view {
        background: #2196F3;
        color: white;
    }

    .btn-edit {
        background: #FF9800;
        color: white;
    }

    .btn-delete {
        background: #f44336;
        color: white;
    }

    .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .btn-delete:hover {
        background: #d32f2f;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn-action {
        background: rgba(227, 53, 53, 0.95);
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        font-weight: bold;
        font-family: 'Courier New', monospace;
        border: 2px solid #aa2222;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        background: #cc0000;
    }

    .empty-message {
        text-align: center;
        color: white;
        font-size: 1.2rem;
        padding: 50px;
        background: rgba(0,0,0,0.5);
        border-radius: 30px;
        backdrop-filter: blur(10px);
    }

    .empty-message p {
        margin-bottom: 20px;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .header h1 {
            font-size: 1.5rem;
            padding: 10px 20px;
        }
        
        .stats-bar {
            flex-direction: column;
            text-align: center;
        }
        
        .pokemon-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }

    /* Animação de fade in */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pokemon-card {
        animation: fadeInUp 0.5s ease forwards;
    }
</style>
@include('partials.menu-dropdown')
@endpush

@section('content')
<div class="container">
    <div class="header">
        <h1>📋 MEUS POKÉMON CUSTOMIZADOS</h1>
    </div>

    <div class="stats-bar">
        <div class="total-count">
            📊 TOTAL: <span>{{ count($customPokemons) }}</span> Pokémon(s)
        </div>
        <div class="total-count">
            ✨ CRIADOS POR VOCÊ
        </div>
    </div>

    <div class="pokemon-grid">
        @forelse($customPokemons as $pokemon)
            @php
                $typeColors = [
                    'normal' => '#A8A878', 'fire' => '#F08030', 'water' => '#6890F0',
                    'electric' => '#F8D030', 'grass' => '#78C850', 'ice' => '#98D8D8',
                    'fighting' => '#C03028', 'poison' => '#A040A0', 'ground' => '#E0C068',
                    'flying' => '#A890F0', 'psychic' => '#F85888', 'bug' => '#A8B820',
                    'rock' => '#B8A038', 'ghost' => '#705898', 'dragon' => '#7038F8',
                    'dark' => '#705848', 'steel' => '#B8B8D0', 'fairy' => '#EE99AC'
                ];
                
                // Obter os tipos (suporte a múltiplos tipos)
                $types = [];
                if (isset($pokemon->types_list) && is_array($pokemon->types_list)) {
                    $types = $pokemon->types_list;
                } elseif (isset($pokemon->types) && $pokemon->types) {
                    $types = json_decode($pokemon->types, true) ?? [$pokemon->type];
                } else {
                    $types = [$pokemon->type];
                }
            @endphp
            <div class="pokemon-card">
                <div class="custom-badge">CUSTOM</div>
                <div class="pokemon-image">
                    <img src="{{ $pokemon->image_path ? asset('storage/' . $pokemon->image_path) : 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png' }}" 
                         alt="{{ $pokemon->name }}">
                </div>
                <div class="pokemon-name">{{ ucfirst($pokemon->name) }}</div>
                <div class="pokemon-id">#{{ str_pad($pokemon->pokemon_id, 4, '0', STR_PAD_LEFT) }}</div>
                
                <div class="types-container">
                    @foreach($types as $type)
                        <span class="type-badge" style="background: {{ $typeColors[$type] ?? '#68A090' }};">{{ strtoupper($type) }}</span>
                    @endforeach
                </div>

                <div class="pokemon-stats">
                    <div class="stat-line">
                        <span class="stat-label">📏 ALTURA</span>
                        <span>{{ $pokemon->height ?? 1.0 }} m</span>
                    </div>
                    <div class="stat-line">
                        <span class="stat-label">⚖️ PESO</span>
                        <span>{{ $pokemon->weight ?? 10.0 }} kg</span>
                    </div>
                    <div class="stat-line">
                        <span class="stat-label">⭐ HABILIDADE</span>
                        <span class="ability-text">{{ ucfirst($pokemon->ability ?? 'Habilidade Especial') }}</span>
                    </div>
                </div>

                <div class="button-group">
                    <a href="{{ route('pokedex.detalhes', ['pokemon' => $pokemon->pokemon_id]) }}" class="btn btn-view">👁️ VER</a>
                    <a href="{{ route('custom-pokemon.edit', ['pokemon_id' => $pokemon->pokemon_id]) }}" class="btn btn-edit">✏️ EDITAR</a>
                    <form action="{{ route('custom-pokemon.destroy', ['pokemon_id' => $pokemon->pokemon_id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete">🗑️ EXCLUIR</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-message">
                <p>😢 NENHUM POKÉMON CUSTOMIZADO AINDA!</p>
                <p style="font-size: 0.9rem; margin-top: 10px;">Crie seu primeiro Pokémon customizado e ele aparecerá aqui.</p>
                <a href="{{ route('custom-pokemon.create') }}" class="btn-action" style="margin-top: 20px; display: inline-block;">➕ CRIAR MEU PRIMEIRO POKÉMON</a>
            </div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('pokedex.lista') }}" class="btn-action">← VOLTAR PARA POKÉDEX</a>
        <a href="{{ route('custom-pokemon.create') }}" class="btn-action" style="background: #4CAF50; border-color: #2e7d32;">➕ CRIAR NOVO POKÉMON</a>
    </div>
</div>
@endsection

