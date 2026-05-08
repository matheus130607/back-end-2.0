@php
    use App\Support\PokemonTypes;

    $typeColors = PokemonTypes::colors();
    $typeLabels = PokemonTypes::labels();
    $backgroundUrl = PokemonTypes::typeBackgroundUrl(['grass']);
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Pokedex Nacional</title>
    <style>
        body {
            margin: 0;
            font-family: "Trebuchet MS", Arial, sans-serif;
        }

        .grid-page {
            position: relative;
            z-index: 1;
            max-width: 1320px;
            margin: 0 auto;
            padding: 120px 20px 48px;
        }

        .grid-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 24px;
        }

        .grid-title {
            margin: 0;
            color: #fff7c7;
            font-size: clamp(1.8rem, 5vw, 3rem);
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        .grid-subtitle {
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.78);
            max-width: 620px;
            line-height: 1.5;
        }

        .view-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .pokemon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(158px, 1fr));
            gap: 18px;
        }

        .pokemon-card {
            min-height: 226px;
            padding: 14px;
            text-align: center;
            cursor: pointer;
            color: #fff;
            background:
                radial-gradient(circle at 50% 32%, rgba(255, 255, 255, 0.22), transparent 35%),
                rgba(10, 16, 28, 0.56);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            backdrop-filter: blur(8px);
            transition: transform 180ms ease, border-color 180ms ease, background 180ms ease;
        }

        .pokemon-card:hover,
        .pokemon-card:focus-visible {
            transform: translateY(-7px);
            border-color: rgba(255, 216, 77, 0.72);
            background:
                radial-gradient(circle at 50% 32%, rgba(255, 216, 77, 0.24), transparent 36%),
                rgba(10, 16, 28, 0.68);
        }

        .pokemon-card img {
            width: 124px;
            height: 124px;
            object-fit: contain;
            display: block;
            margin: 2px auto 8px;
        }

        .pokemon-id {
            color: rgba(255, 255, 255, 0.55);
            font: 800 0.75rem "Courier New", monospace;
        }

        .pokemon-name {
            margin: 5px 0 9px;
            color: #ffdf6c;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .pokemon-types {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .load-more {
            text-align: center;
            margin-top: 28px;
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 2100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.76);
            backdrop-filter: blur(10px);
        }

        .modal.is-open {
            display: flex;
        }

        .modal-card {
            width: min(780px, 100%);
            max-height: 88vh;
            overflow: auto;
            border-radius: 14px;
            border: 2px solid #2b0b14;
            background: linear-gradient(145deg, #e82d4c, #a91530);
            box-shadow: 0 28px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            position: sticky;
            top: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 18px;
            background: rgba(80, 8, 25, 0.72);
            backdrop-filter: blur(8px);
        }

        .modal-header h2 {
            margin: 0;
            color: #fff7c7;
            font-size: 1.35rem;
        }

        .close-modal {
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 50%;
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
            cursor: pointer;
            font-size: 1.2rem;
        }

        .modal-body {
            padding: 20px;
        }

        .detail-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 20px;
        }

        .detail-image-box {
            min-height: 240px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background-image: var(--detail-bg);
            background-size: cover;
            background-position: center;
            border: 3px solid #071a15;
        }

        .detail-image {
            width: 210px;
            height: 210px;
            object-fit: contain;
        }

        .info-card {
            margin-bottom: 12px;
            padding: 13px;
            border-radius: 8px;
            background: rgba(5, 11, 20, 0.52);
        }

        .info-card-title {
            margin-bottom: 8px;
            color: #ffdf6c;
            font: 900 0.78rem "Courier New", monospace;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .stat-row {
            display: grid;
            grid-template-columns: 108px 38px 1fr;
            gap: 10px;
            align-items: center;
            margin: 8px 0;
            font-size: 0.82rem;
        }

        .stat-bar {
            height: 8px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.36);
        }

        .stat-fill {
            height: 100%;
            border-radius: inherit;
            background: #9df5d0;
        }

        .evolution-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .evolution-item {
            width: 86px;
            cursor: pointer;
            text-align: center;
            color: #fff;
        }

        .evolution-item img {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        @media (max-width: 760px) {
            .grid-page {
                padding-top: 92px;
            }

            .grid-header,
            .detail-layout {
                grid-template-columns: 1fr;
                display: grid;
            }

            .view-actions {
                justify-content: flex-start;
            }

            .pokemon-grid {
                grid-template-columns: repeat(auto-fill, minmax(132px, 1fr));
                gap: 12px;
            }

            .pokemon-card {
                min-height: 205px;
            }

            .pokemon-card img {
                width: 104px;
                height: 104px;
            }
        }
    </style>
</head>
<body class="pokemon-page" style="--type-bg: url('{{ $backgroundUrl }}');">

@include('partials.menu-dropdown')

<main class="grid-page">
    <header class="grid-header">
        <div>
            <h1 class="grid-title">Pokedex Nacional</h1>
            <p class="grid-subtitle">Grade rapida com cache no servidor, carregamento progressivo e detalhes sob demanda.</p>
        </div>
        <div class="view-actions">
            <a href="{{ route('pokedex.random') }}" class="ui-button ui-button--dark">Pokedex Random</a>
            <a href="{{ route('pokedex.game') }}" class="ui-button ui-button--yellow">Jogar TCG</a>
        </div>
    </header>

    @if(isset($erro))
        <div class="ui-panel" style="padding: 18px;">{{ $erro }}</div>
    @else
        <section class="pokemon-grid" id="pokemonGrid" aria-live="polite">
            @foreach($pokemons as $pokemon)
                @php
                    $cardTypes = PokemonTypes::fromPokemonPayload($pokemon);
                @endphp
                <article class="pokemon-card" tabindex="0" data-pokemon-id="{{ $pokemon['id'] }}">
                    <img src="{{ $pokemon['image'] }}" alt="{{ ucfirst($pokemon['name']) }}" loading="lazy">
                    <div class="pokemon-id">#{{ str_pad((string) $pokemon['id'], 3, '0', STR_PAD_LEFT) }}</div>
                    <div class="pokemon-name">{{ ucfirst($pokemon['name']) }}</div>
                    <div class="pokemon-types">
                        @foreach($cardTypes as $type)
                            <span class="type-badge" style="background: {{ PokemonTypes::color($type) }}">{{ PokemonTypes::label($type) }}</span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </section>

        <div class="load-more">
            <button class="ui-button ui-button--blue" type="button" id="loadMoreBtn">Carregar mais</button>
        </div>
    @endif
</main>

<div class="modal" id="pokemonModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-card">
        <div class="modal-header">
            <h2 id="modalTitle">Carregando</h2>
            <button class="close-modal" type="button" id="closeModal" aria-label="Fechar">x</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<script>
    const typeColors = @json($typeColors);
    const typeLabels = @json($typeLabels);
    let currentOffset = {{ (int) ($offset ?? 0) }};
    const limit = {{ (int) ($limit ?? 36) }};
    const totalCount = {{ (int) ($totalCount ?? 1025) }};
    let isLoadingMore = false;
    let currentPokemonData = null;
    let isShiny = false;

    const grid = document.getElementById('pokemonGrid');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const modal = document.getElementById('pokemonModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');

    function typeBadge(type) {
        const color = typeColors[type] || '#A8A77A';
        const label = typeLabels[type] || type;
        return `<span class="type-badge" style="background:${color}">${label}</span>`;
    }

    function cardTemplate(pokemon) {
        const types = (pokemon.types || []).map(item => item.type?.name || item.name || 'normal');
        return `
            <article class="pokemon-card" tabindex="0" data-pokemon-id="${pokemon.id}">
                <img src="${pokemon.image || pokemon.sprite || ''}" alt="${pokemon.name}" loading="lazy">
                <div class="pokemon-id">#${String(pokemon.id).padStart(3, '0')}</div>
                <div class="pokemon-name">${pokemon.name}</div>
                <div class="pokemon-types">${types.map(typeBadge).join('')}</div>
            </article>
        `;
    }

    async function loadMore() {
        if (isLoadingMore || !loadMoreBtn) return;
        isLoadingMore = true;
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Carregando...';
        window.showPokedexLoader?.('Carregando mais');

        try {
            const nextOffset = currentOffset + limit;
            const response = await fetch(`/api/pokedex-grid?offset=${nextOffset}&limit=${limit}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            grid.insertAdjacentHTML('beforeend', data.pokemons.map(cardTemplate).join(''));
            currentOffset = nextOffset;

            if (!data.hasMore || currentOffset + limit >= totalCount) {
                loadMoreBtn.style.display = 'none';
            }
        } catch (error) {
            console.error(error);
            loadMoreBtn.textContent = 'Tentar novamente';
        } finally {
            isLoadingMore = false;
            loadMoreBtn.disabled = false;
            if (loadMoreBtn.style.display !== 'none') {
                loadMoreBtn.textContent = 'Carregar mais';
            }
            window.hidePokedexLoader?.();
        }
    }

    async function showPokemonDetails(id) {
        modal.classList.add('is-open');
        modalTitle.textContent = 'Carregando';
        modalBody.innerHTML = '<div class="info-card">Buscando dados completos...</div>';
        window.showPokedexLoader?.('Carregando detalhes');

        try {
            const response = await fetch(`/api/pokedex-grid/pokemon/${id}/detalhes`, { headers: { 'Accept': 'application/json' } });
            const pokemon = await response.json();
            currentPokemonData = pokemon;
            isShiny = false;
            renderModal(pokemon);
        } catch (error) {
            modalBody.innerHTML = '<div class="info-card">Erro ao carregar detalhes.</div>';
        } finally {
            window.hidePokedexLoader?.();
        }
    }

    function backgroundFor(types) {
        return `url('/pokedex/type-background.svg?types=${encodeURIComponent(types.join(','))}')`;
    }

    function renderModal(pokemon) {
        modalTitle.textContent = `${pokemon.name} #${String(pokemon.id).padStart(3, '0')}`;
        const bg = backgroundFor(pokemon.types || ['normal']);
        modalBody.innerHTML = `
            <div class="detail-layout">
                <div>
                    <div class="detail-image-box" style="--detail-bg:${bg}">
                        <img id="detailImage" class="detail-image pokemon-image-shadow" src="${pokemon.images.official}" alt="${pokemon.name}">
                    </div>
                    <button class="ui-button ui-button--yellow" style="width:100%; margin-top:12px;" type="button" onclick="toggleShinyMode()">Modo shiny</button>
                </div>
                <div>
                    <div class="info-card">
                        <div class="info-card-title">Medidas</div>
                        <div>${pokemon.height} m | ${pokemon.weight} kg | captura ${pokemon.species.capture_rate}</div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-title">Tipos</div>
                        <div class="pokemon-types">${pokemon.types.map(typeBadge).join('')}</div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-title">Habilidades</div>
                        <div>${pokemon.abilities.map(ability => ability.name + (ability.is_hidden ? ' (oculta)' : '')).join(', ')}</div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-title">Status base</div>
                        ${pokemon.stats.map(stat => `
                            <div class="stat-row">
                                <strong>${stat.name}</strong>
                                <span>${stat.value}</span>
                                <div class="stat-bar"><div class="stat-fill" style="width:${Math.min(100, (stat.value / 255) * 100)}%"></div></div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            ${pokemon.evolution_chain && pokemon.evolution_chain.length > 1 ? `
                <div class="info-card">
                    <div class="info-card-title">Evolucoes</div>
                    <div class="evolution-flex">
                        ${pokemon.evolution_chain.map(evo => `
                            <div class="evolution-item" onclick="showPokemonDetails(${evo.id})">
                                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${evo.id}.png" alt="${evo.name}">
                                <div>${evo.name}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            <div class="info-card">
                <div class="info-card-title">Descricao</div>
                <div>${pokemon.species.flavor_text}</div>
            </div>
        `;
    }

    window.toggleShinyMode = function () {
        if (!currentPokemonData) return;
        isShiny = !isShiny;
        const image = document.getElementById('detailImage');
        image.src = isShiny
            ? (currentPokemonData.images.official_shiny || currentPokemonData.images.front_shiny || currentPokemonData.images.official)
            : currentPokemonData.images.official;
    };

    function closeModal() {
        modal.classList.remove('is-open');
        currentPokemonData = null;
    }

    loadMoreBtn?.addEventListener('click', loadMore);

    grid?.addEventListener('click', event => {
        const card = event.target.closest('.pokemon-card');
        if (card) showPokemonDetails(card.dataset.pokemonId);
    });

    grid?.addEventListener('keydown', event => {
        if (event.key !== 'Enter') return;
        const card = event.target.closest('.pokemon-card');
        if (card) showPokemonDetails(card.dataset.pokemonId);
    });

    document.getElementById('closeModal')?.addEventListener('click', closeModal);
    modal.addEventListener('click', event => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeModal();
    });
</script>

</body>
</html>
