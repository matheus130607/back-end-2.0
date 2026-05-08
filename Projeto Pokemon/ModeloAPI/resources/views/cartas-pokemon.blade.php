@php
    $tcgTypeColors = [
        'Fire' => '#EE8130',
        'Water' => '#6390F0',
        'Grass' => '#7AC74C',
        'Lightning' => '#F7D02C',
        'Psychic' => '#F95587',
        'Fighting' => '#C22E28',
        'Darkness' => '#705746',
        'Metal' => '#B7B7CE',
        'Dragon' => '#6F35FC',
        'Fairy' => '#D685AD',
        'Colorless' => '#A8A77A',
    ];

    $priceInfo = function (array $card) {
        $usd = [
            $card['tcgplayer']['prices']['holofoil']['market'] ?? null,
            $card['tcgplayer']['prices']['normal']['market'] ?? null,
            $card['tcgplayer']['prices']['reverseHolofoil']['market'] ?? null,
        ];

        foreach ($usd as $price) {
            if (is_numeric($price)) {
                return ['label' => 'US$ ' . number_format((float) $price, 2, '.', ','), 'value' => (float) $price];
            }
        }

        $eur = [
            $card['cardmarket']['prices']['averageSellPrice'] ?? null,
            $card['cardmarket']['prices']['trendPrice'] ?? null,
        ];

        foreach ($eur as $price) {
            if (is_numeric($price)) {
                return ['label' => '€ ' . number_format((float) $price, 2, ',', '.'), 'value' => (float) $price];
            }
        }

        return ['label' => 'Preco indisponivel', 'value' => null];
    };

    $buildFilterUrl = function ($pageNum) use ($busca, $rarity, $type, $set, $sort) {
        $params = array_filter([
            'page' => $pageNum > 1 ? $pageNum : null,
            'search' => $busca ?: null,
            'rarity' => $rarity ?: null,
            'type' => $type ?: null,
            'set' => $set ?: null,
            'sort' => $sort ?: null,
        ], fn ($value) => $value !== null && $value !== '');

        return route('cartas.index') . ($params ? '?' . http_build_query($params) : '');
    };
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <title>Cartas Pokemon TCG</title>
</head>
<body class="pokemon-page pokedex-app cards-tcg-page">
@include('partials.menu-dropdown')

