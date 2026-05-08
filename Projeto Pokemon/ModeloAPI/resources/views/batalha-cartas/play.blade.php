<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <link rel="stylesheet" href="{{ asset('css/battle-cards.css') }}">
    <title>Jogar - Batalha TCG</title>
</head>
<body class="pokemon-page pokedex-app battle-cards-page">
@include('partials.menu-dropdown')

<main class="battle-page-shell battle-play-shell" data-battle-screen="play">
    <section class="battle-hero battle-hero--compact">
        <div>
            <p class="pokedex-kicker">Arena local</p>
            <h1>Batalha TCG</h1>
        </div>
        <div class="battle-hero-actions">
            <a href="{{ route('battle-cards.deck') }}" class="pokedex-action pokedex-action--light">Montar deck</a>
            <a href="{{ route('battle-cards.index') }}" class="pokedex-action">Inicio</a>
        </div>
    </section>

    <section class="battle-board" data-battle-board>
        <div class="battle-scorebar">
            <div class="battle-score">Oponente: <strong data-bot-points>0</strong></div>
            <div class="battle-turn-pill" data-turn-pill>Preparando</div>
            <div class="battle-score">Jogador: <strong data-player-points>0</strong></div>
        </div>

        <div class="battle-side battle-side--bot">
            <div class="battle-side-info">
                <span>Oponente</span>
                <strong>Deck <span data-bot-deck-count>0</span></strong>
            </div>
            <div class="battle-bench battle-bench--bot" data-bot-bench></div>
            <div class="battle-active-slot" data-bot-active></div>
        </div>

        <div class="battle-center-panel">
            <div class="battle-log" data-battle-log></div>
            <div class="battle-orb-zone" data-orb-zone></div>
            <div class="battle-attack-panel" data-attack-panel></div>
            <div class="battle-actions-row battle-actions-row--center">
                <button type="button" class="pokedex-action pokedex-action--blue" data-setup-ready hidden>Comecar batalha</button>
                <button type="button" class="pokedex-action pokedex-action--light" data-end-turn disabled>Finalizar turno</button>
            </div>
        </div>

        <div class="battle-side battle-side--player">
            <div class="battle-active-slot" data-player-active></div>
            <div class="battle-bench battle-bench--player" data-player-bench></div>
            <div class="battle-side-info">
                <span>Jogador</span>
                <strong>Deck <span data-player-deck-count>0</span></strong>
            </div>
        </div>

        <div class="battle-hand-panel">
            <div class="battle-panel-heading battle-panel-heading--compact">
                <div>
                    <p class="pokedex-kicker">Mao</p>
                    <h2>Cartas do jogador</h2>
                </div>
                <span class="battle-counter" data-hand-counter>0</span>
            </div>
            <div class="battle-hand" data-player-hand></div>
        </div>
    </section>
</main>

<div class="battle-modal" data-battle-modal hidden>
    <div class="battle-modal__backdrop" data-modal-close></div>
    <div class="battle-modal__box" role="dialog" aria-modal="true" aria-labelledby="battleModalTitle">
        <p class="pokedex-kicker">Fim de partida</p>
        <h2 id="battleModalTitle" data-modal-title>Resultado</h2>
        <p data-modal-message></p>
        <div class="battle-actions-row">
            <button type="button" class="pokedex-action pokedex-action--blue" data-modal-rematch>Jogar novamente</button>
            <a href="{{ route('battle-cards.deck') }}" class="pokedex-action pokedex-action--light">Voltar para montar deck</a>
            <a href="{{ route('cartas.index') }}" class="pokedex-action">Voltar para cartas Pokemon</a>
        </div>
    </div>
</div>

<script>
    window.BattleCardsConfig = {
        mode: 'play',
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
