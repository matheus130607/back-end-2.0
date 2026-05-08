<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <link rel="stylesheet" href="{{ asset('css/battle-cards.css') }}">
    <title>Batalha TCG</title>
</head>
<body class="pokemon-page pokedex-app battle-cards-page">
@include('partials.menu-dropdown')

<main class="battle-page-shell" data-battle-screen="home">
    <section class="battle-hero">
        <div>
            <p class="pokedex-kicker">Modo de cartas</p>
            <h1>Batalha TCG</h1>
            <p class="pokedex-lead">Monte seu deck de 16 cartas, escolha suas energias e batalhe contra o oponente em turnos.</p>
        </div>
        <div class="battle-hero-actions">
            <button type="button" class="pokedex-action pokedex-action--blue" data-battle-auto>Gerar Deck Automatico</button>
            <a href="{{ route('battle-cards.deck') }}" class="pokedex-action pokedex-action--light">Montar Deck Manualmente</a>
        </div>
    </section>

    <section class="battle-home-grid">
        <article class="battle-panel battle-panel--intro">
            <h2>Como funciona</h2>
            <div class="battle-rule-grid">
                <span>Deck com 16 cartas Pokemon.</span>
                <span>Comece com um Pokemon Basico em campo.</span>
                <span>Receba 1 orbe de energia por turno.</span>
                <span>Evolua somente depois de esperar uma rodada.</span>
                <span>Ataque quando tiver energia suficiente.</span>
                <span>Faca 3 pontos para vencer.</span>
            </div>
        </article>

        <article class="battle-panel">
            <div class="battle-panel-heading">
                <div>
                    <p class="pokedex-kicker">Energia</p>
                    <h2>Tipos do deck</h2>
                </div>
                <span class="battle-counter" data-energy-counter>0/3</span>
            </div>
            <p class="battle-muted">Escolha de 1 a 3 tipos. Se nao escolher, o sistema detecta pelos tipos do deck.</p>
            <div class="battle-energy-picker" data-energy-picker>
                @foreach($energyTypes as $type)
                    <button type="button" class="battle-energy-choice" data-energy="{{ $type }}">
                        <span class="battle-orb" data-energy-orb="{{ $type }}">{{ $type }}</span>
                    </button>
                @endforeach
            </div>
        </article>
    </section>

    <section class="battle-panel">
        <div class="battle-panel-heading">
            <div>
                <p class="pokedex-kicker">Deck atual</p>
                <h2>Previa do deck</h2>
            </div>
            <span class="battle-counter" data-deck-counter>0/16</span>
        </div>

        <div class="battle-status-line" data-battle-status>Nenhum deck carregado ainda.</div>
        <div class="battle-deck-preview" data-deck-preview></div>

        <div class="battle-actions-row">
            <button type="button" class="pokedex-action pokedex-action--blue" data-start-battle disabled>Iniciar Batalha</button>
            <button type="button" class="pokedex-action pokedex-action--light" data-continue-deck hidden>Continuar Deck Salvo</button>
            <a href="{{ route('cartas.index') }}" class="pokedex-action">Ver Cartas Pokemon</a>
        </div>
    </section>
</main>

<script>
    window.BattleCardsConfig = {
        mode: 'home',
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
