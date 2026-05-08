(function () {
    'use strict';

    const cfg = window.BattleCardsConfig || {};
    const DECK_KEY = 'pokemon_tcg_deck_v1';
    const ENERGY_KEY = 'pokemon_tcg_energy_types_v1';
    const BATTLE_KEY = 'pokemon_tcg_battle_state_v1';
    const DECK_SIZE = 16;
    const MAX_BENCH = 3;
    const POINTS_TO_WIN = 3;
    const PLACEHOLDER = '/favicon.png';
    const ENERGY_COLORS = {
        Grass: '#7AC74C',
        Fire: '#EE8130',
        Water: '#6390F0',
        Lightning: '#F7D02C',
        Psychic: '#F95587',
        Fighting: '#C22E28',
        Darkness: '#705746',
        Metal: '#B7B7CE',
        Dragon: '#6F35FC',
        Colorless: '#A8A77A',
        Fairy: '#D685AD',
    };

    let availableCards = [];
    let builderDeck = [];
    let selectedEnergyTypes = [];
    let battleState = null;
    let filterTimer = null;
    let battleBoardBound = false;

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        applyEnergyColors(document);

        if (cfg.mode === 'home') {
            initHome();
        }

        if (cfg.mode === 'deck') {
            initDeckBuilder();
        }

        if (cfg.mode === 'play') {
            initBattle();
        }
    }

    function initHome() {
        builderDeck = readDeck();
        selectedEnergyTypes = readEnergyTypes();
        bindEnergyPicker(document);
        renderHomeDeck();

        document.querySelector('[data-battle-auto]')?.addEventListener('click', async () => {
            setStatus('Gerando deck automatico...');
            const result = await fetchAutoDeck();

            if (!result.deck.length) {
                setStatus('Nao foi possivel gerar um deck completo. Tente novamente.');
                return;
            }

            builderDeck = result.deck;
            selectedEnergyTypes = result.energyTypes.length ? result.energyTypes : inferEnergyTypes(builderDeck);
            saveDeck(builderDeck);
            saveEnergyTypes(selectedEnergyTypes);
            syncEnergyButtons();
            renderHomeDeck();
            setStatus('Deck automatico pronto para batalha.');
        });

        document.querySelector('[data-start-battle]')?.addEventListener('click', () => {
            if (!validateDeck(builderDeck).valid) {
                setStatus('Seu deck precisa ter 16 cartas e pelo menos um Pokemon Basico.');
                return;
            }

            saveEnergyTypes(selectedEnergyTypes.length ? selectedEnergyTypes : inferEnergyTypes(builderDeck));
            window.location.href = cfg.routes.play;
        });

        document.querySelector('[data-continue-deck]')?.addEventListener('click', () => {
            window.location.href = cfg.routes.deck;
        });
    }

    function initDeckBuilder() {
        availableCards = (cfg.initialCards || []).map(normalizeCard);
        builderDeck = readDeck();
        selectedEnergyTypes = readEnergyTypes();
        bindEnergyPicker(document);
        bindDeckFilters();
        renderAvailableCards();
        renderBuilderDeck();

        document.querySelector('[data-battle-auto]')?.addEventListener('click', async () => {
            setLibraryStatus('Gerando deck automatico...');
            const result = await fetchAutoDeck();

            if (!result.deck.length) {
                setLibraryStatus('Nao foi possivel gerar um deck completo. Tente novamente.');
                return;
            }

            builderDeck = result.deck;
            selectedEnergyTypes = result.energyTypes.length ? result.energyTypes : inferEnergyTypes(builderDeck);
            saveDeck(builderDeck);
            saveEnergyTypes(selectedEnergyTypes);
            syncEnergyButtons();
            renderBuilderDeck();
            setLibraryStatus('Deck automatico gerado. Voce pode revisar antes de jogar.');
        });

        document.querySelector('[data-clear-deck]')?.addEventListener('click', () => {
            builderDeck = [];
            saveDeck(builderDeck);
            renderBuilderDeck();
        });

        document.querySelector('[data-save-start]')?.addEventListener('click', () => {
            const validation = validateDeck(builderDeck);
            updateDeckWarning(validation);

            if (!validation.valid) {
                return;
            }

            saveDeck(builderDeck);
            saveEnergyTypes(selectedEnergyTypes.length ? selectedEnergyTypes : inferEnergyTypes(builderDeck));
            localStorage.removeItem(BATTLE_KEY);
            window.location.href = cfg.routes.play;
        });

        document.querySelector('[data-available-cards]')?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-add-card-id]');
            if (!button) return;

            if (builderDeck.length >= DECK_SIZE) {
                setLibraryStatus('O deck ja tem 16 cartas.');
                return;
            }

            const card = availableCards.find((item) => item.id === button.dataset.addCardId);
            if (!card) return;

            builderDeck.push(normalizeCard(card));
            saveDeck(builderDeck);
            renderBuilderDeck();
        });

        document.querySelector('[data-my-deck]')?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-deck-index]');
            if (!button) return;

            const index = Number(button.dataset.removeDeckIndex);
            builderDeck.splice(index, 1);
            saveDeck(builderDeck);
            renderBuilderDeck();
        });
    }

    async function initBattle() {
        const savedDeck = readDeck();
        const validation = validateDeck(savedDeck);

        if (!validation.valid) {
            renderBattleUnavailable('Seu deck precisa ter 16 cartas e pelo menos um Pokemon Basico.');
            return;
        }

        const botDeckResult = await fetchAutoDeck();
        const botDeck = botDeckResult.deck.length ? botDeckResult.deck : savedDeck;
        selectedEnergyTypes = readEnergyTypes();

        battleState = createBattleState(
            savedDeck,
            botDeck,
            selectedEnergyTypes.length ? selectedEnergyTypes : inferEnergyTypes(savedDeck),
            botDeckResult.energyTypes.length ? botDeckResult.energyTypes : inferEnergyTypes(botDeck)
        );

        setupOpeningHands();
        bindBattleBoard();
        showBattleMessage('Escolha um Pokemon Basico da mao para ser ativo.');
        renderBattle();
    }

    function bindDeckFilters() {
        const form = document.querySelector('[data-card-filters]');
        if (!form) return;

        form.addEventListener('input', () => {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(loadAvailableCards, 400);
        });

        form.addEventListener('change', loadAvailableCards);

        document.querySelector('[data-clear-card-filters]')?.addEventListener('click', () => {
            form.reset();
            loadAvailableCards();
        });
    }

    async function loadAvailableCards() {
        const form = document.querySelector('[data-card-filters]');
        if (!form) return;

        setLibraryStatus('Buscando cartas...');
        const params = new URLSearchParams(new FormData(form));

        try {
            const response = await fetch(`${cfg.routes.cards}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('cards');
            }

            const data = await response.json();
            availableCards = (data.cards || []).map(normalizeCard);
            renderAvailableCards();
            setLibraryStatus(availableCards.length ? 'Cartas carregadas.' : 'Nenhuma carta encontrada com esses filtros.');
        } catch (error) {
            setLibraryStatus('Nao foi possivel carregar as cartas no momento.');
        }
    }

    async function fetchAutoDeck() {
        try {
            const response = await fetch(cfg.routes.autoDeck, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('deck');
            }

            const data = await response.json();
            const deck = (data.deck || []).map(normalizeCard).slice(0, DECK_SIZE);

            return {
                deck: deck.length === DECK_SIZE ? deck : [],
                energyTypes: normalizeEnergyTypes(data.energyTypes || []),
            };
        } catch (error) {
            return { deck: [], energyTypes: [] };
        }
    }

    function bindEnergyPicker(root) {
        root.querySelectorAll('[data-energy]').forEach((button) => {
            button.addEventListener('click', () => {
                const energy = normalizeEnergyType(button.dataset.energy);

                if (selectedEnergyTypes.includes(energy)) {
                    selectedEnergyTypes = selectedEnergyTypes.filter((item) => item !== energy);
                } else {
                    selectedEnergyTypes.push(energy);
                    if (selectedEnergyTypes.length > 3) {
                        selectedEnergyTypes.shift();
                    }
                }

                saveEnergyTypes(selectedEnergyTypes);
                syncEnergyButtons();
            });
        });

        syncEnergyButtons();
    }

    function syncEnergyButtons() {
        document.querySelectorAll('[data-energy]').forEach((button) => {
            const energy = normalizeEnergyType(button.dataset.energy);
            button.classList.toggle('is-active', selectedEnergyTypes.includes(energy));
        });

        document.querySelectorAll('[data-energy-counter]').forEach((counter) => {
            counter.textContent = `${selectedEnergyTypes.length}/3`;
        });
    }

    function applyEnergyColors(root) {
        root.querySelectorAll('[data-energy-orb]').forEach((orb) => {
            const energy = normalizeEnergyType(orb.dataset.energyOrb);
            orb.style.setProperty('--energy-color', energyColor(energy));
        });
    }

    function renderHomeDeck() {
        renderDeckPreview(document.querySelector('[data-deck-preview]'), builderDeck);

        const counter = document.querySelector('[data-deck-counter]');
        if (counter) counter.textContent = `${builderDeck.length}/16`;

        const startButton = document.querySelector('[data-start-battle]');
        if (startButton) startButton.disabled = !validateDeck(builderDeck).valid;

        const continueButton = document.querySelector('[data-continue-deck]');
        if (continueButton) continueButton.hidden = !builderDeck.length;

        if (builderDeck.length) {
            setStatus(validateDeck(builderDeck).valid ? 'Deck salvo pronto para jogar.' : 'Deck salvo incompleto.');
        }
    }

    function renderAvailableCards() {
        const container = document.querySelector('[data-available-cards]');
        if (!container) return;

        container.innerHTML = availableCards.map((card) => cardMiniHtml(card, {
            action: 'Adicionar',
            attr: `data-add-card-id="${escapeAttr(card.id)}"`,
        })).join('');

        const counter = document.querySelector('[data-library-counter]');
        if (counter) counter.textContent = String(availableCards.length);
        setLibraryStatus(availableCards.length ? 'Cartas carregadas.' : 'Nenhuma carta encontrada.');
    }

    function renderBuilderDeck() {
        const container = document.querySelector('[data-my-deck]');
        if (!container) return;

        container.innerHTML = builderDeck.map((card, index) => `
            <div class="battle-deck-row">
                <img src="${escapeAttr(card.image)}" alt="${escapeAttr(card.name)}" onerror="this.src='${PLACEHOLDER}'">
                <div>
                    <strong>${escapeHtml(card.name)}</strong>
                    <small>${escapeHtml(card.stage)} | HP ${card.hp}</small>
                </div>
                <button type="button" class="battle-row-button" data-remove-deck-index="${index}" aria-label="Remover ${escapeAttr(card.name)}">x</button>
            </div>
        `).join('');

        const counter = document.querySelector('[data-deck-counter]');
        if (counter) counter.textContent = `${builderDeck.length}/16`;

        updateDeckWarning(validateDeck(builderDeck));
    }

    function renderDeckPreview(container, cards) {
        if (!container) return;

        container.innerHTML = cards.map((card) => cardMiniHtml(card)).join('');
    }

    function cardMiniHtml(card, options) {
        const firstType = card.types[0] || 'Colorless';
        const typeBadges = card.types.map((type) => `
            <span class="battle-type-chip" style="--chip-color: ${energyColor(type)}">${escapeHtml(type)}</span>
        `).join('');
        const button = options?.action
            ? `<button type="button" class="battle-card-button" ${options.attr || ''}>${escapeHtml(options.action)}</button>`
            : '';

        return `
            <article class="battle-card-mini" style="--card-color: ${energyColor(firstType)}">
                <div class="battle-card-mini__image">
                    <img src="${escapeAttr(card.image)}" alt="${escapeAttr(card.name)}" loading="lazy" onerror="this.src='${PLACEHOLDER}'">
                </div>
                <div class="battle-card-mini__body">
                    <strong>${escapeHtml(card.name)}</strong>
                    <div class="battle-card-meta">
                        <span class="battle-stage-badge">${escapeHtml(card.stage)}</span>
                        <span>HP ${card.hp}</span>
                    </div>
                    <div class="battle-card-meta">${typeBadges}</div>
                    ${button}
                </div>
            </article>
        `;
    }

    function updateDeckWarning(validation) {
        const warning = document.querySelector('[data-deck-warning]');
        if (!warning) return;

        if (!builderDeck.length) {
            warning.textContent = 'Seu deck precisa ter 16 cartas e pelo menos um Pokemon Basico.';
            return;
        }

        if (!validation.valid) {
            warning.textContent = validation.message;
            return;
        }

        warning.textContent = validation.hasEvolutionWarning
            ? 'Algumas evolucoes podem nao ser jogaveis porque o Pokemon anterior nao esta no deck.'
            : 'Deck valido e pronto para batalha.';
    }

    function createBattleState(playerDeck, botDeck, playerEnergyTypes, botEnergyTypes) {
        localStorage.removeItem(BATTLE_KEY);

        return {
            phase: 'setup',
            turn: 1,
            currentPlayer: 'player',
            selectedOrb: false,
            pendingEvolutionId: null,
            promotionOwner: null,
            ended: false,
            player: {
                deck: shuffle(playerDeck.map((card) => createInstance(card, null))),
                hand: [],
                active: null,
                bench: [],
                discard: [],
                points: 0,
                energyTypes: playerEnergyTypes,
                currentOrb: null,
                hasAttachedEnergyThisTurn: false,
                hasAttacked: false,
            },
            bot: {
                deck: shuffle(botDeck.map((card) => createInstance(card, null))),
                hand: [],
                active: null,
                bench: [],
                discard: [],
                points: 0,
                energyTypes: botEnergyTypes,
                currentOrb: null,
                hasAttachedEnergyThisTurn: false,
                hasAttacked: false,
            },
            log: [],
        };
    }

    function setupOpeningHands() {
        drawOpeningHand('player');
        drawOpeningHand('bot');
        setupBotField();
    }

    function drawOpeningHand(owner) {
        const player = battleState[owner];

        for (let i = 0; i < 5; i += 1) {
            drawCard(owner, 0, false);
        }

        if (!player.hand.some(isBasicCard)) {
            const basicIndex = player.deck.findIndex(isBasicCard);
            if (basicIndex >= 0) {
                const [basic] = player.deck.splice(basicIndex, 1);
                basic.turnDrawn = 0;
                player.hand.push(basic);
            }
        }
    }

    function setupBotField() {
        const bot = battleState.bot;
        const activeIndex = bot.hand.findIndex(isBasicCard);

        if (activeIndex >= 0) {
            bot.active = playCardFromHand('bot', activeIndex, 1);
        }

        while (bot.bench.length < MAX_BENCH) {
            const index = bot.hand.findIndex(isBasicCard);
            if (index < 0) break;
            bot.bench.push(playCardFromHand('bot', index, 1));
        }
    }

    function bindBattleBoard() {
        if (battleBoardBound) return;
        battleBoardBound = true;

        document.querySelector('[data-battle-board]')?.addEventListener('click', handleBattleClick);
        document.querySelector('[data-end-turn]')?.addEventListener('click', endPlayerTurn);
        document.querySelector('[data-setup-ready]')?.addEventListener('click', beginBattle);
        document.querySelector('[data-modal-rematch]')?.addEventListener('click', () => {
            hideResultModal();
            initBattle();
        });
        document.querySelector('[data-modal-close]')?.addEventListener('click', hideResultModal);
    }

    function handleBattleClick(event) {
        const handCard = event.target.closest('[data-hand-index]');
        if (handCard) {
            handleHandCardClick(Number(handCard.dataset.handIndex));
            return;
        }

        const orb = event.target.closest('[data-current-orb]');
        if (orb) {
            if (battleState.phase === 'battle' && battleState.currentPlayer === 'player' && battleState.player.currentOrb) {
                battleState.selectedOrb = !battleState.selectedOrb;
                renderBattle();
            }
            return;
        }

        const fieldCard = event.target.closest('[data-field-instance]');
        if (fieldCard) {
            handleFieldCardClick(fieldCard);
            return;
        }

        const attackButton = event.target.closest('[data-attack-index]');
        if (attackButton) {
            performAttack('player', Number(attackButton.dataset.attackIndex));
        }
    }

    function handleHandCardClick(index) {
        const player = battleState.player;
        const card = player.hand[index];
        if (!card || battleState.currentPlayer !== 'player') return;

        if (battleState.promotionOwner === 'player') {
            showBattleMessage('Escolha um Pokemon do banco para entrar como ativo.');
            renderBattle();
            return;
        }

        if (battleState.phase === 'setup') {
            if (!isBasicCard(card)) {
                showBattleMessage('Somente Pokemon Basico pode entrar no campo inicial.');
                renderBattle();
                return;
            }

            if (!player.active) {
                player.active = playCardFromHand('player', index, 1);
                showBattleMessage(`${player.active.name} entrou como Pokemon ativo.`);
            } else if (player.bench.length < MAX_BENCH) {
                const played = playCardFromHand('player', index, 1);
                player.bench.push(played);
                showBattleMessage(`${played.name} foi para o banco.`);
            } else {
                showBattleMessage('O banco ja esta cheio.');
            }

            renderBattle();
            return;
        }

        if (battleState.phase !== 'battle') return;

        if (isBasicCard(card)) {
            if (!player.active) {
                player.active = playCardFromHand('player', index, battleState.turn);
                showBattleMessage(`${player.active.name} entrou como ativo.`);
            } else if (player.bench.length < MAX_BENCH) {
                const played = playCardFromHand('player', index, battleState.turn);
                player.bench.push(played);
                showBattleMessage(`${played.name} foi para o banco.`);
            } else {
                showBattleMessage('O banco esta cheio.');
            }
            renderBattle();
            return;
        }

        const targets = evolutionTargets(card, 'player');
        if (!targets.length) {
            showBattleMessage(card.turnDrawn >= battleState.turn
                ? 'Essa evolucao so podera ser usada no proximo turno.'
                : 'Essa carta ainda nao pode ser usada para evolucao.');
            battleState.pendingEvolutionId = null;
            renderBattle();
            return;
        }

        battleState.pendingEvolutionId = card.instanceId;
        showBattleMessage(`Clique no Pokemon em campo para evoluir para ${card.name}.`);
        renderBattle();
    }

    function handleFieldCardClick(fieldCard) {
        if (fieldCard.dataset.owner !== 'player') return;

        const instanceId = fieldCard.dataset.fieldInstance;
        const zone = fieldCard.dataset.zone;
        const index = Number(fieldCard.dataset.fieldIndex);

        if (battleState.promotionOwner === 'player') {
            if (zone === 'bench') {
                promoteBenchCard('player', index);
            } else {
                showBattleMessage('Escolha uma carta do banco para promover.');
                renderBattle();
            }
            return;
        }

        if (battleState.pendingEvolutionId) {
            evolvePokemon('player', instanceId, battleState.pendingEvolutionId);
            return;
        }

        if (battleState.selectedOrb && battleState.player.currentOrb) {
            attachEnergy(instanceId);
        }
    }

    function beginBattle() {
        if (!battleState.player.active) {
            showBattleMessage('Escolha um Pokemon Basico ativo antes de comecar.');
            renderBattle();
            return;
        }

        battleState.phase = 'battle';
        startPlayerTurn(true);
    }

    function startPlayerTurn(firstTurn) {
        if (battleState.ended) return;

        battleState.currentPlayer = 'player';
        battleState.player.hasAttachedEnergyThisTurn = false;
        battleState.player.hasAttacked = false;
        battleState.player.currentOrb = randomEnergy(battleState.player.energyTypes);
        battleState.selectedOrb = false;
        battleState.pendingEvolutionId = null;

        drawCard('player', battleState.turn, true);
        showBattleMessage(firstTurn ? 'Seu turno comecou.' : `Turno ${battleState.turn}: sua vez.`);
        showBattleMessage(`Uma orbe ${battleState.player.currentOrb} foi gerada.`);
        renderBattle();
    }

    function endPlayerTurn() {
        if (!battleState || battleState.phase !== 'battle' || battleState.currentPlayer !== 'player') return;

        battleState.player.currentOrb = null;
        battleState.selectedOrb = false;
        battleState.pendingEvolutionId = null;
        showBattleMessage('Voce finalizou o turno.');
        renderBattle();
        window.setTimeout(botTurn, 550);
    }

    async function botTurn() {
        if (battleState.ended) return;

        battleState.currentPlayer = 'bot';
        battleState.bot.hasAttachedEnergyThisTurn = false;
        battleState.bot.hasAttacked = false;
        battleState.bot.currentOrb = randomEnergy(battleState.bot.energyTypes);
        showBattleMessage('Turno do oponente.');
        drawCard('bot', battleState.turn, true);
        renderBattle();
        await wait(500);

        playBotBasics();
        renderBattle();
        await wait(500);

        evolveBot();
        renderBattle();
        await wait(500);

        attachBotEnergy();
        renderBattle();
        await wait(500);

        botAttack();
        if (battleState.ended) return;
        if (battleState.promotionOwner === 'player') {
            battleState.currentPlayer = 'player';
            renderBattle();
            return;
        }

        battleState.bot.currentOrb = null;
        battleState.turn += 1;
        await wait(650);
        startPlayerTurn(false);
    }

    function playBotBasics() {
        const bot = battleState.bot;

        if (!bot.active) {
            const activeIndex = bot.hand.findIndex(isBasicCard);
            if (activeIndex >= 0) {
                bot.active = playCardFromHand('bot', activeIndex, battleState.turn);
                showBattleMessage(`Oponente colocou ${bot.active.name} como ativo.`);
            }
        }

        while (bot.bench.length < MAX_BENCH) {
            const index = bot.hand.findIndex(isBasicCard);
            if (index < 0) break;
            const played = playCardFromHand('bot', index, battleState.turn);
            bot.bench.push(played);
            showBattleMessage(`Oponente colocou ${played.name} no banco.`);
        }
    }

    function evolveBot() {
        const bot = battleState.bot;

        for (const card of [...bot.hand]) {
            const targets = evolutionTargets(card, 'bot');
            if (targets.length) {
                evolvePokemon('bot', targets[0].instanceId, card.instanceId);
                return;
            }
        }
    }

    function attachBotEnergy() {
        const bot = battleState.bot;
        const target = bot.active || bot.bench[0];

        if (!target || !bot.currentOrb || bot.hasAttachedEnergyThisTurn) return;

        target.attachedEnergy.push(bot.currentOrb);
        bot.hasAttachedEnergyThisTurn = true;
        showBattleMessage(`Oponente anexou energia em ${target.name}.`);
        bot.currentOrb = null;
    }

    function botAttack() {
        const bot = battleState.bot;
        if (!bot.active || !battleState.player.active) return;

        const attacks = bot.active.attacks
            .map((attack, index) => ({ attack, index, damage: parseDamage(attack.damage) }))
            .filter((item) => canAttack(bot.active, item.attack))
            .sort((a, b) => b.damage - a.damage);

        if (!attacks.length) {
            showBattleMessage('Oponente nao tem energia suficiente para atacar.');
            return;
        }

        performAttack('bot', attacks[0].index);
    }

    function drawCard(owner, turnDrawn, announce) {
        const player = battleState[owner];
        const card = player.deck.shift();

        if (!card) {
            if (announce) showBattleMessage(owner === 'player' ? 'Seu deck acabou.' : 'Deck do oponente acabou.');
            return null;
        }

        card.turnDrawn = turnDrawn;
        player.hand.push(card);

        if (announce) {
            showBattleMessage(owner === 'player' ? 'Voce comprou uma carta.' : 'Oponente comprou uma carta.');
        }

        return card;
    }

    function playCardFromHand(owner, index, turnPlayed) {
        const player = battleState[owner];
        const [card] = player.hand.splice(index, 1);
        card.turnPlayed = turnPlayed;
        return card;
    }

    function attachEnergy(instanceId) {
        const target = findPlayerFieldCard(instanceId);
        const player = battleState.player;

        if (!target || !player.currentOrb || player.hasAttachedEnergyThisTurn) {
            showBattleMessage('Nao ha energia disponivel para anexar.');
            renderBattle();
            return;
        }

        target.attachedEnergy.push(player.currentOrb);
        showBattleMessage(`Energia ${player.currentOrb} anexada em ${target.name}.`);
        player.currentOrb = null;
        player.hasAttachedEnergyThisTurn = true;
        battleState.selectedOrb = false;
        renderBattle();
    }

    function evolvePokemon(owner, baseInstanceId, evolutionInstanceId) {
        const player = battleState[owner];
        const evolutionIndex = player.hand.findIndex((card) => card.instanceId === evolutionInstanceId);
        const evolutionCard = player.hand[evolutionIndex];
        const location = findFieldLocation(owner, baseInstanceId);

        if (!evolutionCard || !location || !canEvolve(location.card, evolutionCard, battleState.turn)) {
            showBattleMessage('Essa evolucao ainda nao e permitida.');
            battleState.pendingEvolutionId = null;
            renderBattle();
            return;
        }

        const [evolution] = player.hand.splice(evolutionIndex, 1);
        const base = location.card;
        const damageTaken = Math.max(0, base.maxHp - base.currentHp);
        const evolved = {
            ...evolution,
            attachedEnergy: [...base.attachedEnergy],
            currentHp: Math.max(10, evolution.maxHp - damageTaken),
            maxHp: evolution.maxHp,
            turnPlayed: battleState.turn,
            turnEvolved: battleState.turn,
            evolutionStack: [...(base.evolutionStack || []), compactCard(base)],
        };

        if (location.zone === 'active') {
            player.active = evolved;
        } else {
            player.bench[location.index] = evolved;
        }

        battleState.pendingEvolutionId = null;
        showBattleMessage(`${base.name} evoluiu para ${evolved.name}!`);
        renderBattle();
    }

    function performAttack(owner, attackIndex) {
        if (battleState.phase !== 'battle' || battleState.currentPlayer !== owner) return;

        const attacker = battleState[owner].active;
        const defenderOwner = owner === 'player' ? 'bot' : 'player';
        const defender = battleState[defenderOwner].active;
        const attack = attacker?.attacks?.[attackIndex];

        if (!attacker || !defender || !attack) return;

        if (battleState[owner].hasAttacked) {
            showBattleMessage('Este Pokemon ja atacou neste turno.');
            renderBattle();
            return;
        }

        if (!canAttack(attacker, attack)) {
            showBattleMessage('Energia insuficiente para esse ataque.');
            renderBattle();
            return;
        }

        let damage = parseDamage(attack.damage);
        damage = applyWeakness(damage, attacker, defender);
        defender.currentHp = Math.max(0, defender.currentHp - damage);
        battleState[owner].hasAttacked = true;

        showBattleMessage(`${attacker.name} usou ${attack.name}.`);
        showBattleMessage(`${defender.name} recebeu ${damage} de dano.`);

        if (defender.currentHp <= 0) {
            handleKnockout(owner, defenderOwner);
        }

        renderBattle();
    }

    function handleKnockout(attackerOwner, defenderOwner) {
        const attacker = battleState[attackerOwner];
        const defender = battleState[defenderOwner];
        const defeated = defender.active;

        if (!defeated) return;

        defender.discard.push(defeated);
        defender.active = null;
        attacker.points += 1;
        showBattleMessage(`${defeated.name} foi nocauteado.`);

        if (defender.bench.length) {
            if (defenderOwner === 'player') {
                battleState.promotionOwner = 'player';
                showBattleMessage('Escolha um Pokemon do banco para entrar como ativo.');
            } else {
                promoteBenchCard(defenderOwner, 0);
            }
        }

        checkWinCondition();
    }

    function promoteBenchCard(owner, index) {
        const player = battleState[owner];
        const [promoted] = player.bench.splice(index, 1);

        if (!promoted) return;

        player.active = promoted;
        battleState.promotionOwner = null;
        showBattleMessage(`${owner === 'player' ? 'Voce promoveu' : 'Oponente promoveu'} ${promoted.name}.`);

        if (owner === 'player' && battleState.currentPlayer === 'player' && battleState.phase === 'battle') {
            battleState.bot.currentOrb = null;
            battleState.turn += 1;
            startPlayerTurn(false);
            return;
        }

        renderBattle();
    }

    function checkWinCondition() {
        if (battleState.player.points >= POINTS_TO_WIN) {
            finishBattle(true, 'Voce fez 3 pontos e venceu a partida.');
            return;
        }

        if (battleState.bot.points >= POINTS_TO_WIN) {
            finishBattle(false, 'O oponente fez 3 pontos.');
            return;
        }

        if (!battleState.bot.active && !battleState.bot.bench.length) {
            finishBattle(true, 'O oponente ficou sem Pokemon em campo.');
            return;
        }

        if (!battleState.player.active && !battleState.player.bench.length) {
            finishBattle(false, 'Voce ficou sem Pokemon em campo.');
        }
    }

    function finishBattle(playerWon, message) {
        battleState.ended = true;
        battleState.phase = 'ended';
        showBattleMessage(playerWon ? 'Voce venceu a partida.' : 'Voce perdeu a partida.');
        renderBattle();
        showResultModal(playerWon ? 'Voce venceu!' : 'Voce perdeu!', message);
    }

    function renderBattle() {
        if (!battleState) return;

        const player = battleState.player;
        const bot = battleState.bot;

        setText('[data-player-points]', player.points);
        setText('[data-bot-points]', bot.points);
        setText('[data-player-deck-count]', player.deck.length);
        setText('[data-bot-deck-count]', bot.deck.length);
        setText('[data-hand-counter]', player.hand.length);
        setText('[data-turn-pill]', battleState.phase === 'setup'
            ? 'Preparacao'
            : (battleState.currentPlayer === 'player' ? `Turno ${battleState.turn}: Jogador` : `Turno ${battleState.turn}: Oponente`));

        renderField('[data-player-active]', player.active, 'player', 'active');
        renderField('[data-bot-active]', bot.active, 'bot', 'active');
        renderBench('[data-player-bench]', player.bench, 'player');
        renderBench('[data-bot-bench]', bot.bench, 'bot');
        renderHand();
        renderOrbZone();
        renderAttackPanel();
        renderLog();

        const readyButton = document.querySelector('[data-setup-ready]');
        if (readyButton) readyButton.hidden = !(battleState.phase === 'setup' && player.active);

        const endButton = document.querySelector('[data-end-turn]');
        if (endButton) {
            endButton.disabled = battleState.phase !== 'battle'
                || battleState.currentPlayer !== 'player'
                || battleState.ended
                || battleState.promotionOwner === 'player';
        }

        localStorage.setItem(BATTLE_KEY, JSON.stringify(battleState));
    }

    function renderField(selector, card, owner, zone, index) {
        const container = document.querySelector(selector);
        if (!container) return;
        container.innerHTML = card ? fieldCardHtml(card, owner, zone, index ?? 0, zone === 'active') : '';
    }

    function renderBench(selector, cards, owner) {
        const container = document.querySelector(selector);
        if (!container) return;
        container.innerHTML = cards.map((card, index) => fieldCardHtml(card, owner, 'bench', index, false)).join('');
    }

    function renderHand() {
        const container = document.querySelector('[data-player-hand]');
        if (!container) return;

        container.innerHTML = battleState.player.hand.map((card, index) => {
            const clickable = battleState.currentPlayer === 'player' && battleState.phase !== 'ended';
            return `
                <article class="battle-hand-card ${clickable ? 'is-clickable' : ''}" data-hand-index="${index}" style="--card-color: ${energyColor(card.types[0])}">
                    <img src="${escapeAttr(card.image)}" alt="${escapeAttr(card.name)}" onerror="this.src='${PLACEHOLDER}'">
                    <div>
                        <strong>${escapeHtml(card.name)}</strong>
                        <span class="battle-card-meta">${escapeHtml(card.stage)} | HP ${card.hp}</span>
                    </div>
                </article>
            `;
        }).join('');
    }

    function renderOrbZone() {
        const container = document.querySelector('[data-orb-zone]');
        if (!container) return;

        if (battleState.phase !== 'battle' || battleState.currentPlayer !== 'player') {
            container.innerHTML = '<span class="battle-muted">Aguardando turno do jogador.</span>';
            return;
        }

        if (!battleState.player.currentOrb) {
            container.innerHTML = '<span class="battle-muted">Energia do turno ja foi usada ou descartada.</span>';
            return;
        }

        const energy = battleState.player.currentOrb;
        container.innerHTML = `
            <button type="button" class="battle-current-orb ${battleState.selectedOrb ? 'is-selected' : ''}" data-current-orb>
                <span class="battle-orb" style="--energy-color: ${energyColor(energy)}">${escapeHtml(energy)}</span>
            </button>
        `;
    }

    function renderAttackPanel() {
        const container = document.querySelector('[data-attack-panel]');
        if (!container) return;

        const active = battleState.player.active;
        if (battleState.phase !== 'battle' || battleState.currentPlayer !== 'player' || !active) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = active.attacks.map((attack, index) => {
            const enabled = !battleState.player.hasAttacked && canAttack(active, attack) && !!battleState.bot.active;
            const cost = (attack.cost || []).join(' + ') || 'Sem custo';

            return `
                <button type="button" class="battle-attack-button" data-attack-index="${index}" ${enabled ? '' : 'disabled'}>
                    ${escapeHtml(attack.name)} | ${escapeHtml(cost)} | ${escapeHtml(attack.damage || '0')}
                </button>
            `;
        }).join('');
    }

    function renderLog() {
        const container = document.querySelector('[data-battle-log]');
        if (!container) return;
        container.innerHTML = battleState.log.slice(0, 5).map((line) => `<p>${escapeHtml(line)}</p>`).join('');
    }

    function fieldCardHtml(card, owner, zone, index, active) {
        const pending = battleState.pendingEvolutionId
            ? battleState[owner]?.hand?.find((item) => item.instanceId === battleState.pendingEvolutionId)
            : null;
        const canReceiveEnergy = owner === 'player'
            && battleState.selectedOrb
            && battleState.player.currentOrb
            && battleState.phase === 'battle'
            && battleState.currentPlayer === 'player'
            && !battleState.promotionOwner;
        const canEvolveTarget = owner === 'player' && pending && canEvolve(card, pending, battleState.turn);
        const canPromoteTarget = battleState.promotionOwner === owner && zone === 'bench';
        const classes = [
            'battle-field-card',
            active ? 'is-active-card' : '',
            canReceiveEnergy || canEvolveTarget || canPromoteTarget ? 'is-clickable' : '',
            canEvolveTarget || canPromoteTarget ? 'can-evolve-target' : '',
        ].filter(Boolean).join(' ');
        const hpPercent = Math.max(0, Math.min(100, Math.round((card.currentHp / card.maxHp) * 100)));
        const typeBadges = card.types.map((type) => `<span class="battle-type-chip" style="--chip-color: ${energyColor(type)}">${escapeHtml(type)}</span>`).join('');
        const energyDots = card.attachedEnergy.map((type) => `<span class="battle-energy-dot" title="${escapeAttr(type)}" style="--energy-color: ${energyColor(type)}"></span>`).join('');

        return `
            <article class="${classes}" data-owner="${owner}" data-zone="${zone}" data-field-index="${index}" data-field-instance="${escapeAttr(card.instanceId)}" style="--card-color: ${energyColor(card.types[0])}">
                <div class="battle-field-card__top">
                    <span class="battle-stage-badge">${escapeHtml(card.stage)}</span>
                    <span>${card.currentHp}/${card.maxHp} HP</span>
                </div>
                <div class="battle-field-card__image">
                    <img src="${escapeAttr(card.image)}" alt="${escapeAttr(card.name)}" onerror="this.src='${PLACEHOLDER}'">
                </div>
                <strong class="battle-field-card__name">${escapeHtml(card.name)}</strong>
                <div class="battle-hp-bar" style="--hp-percent: ${hpPercent}%"><span></span></div>
                <div class="battle-card-meta">${typeBadges}</div>
                <div class="battle-energy-row">${energyDots || '<span>Sem energia</span>'}</div>
            </article>
        `;
    }

    function renderBattleUnavailable(message) {
        const board = document.querySelector('[data-battle-board]');
        if (!board) return;

        board.innerHTML = `
            <section class="battle-panel">
                <p class="pokedex-kicker">Deck necessario</p>
                <h2>Nao foi possivel iniciar</h2>
                <p class="battle-muted">${escapeHtml(message)}</p>
                <div class="battle-actions-row">
                    <a class="pokedex-action pokedex-action--blue" href="${escapeAttr(cfg.routes.deck)}">Montar deck</a>
                    <a class="pokedex-action pokedex-action--light" href="${escapeAttr(cfg.routes.home)}">Voltar ao inicio</a>
                </div>
            </section>
        `;
    }

    function canAttack(card, attack) {
        const cost = (attack.cost || []).map(normalizeEnergyType);
        if (!cost.length) return true;

        const attached = [...(card.attachedEnergy || []).map(normalizeEnergyType)];
        let colorlessCost = 0;

        for (const required of cost) {
            if (required === 'Colorless') {
                colorlessCost += 1;
                continue;
            }

            const index = attached.findIndex((energy) => energy === required);
            if (index < 0) {
                return false;
            }
            attached.splice(index, 1);
        }

        return attached.length >= colorlessCost;
    }

    function parseDamage(damageText) {
        const text = String(damageText || '').trim();
        const match = text.match(/\d+/);
        return match ? Number(match[0]) : 0;
    }

    function applyWeakness(damage, attacker, defender) {
        const attackerType = normalizeEnergyType(attacker.types[0] || 'Colorless');
        const weakness = (defender.weaknesses || []).find((item) => normalizeEnergyType(item.type || '') === attackerType);

        if (!weakness) return damage;

        const value = String(weakness.value || '');
        if (value.includes('2')) return damage * 2;

        const bonus = value.match(/\d+/);
        return bonus ? damage + Number(bonus[0]) : damage;
    }

    function canEvolve(baseCard, evolutionCard, currentTurn) {
        if (!baseCard || !evolutionCard || !evolutionCard.evolvesFrom) return false;
        if (cleanName(evolutionCard.evolvesFrom) !== cleanName(baseCard.name)) return false;
        if (!(baseCard.turnPlayed < currentTurn)) return false;
        if (!(evolutionCard.turnDrawn < currentTurn)) return false;

        const baseStage = stageRank(baseCard.stage);
        const evolutionStage = stageRank(evolutionCard.stage);
        return evolutionStage === baseStage + 1;
    }

    function evolutionTargets(evolutionCard, owner) {
        const player = battleState[owner];
        const field = [player.active, ...player.bench].filter(Boolean);
        return field.filter((card) => canEvolve(card, evolutionCard, battleState.turn));
    }

    function stageRank(stage) {
        if (stage === 'Stage 3') return 3;
        if (stage === 'Stage 2') return 2;
        if (stage === 'Stage 1') return 1;
        return 0;
    }

    function findPlayerFieldCard(instanceId) {
        const location = findFieldLocation('player', instanceId);
        return location?.card || null;
    }

    function findFieldLocation(owner, instanceId) {
        const player = battleState[owner];

        if (player.active?.instanceId === instanceId) {
            return { zone: 'active', index: 0, card: player.active };
        }

        const index = player.bench.findIndex((card) => card.instanceId === instanceId);
        if (index >= 0) {
            return { zone: 'bench', index, card: player.bench[index] };
        }

        return null;
    }

    function showBattleMessage(message) {
        if (!battleState) return;
        battleState.log.unshift(message);
        battleState.log = battleState.log.slice(0, 24);
    }

    function showResultModal(title, message) {
        const modal = document.querySelector('[data-battle-modal]');
        if (!modal) return;

        setText('[data-modal-title]', title);
        setText('[data-modal-message]', message);
        modal.hidden = false;
    }

    function hideResultModal() {
        const modal = document.querySelector('[data-battle-modal]');
        if (modal) modal.hidden = true;
    }

    function validateDeck(deck) {
        if (deck.length !== DECK_SIZE) {
            return { valid: false, message: 'Seu deck precisa ter 16 cartas e pelo menos um Pokemon Basico.', hasEvolutionWarning: false };
        }

        if (!deck.some(isBasicCard)) {
            return { valid: false, message: 'Seu deck precisa ter pelo menos um Pokemon Basico.', hasEvolutionWarning: false };
        }

        const names = deck.map((card) => cleanName(card.name));
        const hasEvolutionWarning = deck.some((card) => !isBasicCard(card) && !names.includes(cleanName(card.evolvesFrom || '')));

        return { valid: true, message: 'Deck valido.', hasEvolutionWarning };
    }

    function isBasicCard(card) {
        return (card?.stage || stageFromSubtypes(card?.subtypes || [])) === 'Basic';
    }

    function normalizeCard(card) {
        const subtypes = Array.isArray(card.subtypes) ? card.subtypes : [];
        const types = normalizeEnergyTypes(card.types && card.types.length ? card.types : ['Colorless']);
        const hp = Number.parseInt(String(card.hp || card.maxHp || 50).replace(/\D+/g, ''), 10) || 50;

        return {
            id: String(card.id || card.cardId || uniqueId('card')),
            name: String(card.name || 'Pokemon'),
            image: String(card.image || card.images?.large || card.images?.small || PLACEHOLDER),
            hp: Math.max(30, hp),
            types,
            supertype: String(card.supertype || 'Pokemon'),
            subtypes,
            stage: card.stage || stageFromSubtypes(subtypes),
            evolvesFrom: card.evolvesFrom || null,
            attacks: normalizeAttacks(card.attacks),
            weaknesses: Array.isArray(card.weaknesses) ? card.weaknesses : [],
            resistances: Array.isArray(card.resistances) ? card.resistances : [],
            retreatCost: Array.isArray(card.retreatCost) ? card.retreatCost : [],
            rarity: String(card.rarity || 'Sem raridade'),
            set: card.set || { name: 'Colecao' },
        };
    }

    function normalizeAttacks(attacks) {
        if (!Array.isArray(attacks) || !attacks.length) {
            return [{
                name: 'Investida',
                cost: ['Colorless'],
                damage: '10',
                text: '',
                convertedEnergyCost: 1,
            }];
        }

        return attacks.map((attack) => ({
            name: String(attack.name || 'Ataque'),
            cost: normalizeEnergyTypes(attack.cost || ['Colorless']),
            damage: String(attack.damage || '0'),
            text: String(attack.text || ''),
            convertedEnergyCost: Number(attack.convertedEnergyCost || (attack.cost || []).length || 0),
        }));
    }

    function createInstance(card, turnDrawn) {
        const normalized = normalizeCard(card);
        const maxHp = normalized.hp;

        return {
            ...normalized,
            instanceId: uniqueId('inst'),
            cardId: normalized.id,
            maxHp,
            currentHp: maxHp,
            turnDrawn,
            turnPlayed: null,
            turnEvolved: null,
            attachedEnergy: [],
            evolutionStack: [],
        };
    }

    function compactCard(card) {
        return {
            cardId: card.cardId || card.id,
            name: card.name,
            image: card.image,
            stage: card.stage,
            types: card.types,
        };
    }

    function stageFromSubtypes(subtypes) {
        if (subtypes.includes('Stage 3')) return 'Stage 3';
        if (subtypes.includes('Stage 2')) return 'Stage 2';
        if (subtypes.includes('Stage 1')) return 'Stage 1';
        return 'Basic';
    }

    function inferEnergyTypes(deck) {
        const types = [];

        deck.forEach((card) => {
            normalizeCard(card).types.forEach((type) => {
                if (!types.includes(type)) {
                    types.push(type);
                }
            });
        });

        return types.length ? types.slice(0, 3) : ['Colorless'];
    }

    function randomEnergy(types) {
        const pool = normalizeEnergyTypes(types.length ? types : ['Colorless']);
        return pool[Math.floor(Math.random() * pool.length)] || 'Colorless';
    }

    function normalizeEnergyTypes(types) {
        return [...new Set((Array.isArray(types) ? types : [types]).map(normalizeEnergyType))].filter(Boolean);
    }

    function normalizeEnergyType(type) {
        const value = String(type || 'Colorless').toLowerCase();
        if (value === 'electric' || value === 'lightning') return 'Lightning';
        if (value === 'dark' || value === 'darkness') return 'Darkness';
        if (value === 'steel' || value === 'metal') return 'Metal';
        if (value === 'normal' || value === 'colorless') return 'Colorless';
        const title = value.charAt(0).toUpperCase() + value.slice(1);
        return ENERGY_COLORS[title] ? title : 'Colorless';
    }

    function energyColor(type) {
        return ENERGY_COLORS[normalizeEnergyType(type)] || ENERGY_COLORS.Colorless;
    }

    function cleanName(name) {
        return String(name || '')
            .toLowerCase()
            .replace(/\s+\(.+\)$/g, '')
            .replace(/[^a-z0-9 ]/g, '')
            .trim();
    }

    function shuffle(items) {
        const copy = [...items];
        for (let i = copy.length - 1; i > 0; i -= 1) {
            const j = Math.floor(Math.random() * (i + 1));
            [copy[i], copy[j]] = [copy[j], copy[i]];
        }
        return copy;
    }

    function readDeck() {
        try {
            const deck = JSON.parse(localStorage.getItem(DECK_KEY) || '[]');
            return Array.isArray(deck) ? deck.map(normalizeCard) : [];
        } catch (error) {
            return [];
        }
    }

    function saveDeck(deck) {
        localStorage.setItem(DECK_KEY, JSON.stringify(deck.map(normalizeCard)));
    }

    function readEnergyTypes() {
        try {
            return normalizeEnergyTypes(JSON.parse(localStorage.getItem(ENERGY_KEY) || '[]')).slice(0, 3);
        } catch (error) {
            return [];
        }
    }

    function saveEnergyTypes(types) {
        localStorage.setItem(ENERGY_KEY, JSON.stringify(normalizeEnergyTypes(types).slice(0, 3)));
    }

    function setStatus(message) {
        setText('[data-battle-status]', message);
    }

    function setLibraryStatus(message) {
        setText('[data-library-status]', message);
    }

    function setText(selector, value) {
        const element = document.querySelector(selector);
        if (element) element.textContent = String(value);
    }

    function uniqueId(prefix) {
        if (window.crypto?.randomUUID) {
            return `${prefix}-${window.crypto.randomUUID()}`;
        }

        return `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2)}`;
    }

    function wait(ms) {
        return new Promise((resolve) => window.setTimeout(resolve, ms));
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttr(value) {
        return escapeHtml(value).replace(/`/g, '&#096;');
    }
})();
