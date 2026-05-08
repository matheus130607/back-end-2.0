@php
    use App\Support\PokemonTypes;

    $energyColors = [
        'Colorless' => '#A8A77A',
        'Fire' => '#EE8130',
        'Water' => '#6390F0',
        'Lightning' => '#F7D02C',
        'Grass' => '#7AC74C',
        'Psychic' => '#F95587',
        'Fighting' => '#C22E28',
        'Darkness' => '#705746',
        'Metal' => '#B7B7CE',
        'Dragon' => '#6F35FC',
        'Fairy' => '#D685AD',
    ];
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Pokedex Jogo</title>
    <style>
        body {
            margin: 0;
            font-family: "Trebuchet MS", Arial, sans-serif;
        }

        .game-page {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 112px 18px 36px;
        }

        .game-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 16px;
        }

        .game-top h1 {
            margin: 0;
            color: #fff7c7;
            font-size: clamp(1.7rem, 4vw, 2.6rem);
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        .score-board {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font: 900 0.95rem "Courier New", monospace;
        }

        .score-pill {
            min-width: 80px;
            padding: 9px 12px;
            border-radius: 999px;
            text-align: center;
            background: rgba(8, 12, 22, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .game-board {
            display: grid;
            grid-template-rows: auto auto auto;
            gap: 14px;
        }

        .side-panel {
            padding: 14px;
        }

        .side-title {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            color: #ffdf6c;
            font: 900 0.92rem "Courier New", monospace;
            text-transform: uppercase;
        }

        .battle-row {
            display: grid;
            grid-template-columns: 1fr minmax(240px, 320px) 1fr;
            gap: 14px;
            align-items: stretch;
        }

        .bench {
            display: grid;
            grid-template-columns: repeat(3, minmax(88px, 1fr));
            gap: 10px;
        }

        .active-zone {
            min-height: 296px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background:
                radial-gradient(circle at 50% 38%, rgba(255, 255, 255, 0.14), transparent 36%),
                rgba(5, 11, 20, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .tcg-card {
            width: 100%;
            min-height: 158px;
            position: relative;
            display: grid;
            grid-template-rows: auto 1fr auto;
            gap: 6px;
            padding: 9px;
            color: #fff;
            background: linear-gradient(160deg, rgba(16, 24, 39, 0.92), rgba(46, 20, 45, 0.86));
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 8px;
            box-shadow: 0 14px 24px rgba(0, 0, 0, 0.28);
        }

        .tcg-card.is-active {
            max-width: 230px;
            min-height: 280px;
        }

        .tcg-card img {
            width: 100%;
            height: 110px;
            object-fit: contain;
            align-self: center;
            filter: drop-shadow(0 12px 14px rgba(0, 0, 0, 0.34));
        }

        .tcg-card.is-active img {
            height: 170px;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 900;
        }

        .hp {
            color: #ffdf6c;
        }

        .damage {
            color: #ff8aa0;
        }

        .energy-row {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            min-height: 20px;
        }

        .energy {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.42);
            box-shadow: inset 0 0 0 3px rgba(0, 0, 0, 0.12);
        }

        .empty-slot {
            min-height: 158px;
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: rgba(255, 255, 255, 0.42);
            font: 900 0.8rem "Courier New", monospace;
        }

        .center-panel {
            display: grid;
            gap: 12px;
            align-content: center;
        }

        .energy-orb {
            width: 84px;
            height: 84px;
            margin: 0 auto;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.58);
            box-shadow: 0 0 30px var(--orb-color), inset 0 0 18px rgba(255, 255, 255, 0.45);
            background: var(--orb-color);
        }

        .turn-box {
            padding: 14px;
            text-align: center;
            color: #fff;
        }

        .turn-box strong {
            display: block;
            margin-bottom: 8px;
            color: #ffdf6c;
            font: 900 0.9rem "Courier New", monospace;
        }

        .action-grid {
            display: grid;
            gap: 8px;
        }

        .hand {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(128px, 1fr));
            gap: 10px;
        }

        .hand .tcg-card {
            cursor: pointer;
            min-height: 210px;
        }

        .hand .tcg-card:hover {
            transform: translateY(-5px);
        }

        .attack-list {
            display: grid;
            gap: 8px;
        }

        .attack-btn {
            display: grid;
            gap: 4px;
            padding: 10px;
            border: 0;
            border-radius: 8px;
            color: #fff;
            text-align: left;
            background: rgba(8, 12, 22, 0.76);
            cursor: pointer;
        }

        .attack-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .log {
            max-height: 170px;
            overflow: auto;
            padding: 12px;
            color: rgba(255, 255, 255, 0.82);
            font: 700 0.82rem "Courier New", monospace;
            line-height: 1.55;
        }

        @media (max-width: 980px) {
            .battle-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .game-page {
                padding-top: 92px;
            }

            .game-top {
                display: grid;
            }

            .bench {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="pokemon-page" style="--type-bg: url('{{ $backgroundUrl }}');">

@include('partials.menu-dropdown')

<main class="game-page">
    <header class="game-top">
        <h1>Pokedex Jogo</h1>
        <div class="score-board">
            <div class="score-pill">Voce <span id="playerPoints">0</span>/3</div>
            <div class="score-pill">Bot <span id="botPoints">0</span>/3</div>
        </div>
    </header>

    <section class="game-board">
        <div class="side-panel ui-panel">
            <div class="side-title">
                <span>Bot</span>
                <span id="botDeckCount">20 cartas</span>
            </div>
            <div class="bench" id="botBench"></div>
        </div>

        <div class="battle-row">
            <div class="active-zone" id="botActive"></div>

            <aside class="center-panel">
                <div class="turn-box ui-panel">
                    <strong id="turnLabel">Seu turno</strong>
                    <div id="energyLabel">Energia pronta</div>
                    <div class="energy-orb" id="energyOrb" style="--orb-color: #A8A77A;"></div>
                </div>
                <div class="action-grid">
                    <button class="ui-button ui-button--yellow" type="button" id="drawBtn">Comprar</button>
                    <button class="ui-button ui-button--blue" type="button" id="attachBtn">Anexar energia</button>
                    <button class="ui-button" type="button" id="endTurnBtn">Encerrar turno</button>
                    <button class="ui-button ui-button--dark" type="button" id="restartBtn">Reiniciar</button>
                </div>
                <div class="log ui-panel" id="gameLog"></div>
            </aside>

            <div class="active-zone" id="playerActive"></div>
        </div>

        <div class="side-panel ui-panel">
            <div class="side-title">
                <span>Sua mao</span>
                <span id="playerDeckCount">20 cartas</span>
            </div>
            <div class="bench" id="playerBench"></div>
            <div class="hand" id="playerHand" style="margin-top: 12px;"></div>
        </div>

        <div class="side-panel ui-panel">
            <div class="side-title">
                <span>Ataques</span>
                <span id="activeStatus">Sem ativo</span>
            </div>
            <div class="attack-list" id="attackList"></div>
        </div>
    </section>
</main>

<script>
    const energyColors = @json($energyColors);
    const initialPlayerDeck = @json($playerDeck);
    const initialBotDeck = @json($botDeck);

    const state = {
        turn: 'player',
        winner: null,
        player: null,
        bot: null,
        log: []
    };

    function cloneDeck(deck) {
        return deck.map(card => ({
            ...card,
            attached: [],
            damageTaken: 0,
            instanceId: crypto.randomUUID ? crypto.randomUUID() : `${card.id}-${Math.random()}`
        }));
    }

    function shuffle(deck) {
        const copy = [...deck];
        for (let i = copy.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [copy[i], copy[j]] = [copy[j], copy[i]];
        }
        return copy;
    }

    function newPlayer(name, deck) {
        const shuffled = shuffle(cloneDeck(deck));
        return {
            name,
            deck: shuffled,
            hand: [],
            active: null,
            bench: [],
            points: 0,
            energyOrb: null,
            energyUsed: false,
            drewThisTurn: false,
            energyTypes: deriveEnergyTypes(shuffled)
        };
    }

    function deriveEnergyTypes(deck) {
        const costs = new Set();
        deck.forEach(card => {
            (card.attacks || []).forEach(attack => {
                (attack.cost || []).forEach(cost => {
                    if (cost !== 'Colorless') costs.add(cost);
                });
            });
        });
        return costs.size ? Array.from(costs) : ['Colorless'];
    }

    function draw(player, amount = 1) {
        for (let i = 0; i < amount; i++) {
            if (player.deck.length) player.hand.push(player.deck.shift());
        }
    }

    function setupBoard(player) {
        draw(player, 5);
        player.active = player.hand.shift() || null;
        while (player.bench.length < 2 && player.hand.length) {
            player.bench.push(player.hand.shift());
        }
    }

    function startGame() {
        state.player = newPlayer('Voce', initialPlayerDeck);
        state.bot = newPlayer('Bot', initialBotDeck);
        state.turn = 'player';
        state.winner = null;
        state.log = [];
        setupBoard(state.player);
        setupBoard(state.bot);
        startTurn('player');
        addLog('Batalha iniciada.');
        render();
    }

    function startTurn(side) {
        const player = state[side];
        state.turn = side;
        player.energyUsed = false;
        player.drewThisTurn = false;
        player.energyOrb = randomEnergy(player.energyTypes);
        if (side === 'bot') {
            setTimeout(botTurn, 700);
        }
    }

    function randomEnergy(types) {
        return types[Math.floor(Math.random() * types.length)] || 'Colorless';
    }

    function playFromHand(index) {
        if (state.turn !== 'player' || state.winner) return;
        const card = state.player.hand[index];
        if (!card) return;

        if (!state.player.active) {
            state.player.active = card;
        } else if (state.player.bench.length < 3) {
            state.player.bench.push(card);
        } else {
            addLog('Banco cheio.');
            return;
        }

        state.player.hand.splice(index, 1);
        addLog(`${card.name} entrou em campo.`);
        render();
    }

    function attachEnergy(side) {
        const player = state[side];
        if (!player.active || !player.energyOrb || player.energyUsed || state.winner) return false;
        player.active.attached.push(player.energyOrb);
        addLog(`${player.name} anexou energia ${player.energyOrb}.`);
        player.energyOrb = null;
        player.energyUsed = true;
        render();
        return true;
    }

    function playerDraw() {
        if (state.turn !== 'player' || state.player.drewThisTurn || state.winner) return;
        draw(state.player, 1);
        state.player.drewThisTurn = true;
        addLog('Voce comprou uma carta.');
        render();
    }

    function playerAttack(index) {
        if (state.turn !== 'player' || state.winner) return;
        attack('player', index);
    }

    function attack(side, attackIndex) {
        const attacker = state[side];
        const defender = state[side === 'player' ? 'bot' : 'player'];
        const active = attacker.active;
        const target = defender.active;
        const selectedAttack = active?.attacks?.[attackIndex];
        if (!active || !target || !selectedAttack) return false;
        if (!canPay(selectedAttack.cost || [], active.attached || [])) {
            addLog(`${active.name} ainda nao tem energia para ${selectedAttack.name}.`);
            return false;
        }

        const damage = parseDamage(selectedAttack.damage);
        target.damageTaken += damage;
        addLog(`${active.name} usou ${selectedAttack.name} e causou ${damage}.`);

        if (target.damageTaken >= target.hp) {
            const prize = prizeValue(target);
            attacker.points += prize;
            addLog(`${target.name} foi nocauteado. ${attacker.name} ganhou ${prize} ponto(s).`);
            promote(defender);
            checkWinner();
        }

        render();
        return true;
    }

    function canPay(cost, attached) {
        const attachedCounts = countTypes(attached);
        const neededCounts = countTypes(cost.filter(item => item !== 'Colorless'));

        for (const [type, amount] of Object.entries(neededCounts)) {
            if ((attachedCounts[type] || 0) < amount) return false;
            attachedCounts[type] -= amount;
        }

        const colorlessNeeded = cost.filter(item => item === 'Colorless').length;
        const remainingEnergy = Object.values(attachedCounts).reduce((sum, amount) => sum + amount, 0);
        return remainingEnergy >= colorlessNeeded;
    }

    function countTypes(items) {
        return items.reduce((counts, item) => {
            counts[item] = (counts[item] || 0) + 1;
            return counts;
        }, {});
    }

    function parseDamage(value) {
        const match = String(value || '').match(/\d+/);
        return match ? Number(match[0]) : 10;
    }

    function prizeValue(card) {
        const subtypes = (card.subtypes || []).map(item => String(item).toLowerCase());
        return subtypes.some(item => ['ex', 'v', 'vmax', 'vstar', 'gx'].includes(item)) ? 2 : 1;
    }

    function promote(player) {
        player.active = player.bench.shift() || player.hand.shift() || null;
        if (player.active) {
            player.active.damageTaken = player.active.damageTaken || 0;
            addLog(`${player.name} promoveu ${player.active.name}.`);
        }
    }

    function checkWinner() {
        if (state.player.points >= 3) state.winner = 'player';
        if (state.bot.points >= 3) state.winner = 'bot';
        if (!state.player.active) state.winner = 'bot';
        if (!state.bot.active) state.winner = 'player';
        if (state.winner) addLog(state.winner === 'player' ? 'Voce venceu.' : 'Bot venceu.');
    }

    function endTurn() {
        if (state.turn !== 'player' || state.winner) return;
        startTurn('bot');
        render();
    }

    function botTurn() {
        if (state.turn !== 'bot' || state.winner) return;
        const bot = state.bot;
        draw(bot, 1);
        while (bot.bench.length < 3 && bot.hand.length) bot.bench.push(bot.hand.shift());
        if (!bot.active) promote(bot);
        attachEnergy('bot');

        const attackIndex = bestAttackIndex(bot.active);
        if (attackIndex >= 0) {
            attack('bot', attackIndex);
        } else {
            addLog('Bot encerrou sem atacar.');
        }

        if (!state.winner) startTurn('player');
        render();
    }

    function bestAttackIndex(card) {
        if (!card) return -1;
        let best = -1;
        let bestDamage = -1;
        (card.attacks || []).forEach((attack, index) => {
            if (!canPay(attack.cost || [], card.attached || [])) return;
            const damage = parseDamage(attack.damage);
            if (damage > bestDamage) {
                bestDamage = damage;
                best = index;
            }
        });
        return best;
    }

    function cardHtml(card, options = {}) {
        if (!card) return '<div class="empty-slot">Vazio</div>';
        const hpLeft = Math.max(0, card.hp - (card.damageTaken || 0));
        return `
            <div class="tcg-card ${options.active ? 'is-active' : ''}" ${options.handIndex !== undefined ? `data-hand-index="${options.handIndex}"` : ''}>
                <div class="card-head">
                    <span>${card.name}</span>
                    <span class="hp">${hpLeft}/${card.hp}</span>
                </div>
                <img src="${card.image || 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png'}" alt="${card.name}">
                <div>
                    <div class="damage">${card.damageTaken ? `${card.damageTaken} dano` : '&nbsp;'}</div>
                    <div class="energy-row">${(card.attached || []).map(energyHtml).join('')}</div>
                </div>
            </div>
        `;
    }

    function energyHtml(type) {
        return `<span class="energy" title="${type}" style="background:${energyColors[type] || '#A8A77A'}"></span>`;
    }

    function renderBench(side, elementId) {
        const bench = state[side].bench;
        document.getElementById(elementId).innerHTML = [0, 1, 2].map(index => cardHtml(bench[index])).join('');
    }

    function renderAttacks() {
        const card = state.player.active;
        const attackList = document.getElementById('attackList');
        document.getElementById('activeStatus').textContent = card ? card.name : 'Sem ativo';

        if (!card) {
            attackList.innerHTML = '<div class="empty-slot">Sem Pokemon ativo</div>';
            return;
        }

        attackList.innerHTML = card.attacks.map((attack, index) => {
            const ready = canPay(attack.cost || [], card.attached || []);
            return `
                <button class="attack-btn" type="button" ${ready && state.turn === 'player' && !state.winner ? '' : 'disabled'} data-attack-index="${index}">
                    <strong>${attack.name} ${attack.damage ? `(${attack.damage})` : ''}</strong>
                    <span>${(attack.cost || []).join(' / ') || 'Sem custo'}</span>
                </button>
            `;
        }).join('');
    }

    function render() {
        document.getElementById('playerPoints').textContent = state.player.points;
        document.getElementById('botPoints').textContent = state.bot.points;
        document.getElementById('playerDeckCount').textContent = `${state.player.deck.length} cartas`;
        document.getElementById('botDeckCount').textContent = `${state.bot.deck.length} cartas`;
        document.getElementById('playerActive').innerHTML = cardHtml(state.player.active, { active: true });
        document.getElementById('botActive').innerHTML = cardHtml(state.bot.active, { active: true });
        renderBench('player', 'playerBench');
        renderBench('bot', 'botBench');
        document.getElementById('playerHand').innerHTML = state.player.hand.map((card, index) => cardHtml(card, { handIndex: index })).join('');
        renderAttacks();

        const current = state[state.turn];
        const orb = current.energyOrb || 'Colorless';
        document.getElementById('turnLabel').textContent = state.winner
            ? (state.winner === 'player' ? 'Voce venceu' : 'Bot venceu')
            : (state.turn === 'player' ? 'Seu turno' : 'Turno do bot');
        document.getElementById('energyLabel').textContent = current.energyOrb ? `Orbe: ${orb}` : 'Energia usada';
        document.getElementById('energyOrb').style.setProperty('--orb-color', energyColors[orb] || '#A8A77A');
        document.getElementById('drawBtn').disabled = state.turn !== 'player' || state.player.drewThisTurn || state.winner;
        document.getElementById('attachBtn').disabled = state.turn !== 'player' || !state.player.energyOrb || state.player.energyUsed || state.winner;
        document.getElementById('endTurnBtn').disabled = state.turn !== 'player' || state.winner;
        document.getElementById('gameLog').innerHTML = state.log.slice(-24).map(item => `<div>${item}</div>`).join('');
    }

    function addLog(message) {
        state.log.push(message);
    }

    document.getElementById('drawBtn').addEventListener('click', playerDraw);
    document.getElementById('attachBtn').addEventListener('click', () => attachEnergy('player'));
    document.getElementById('endTurnBtn').addEventListener('click', endTurn);
    document.getElementById('restartBtn').addEventListener('click', startGame);
    document.getElementById('playerHand').addEventListener('click', event => {
        const card = event.target.closest('[data-hand-index]');
        if (card) playFromHand(Number(card.dataset.handIndex));
    });
    document.getElementById('attackList').addEventListener('click', event => {
        const button = event.target.closest('[data-attack-index]');
        if (button) playerAttack(Number(button.dataset.attackIndex));
    });

    startGame();
</script>

</body>
</html>