<main class="cards-page-shell">
    <section class="pokedex-hero">
        <div>
            <p class="pokedex-kicker">Pokemon TCG</p>
            <h1>Cartas Pokemon</h1>
            <p class="pokedex-lead">Pesquise cartas, filtre por tipo, raridade e colecao, e veja o melhor preco disponivel pela API.</p>
        </div>
        <a href="{{ url('/carta-random') }}" class="pokedex-action pokedex-action--blue">Carta aleatoria</a>
    </section>

    <section class="pokedex-filters">
        <form class="cards-filter-form" id="cardsFilterForm" method="GET" action="{{ route('cartas.index') }}" data-loading-message="Buscando cartas">
            <label class="filter-field">
                <span>Buscar por nome</span>
                <input type="search" name="search" value="{{ $busca ?? '' }}" placeholder="Ex: Charizard" autocomplete="off">
            </label>

            <label class="filter-field">
                <span>Tipo</span>
                <select name="type">
                    <option value="">Todos tipos</option>
                    @foreach(array_keys($tcgTypeColors) as $option)
                        <option value="{{ $option }}" {{ ($type ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </label>

            <label class="filter-field">
                <span>Raridade</span>
                <select name="rarity">
                    <option value="">Todas raridades</option>
                    @foreach(['Common', 'Uncommon', 'Rare', 'Rare Holo', 'Rare Ultra', 'Rare Secret', 'Illustration Rare', 'Special Illustration Rare'] as $option)
                        <option value="{{ $option }}" {{ ($rarity ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </label>

            <label class="filter-field">
                <span>Colecao</span>
                <select name="set">
                    <option value="">Todas colecoes</option>
                    @foreach($setOptions ?? [] as $option)
                        <option value="{{ $option }}" {{ ($set ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                    @if(!empty($set) && !($setOptions ?? collect())->contains($set))
                        <option value="{{ $set }}" selected>{{ $set }}</option>
                    @endif
                </select>
            </label>

            <label class="filter-field">
                <span>Ordenacao</span>
                <select name="sort">
                    <option value="">Mais recentes</option>
                    <option value="name-asc" {{ ($sort ?? '') === 'name-asc' ? 'selected' : '' }}>Nome A-Z</option>
                    <option value="name-desc" {{ ($sort ?? '') === 'name-desc' ? 'selected' : '' }}>Nome Z-A</option>
                    <option value="price-asc" {{ ($sort ?? '') === 'price-asc' ? 'selected' : '' }}>Preco menor</option>
                    <option value="price-desc" {{ ($sort ?? '') === 'price-desc' ? 'selected' : '' }}>Preco maior</option>
                </select>
            </label>

            <div class="cards-filter-actions">
                <button type="submit" class="pokedex-action pokedex-action--blue">Buscar</button>
                <a href="{{ route('cartas.index') }}" class="pokedex-action pokedex-action--light">Limpar filtros</a>
            </div>
        </form>
    </section>

    <div class="cards-stats-line">
        <span><strong>{{ number_format($totalCount ?? 0, 0, ',', '.') }}</strong> cartas encontradas</span>
        <span>Pagina <strong>{{ $page ?? 1 }}</strong> de <strong>{{ $totalPages ?? 1 }}</strong></span>
    </div>

    @if(isset($erro))
        <section class="list-feedback is-visible">{{ $erro }}</section>
    @elseif(empty($cards))
        <section class="list-feedback is-visible">
            <strong>Nenhuma carta encontrada.</strong>
            <span>Tente ajustar os filtros.</span>
        </section>
    @else
        <section class="tcg-card-grid">
            @foreach($cards as $card)
                @php($price = $priceInfo($card))
                <a class="tcg-card-item" href="{{ route('carta.show', ['id' => $card['id']]) }}">
                    <span class="rarity">{{ $card['rarity'] ?? 'Sem raridade' }}</span>
                    <div class="tcg-image-wrap">
                        <img
                            src="{{ $card['images']['large'] ?? ($card['images']['small'] ?? '') }}"
                            alt="{{ $card['name'] }}"
                            loading="lazy"
                            onerror="this.src='{{ asset('favicon.png') }}'"
                        >
                    </div>
                    <div class="tcg-card-info">
                        <strong>{{ $card['name'] }}</strong>
                        <span>#{{ $card['number'] ?? '???' }} | {{ $card['set']['name'] ?? 'Colecao' }}</span>
                        <span class="tcg-price">{{ $price['label'] }}</span>
                        @if(!empty($card['types']))
                            <div class="type-badge-row">
                                @foreach($card['types'] as $typeName)
                                    <span class="pokedex-type-badge" style="--badge-color: {{ $tcgTypeColors[$typeName] ?? '#A8A77A' }}">{{ $typeName }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </section>

        @if(($totalPages ?? 1) > 1)
            <nav class="pagination" aria-label="Paginacao">
                @if($page > 1)
                    <a class="page-link" href="{{ $buildFilterUrl($page - 1) }}">Anterior</a>
                @endif

                @for($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++)
                    <a class="page-link {{ $page == $i ? 'active' : '' }}" href="{{ $buildFilterUrl($i) }}">{{ $i }}</a>
                @endfor

                @if($page < $totalPages)
                    <a class="page-link" href="{{ $buildFilterUrl($page + 1) }}">Proxima</a>
                @endif
            </nav>
        @endif
    @endif
</main>

<script>
    const cardsForm = document.getElementById('cardsFilterForm');
    let cardsSearchTimer = null;

    cardsForm?.querySelector('input[name="search"]')?.addEventListener('input', function () {
        clearTimeout(cardsSearchTimer);
        cardsSearchTimer = setTimeout(() => cardsForm.requestSubmit(), 450);
    });

    cardsForm?.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => cardsForm.requestSubmit());
    });
</script>
</body>
</html>
