<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <link rel="stylesheet" href="{{ asset('css/battle-cards.css') }}">
    <title>Montar Deck - Batalha TCG</title>
</head>
<body class="pokemon-page pokedex-app battle-cards-page">
@include('partials.menu-dropdown')

<main class="battle-page-shell battle-builder-shell" data-battle-screen="deck">
    <section class="battle-hero">
        <div>
            <p class="pokedex-kicker">Deck manual</p>
            <h1>Montar Deck</h1>
            <p class="pokedex-lead">Escolha 16 cartas Pokemon, confira as evolucoes e salve os tipos de energia antes de iniciar.</p>
        </div>
        <div class="battle-hero-actions">
            <button type="button" class="pokedex-action pokedex-action--blue" data-battle-auto>Gerar Deck Automatico</button>
            <a href="{{ route('battle-cards.index') }}" class="pokedex-action pokedex-action--light">Voltar</a>
        </div>
    </section>

    <section class="battle-builder-grid">
        <div class="battle-panel battle-card-library">
            <div class="battle-panel-heading">
                <div>
                    <p class="pokedex-kicker">Biblioteca</p>
                    <h2>Cartas disponiveis</h2>
                </div>
                <span class="battle-counter" data-library-counter>{{ count($initialCards) }}</span>
            </div>

            <form class="battle-filters" data-card-filters>
                <label class="filter-field">
                    <span>Buscar</span>
                    <input type="search" name="search" placeholder="Ex: Charmander" autocomplete="off">
                </label>

                <label class="filter-field">
                    <span>Tipo</span>
                    <select name="type">
                        <option value="">Todos</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="filter-field">
                    <span>Estagio</span>
                    <select name="stage">
                        <option value="">Todos</option>
                        @foreach($stageOptions as $stage)
                            <option value="{{ $stage }}">{{ $stage }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="filter-field">
                    <span>Raridade</span>
                    <select name="rarity">
                        <option value="">Todas</option>
                        @foreach($rarityOptions as $rarity)
                            <option value="{{ $rarity }}">{{ $rarity }}</option>
                        @endforeach
                    </select>
                </label>

                <button type="button" class="pokedex-action pokedex-action--light" data-clear-card-filters>Limpar filtros</button>
            </form>

            <div class="battle-status-line" data-library-status>Carregando cartas...</div>
            <div class="battle-library-grid" data-available-cards></div>
        </div>

        <aside class="battle-panel battle-my-deck">
            <div class="battle-panel-heading">
                <div>
                    <p class="pokedex-kicker">Meu deck</p>
                    <h2>16 cartas</h2>
                </div>
                <span class="battle-counter" data-deck-counter>0/16</span>
            </div>

            <div class="battle-status-line" data-deck-warning>Seu deck precisa ter 16 cartas e pelo menos um Pokemon Basico.</div>
            <div class="battle-deck-list" data-my-deck></div>

            <div class="battle-energy-block">
                <div class="battle-panel-heading battle-panel-heading--compact">
                    <div>
                        <p class="pokedex-kicker">Orbes</p>
                        <h3>Energia do deck</h3>
                    </div>
                    <span class="battle-counter" data-energy-counter>0/3</span>
                </div>
                <div class="battle-energy-picker battle-energy-picker--compact" data-energy-picker>
                    @foreach($energyTypes as $type)
                        <button type="button" class="battle-energy-choice" data-energy="{{ $type }}">
                            <span class="battle-orb" data-energy-orb="{{ $type }}">{{ $type }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="battle-actions-row battle-actions-row--stack">
                <button type="button" class="pokedex-action pokedex-action--blue" data-save-start>Iniciar batalha</button>
                <button type="button" class="pokedex-action pokedex-action--light" data-clear-deck>Limpar deck</button>
            </div>
        </aside>
    </section>
</main>

<script>
    window.BattleCardsConfig = {
        mode: 'deck',
        initialCards: @json($initialCards),
        energyTypes: @json($energyTypes),
        routes: {
            home: @json(route('battle-cards.index')),
            deck: @json(route('battle-cards.deck')),
            play: @json(route('battle-cards.play')),
            autoDeck: @json(route('battle-cards.auto')),
            cards: @json(route('battle-cards.cards')),
            cardsIndex: @json(route('cartas.index'))
        }
    };
</script>
<script src="{{ asset('js/battle-cards.js') }}" defer></script>
</body>
</html>
