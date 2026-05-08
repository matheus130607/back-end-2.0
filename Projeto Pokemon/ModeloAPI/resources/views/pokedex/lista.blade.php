<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <title>Pokedex Nacional</title>
</head>
<body class="pokemon-page pokedex-app pokedex-list-page">
@include('partials.menu-dropdown')

<main class="pokedex-list-shell">
    <section class="pokedex-hero" aria-labelledby="pokedexTitle">
        <div>
            <p class="pokedex-kicker">National Dex</p>
            <h1 id="pokedexTitle">Pokedex Nacional</h1>
            <p class="pokedex-lead">Explore os 1025 Pokemon principais, filtre por geracao, tipo, nome ou numero e abra os detalhes completos.</p>
        </div>
        <div class="pokedex-counter-panel" aria-live="polite">
            <span id="loadedCount">0 Pokemon carregados</span>
            <small id="cacheStatus">Preparando conexao com a PokeAPI</small>
        </div>
    </section>

    <section class="pokedex-filters" aria-label="Filtros da Pokedex">
        <div class="filter-row">
            <label class="filter-field">
                <span>Buscar por nome ou ID</span>
                <input id="searchInput" type="search" placeholder="Ex: pik ou 25" autocomplete="off">
            </label>

            <label class="filter-field">
                <span>Geracao</span>
                <select id="generationFilter">
                    <option value="all">Todas as geracoes</option>
                    <option value="1">Geracao 1</option>
                    <option value="2">Geracao 2</option>
                    <option value="3">Geracao 3</option>
                    <option value="4">Geracao 4</option>
                    <option value="5">Geracao 5</option>
                    <option value="6">Geracao 6</option>
                    <option value="7">Geracao 7</option>
                    <option value="8">Geracao 8</option>
                    <option value="9">Geracao 9</option>
                    <option value="created">Pokemon criados</option>
                </select>
            </label>

            <label class="filter-field">
                <span>Ordenacao</span>
                <select id="sortSelect">
                    <option value="id-asc">ID crescente</option>
                    <option value="id-desc">ID decrescente</option>
                    <option value="name-asc">Nome A-Z</option>
                    <option value="name-desc">Nome Z-A</option>
                </select>
            </label>

            <button type="button" class="pokedex-action pokedex-action--light" id="clearFiltersButton">Limpar filtros</button>
        </div>

        <div class="type-filter-wrap">
            <div class="type-filter-heading">
                <span>Tipagem</span>
                <small>Selecione ate 2 tipos</small>
            </div>
            <div class="type-filter-grid" id="typeFilterGrid">
                @foreach($typeNames as $type)
                    <button
                        type="button"
                        class="type-filter-button"
                        data-type="{{ $type }}"
                        style="--type-color: {{ $typeColors[$type] ?? '#A8A77A' }}"
                    >
                        {{ ucfirst($type) }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="list-feedback is-visible" id="listLoading" aria-live="polite">
        <div class="pokedex-mini-loader"></div>
        <strong>Carregando Pokedex</strong>
        <span id="loadingProgress">Buscando lista principal...</span>
    </section>

    <section class="list-feedback list-feedback--error" id="listError" hidden>
        <strong>Nao foi possivel carregar os Pokemon no momento.</strong>
        <span>Tente novamente mais tarde.</span>
        <button type="button" class="pokedex-action" id="retryListButton">Tentar novamente</button>
    </section>

    <section class="list-feedback" id="emptyState" hidden>
        <strong>Nenhum Pokemon encontrado com esses filtros.</strong>
        <span id="emptyStateHint">Ajuste a busca ou limpe os tipos selecionados.</span>
    </section>

    <section class="pokemon-card-grid" id="pokemonGrid" aria-live="polite"></section>

    <nav class="pokedex-pagination" id="pagination" aria-label="Paginacao da lista">
        <button type="button" class="pokedex-action" id="prevPage">Anterior</button>
        <div>
            <div class="page-numbers" id="pageNumbers"></div>
            <p id="pageSummary">Pagina 1 de 1</p>
        </div>
        <button type="button" class="pokedex-action" id="nextPage">Proximo</button>
    </nav>
</main>

<script>
    window.POKEDEX_CONFIG = {
        page: 'list',
        apiBase: 'https://pokeapi.co/api/v2',
        maxPokemon: 1025,
        perPage: 44,
        listUrl: @json(url('/pokedex/lista')),
        routeBase: @json(url('/pokedex')),
        typeColors: @json($typeColors),
        typeNames: @json($typeNames),
        customPokemons: @json($customPokemons)
    };
</script>
<script src="{{ asset('js/pokedex.js') }}" defer></script>
</body>
</html>
