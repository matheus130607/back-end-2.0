<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <title>Pokedex - Detalhes</title>
</head>
<body class="pokemon-page pokedex-app pokedex-detail-page" data-pokemon-id="{{ $pokemonKey }}">
@include('partials.menu-dropdown')

<div class="pokedex-page-loader is-active" id="detailLoader" role="status" aria-live="polite">
    <div class="loader-device">
        <div class="loader-lights">
            <span class="loader-lens"></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="loader-screen">
            <span class="loader-scan"></span>
            <strong>Carregando dados</strong>
        </div>
    </div>
</div>

<main class="pokedex-detail-shell" id="detailShell" hidden>
    <section class="detail-topbar">
        <div>
            <p class="pokedex-kicker">Pokedex individual</p>
            <h1 id="detailTitle">Pokemon</h1>
        </div>
        <span class="detail-number" id="detailNumber">#000</span>
    </section>

    <section class="detail-error" id="detailError" hidden>
        <strong>Pokemon nao encontrado ou erro ao carregar dados.</strong>
        <a href="{{ url('/pokedex/lista') }}" class="pokedex-action">Voltar para lista</a>
    </section>

    <section class="pokedex-device-detail" id="detailContent">
        <div class="detail-left-panel">
            <div class="device-lights" aria-hidden="true">
                <span class="device-lens"></span>
                <span class="device-dot red"></span>
                <span class="device-dot yellow"></span>
                <span class="device-dot green"></span>
            </div>

            <div class="pokemon-art-screen" id="artScreen">
                <img id="pokemonImage" src="" alt="" loading="eager">
                <div class="image-placeholder" id="imagePlaceholder" hidden>Sem imagem</div>
            </div>

            <div class="pokemon-identity">
                <div>
                    <strong id="pokemonDisplayName">Pokemon</strong>
                    <span id="activeFormName">Forma principal</span>
                </div>
                <div class="type-badge-row" id="pokemonTypes"></div>
            </div>

            <div class="image-option-row" id="imageOptionButtons"></div>

            <article class="detail-panel">
                <h2>Descricao</h2>
                <p id="pokemonDescription">Descricao nao disponivel.</p>
            </article>

            <article class="detail-panel">
                <div class="section-heading">
                    <h2>Linha evolutiva</h2>
                    <span id="evolutionStatus"></span>
                </div>
                <div class="evolution-tree" id="evolutionTree"></div>
            </article>

            <article class="detail-panel">
                <div class="section-heading">
                    <h2>Formas alternativas</h2>
                    <span id="formsStatus"></span>
                </div>
                <div class="forms-grid" id="formsGrid"></div>
            </article>
        </div>

        <div class="detail-right-panel">
            <div class="screen-tabs" role="tablist" aria-label="Dados do Pokemon">
                <button type="button" class="screen-tab is-active" data-tab="stats">Status</button>
                <button type="button" class="screen-tab" data-tab="moves">Ataques</button>
            </div>

            <div class="pokedex-data-screen">
                <section class="screen-pane is-active" id="statsPane">
                    <div id="statsList"></div>
                </section>
                <section class="screen-pane" id="movesPane">
                    <div id="movesList"></div>
                    <div class="move-detail-panel" id="moveDetailPanel" hidden></div>
                </section>
            </div>

            <div class="detail-actions">
                <a href="{{ url('/pokedex/lista') }}" class="pokedex-action">Voltar para lista</a>
                <button type="button" class="pokedex-action pokedex-action--blue" id="randomPokemonButton">Pokemon aleatorio</button>
            </div>
        </div>
    </section>
</main>

<script>
    window.POKEDEX_CONFIG = {
        page: 'details',
        apiBase: 'https://pokeapi.co/api/v2',
        maxPokemon: 1025,
        perPage: 44,
        listUrl: @json(url('/pokedex/lista')),
        routeBase: @json(url('/pokedex')),
        initialPokemonId: @json($pokemonKey),
        typeColors: @json($typeColors),
        typeNames: @json($typeNames),
        customPokemon: @json($customPokemon)
    };
</script>
<script src="{{ asset('js/pokedex.js') }}" defer></script>
</body>
</html>
