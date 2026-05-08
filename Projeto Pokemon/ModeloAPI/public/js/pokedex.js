(function () {
    const config = window.POKEDEX_CONFIG || {};
    const API_BASE = config.apiBase || 'https://pokeapi.co/api/v2';
    const MAX_POKEMON = Number(config.maxPokemon || 1025);
    const PER_PAGE = Number(config.perPage || 44);
    const ROUTE_BASE = config.routeBase || '/pokedex';
    const TYPE_COLORS = config.typeColors || {};
    const TYPE_NAMES = config.typeNames || Object.keys(TYPE_COLORS);
    const CUSTOM_POKEMONS = normalizeCustomPokemons(config.customPokemons || []);

    const LIST_CACHE_KEY = 'pokedex_cache_v1';
    const EVOLUTION_CACHE_KEY = 'pokedex_evolution_cache_v1';
    const LIST_CACHE_TTL = 1000 * 60 * 60 * 24 * 3;
    const EVOLUTION_CACHE_TTL = 1000 * 60 * 60 * 24 * 14;

    const GENERATIONS = [
        { value: '1', label: 'Geracao 1', min: 1, max: 151 },
        { value: '2', label: 'Geracao 2', min: 152, max: 251 },
        { value: '3', label: 'Geracao 3', min: 252, max: 386 },
        { value: '4', label: 'Geracao 4', min: 387, max: 493 },
        { value: '5', label: 'Geracao 5', min: 494, max: 649 },
        { value: '6', label: 'Geracao 6', min: 650, max: 721 },
        { value: '7', label: 'Geracao 7', min: 722, max: 809 },
        { value: '8', label: 'Geracao 8', min: 810, max: 905 },
        { value: '9', label: 'Geracao 9', min: 906, max: 1025 },
    ];

    const PLACEHOLDER_IMAGE = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 220">
            <defs>
                <linearGradient id="bg" x1="0" x2="1" y1="0" y2="1">
                    <stop offset="0" stop-color="#273244"/>
                    <stop offset="1" stop-color="#0b111d"/>
                </linearGradient>
            </defs>
            <rect width="220" height="220" rx="18" fill="url(#bg)"/>
            <circle cx="110" cy="110" r="58" fill="#f8fbff" opacity="0.16"/>
            <path d="M52 110h116" stroke="#f8fbff" stroke-width="12" opacity="0.34"/>
            <circle cx="110" cy="110" r="22" fill="#111827" stroke="#f8fbff" stroke-width="10" opacity="0.5"/>
            <text x="110" y="180" text-anchor="middle" fill="#9df5d0" font-family="Arial" font-size="18" font-weight="700">Sem imagem</text>
        </svg>
    `);

    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

    function fetchJson(url, timeoutMs = 20000) {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), timeoutMs);

        return fetch(url, {
            headers: { Accept: 'application/json' },
            signal: controller.signal,
        }).then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            return response.json();
        }).finally(() => {
            clearTimeout(timeout);
        });
    }

    function readCache(key, ttl) {
        try {
            const raw = localStorage.getItem(key);
            if (!raw) return null;

            const parsed = JSON.parse(raw);
            if (!parsed || Date.now() - Number(parsed.timestamp || 0) > ttl) {
                localStorage.removeItem(key);
                return null;
            }

            return parsed.data || null;
        } catch (error) {
            return null;
        }
    }

    function writeCache(key, data) {
        try {
            localStorage.setItem(key, JSON.stringify({
                timestamp: Date.now(),
                data,
            }));
        } catch (error) {
            // Cache is an optimization; the interface keeps working without it.
        }
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[character]));
    }

    function normalizeSearch(value) {
        return String(value || '').trim().toLowerCase();
    }

    function formatName(value) {
        return String(value || 'pokemon')
            .replace(/-/g, ' ')
            .split(' ')
            .filter(Boolean)
            .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
    }

    function formatId(id) {
        const numericId = Number(id) || 0;
        return `#${String(numericId).padStart(numericId < 1000 ? 3 : 4, '0')}`;
    }

    function formatStatName(name) {
        const map = {
            hp: 'HP',
            attack: 'Attack',
            defense: 'Defense',
            'special-attack': 'Special Attack',
            'special-defense': 'Special Defense',
            speed: 'Speed',
        };

        return map[name] || formatName(name);
    }

    function typeColor(type) {
        return TYPE_COLORS[type] || '#A8A77A';
    }

    function hexToRgb(hex) {
        const clean = String(hex || '').replace('#', '');
        if (!/^[0-9a-f]{6}$/i.test(clean)) return null;

        return {
            r: parseInt(clean.slice(0, 2), 16),
            g: parseInt(clean.slice(2, 4), 16),
            b: parseInt(clean.slice(4, 6), 16),
        };
    }

    function rgbToHex(rgb) {
        return `#${[rgb.r, rgb.g, rgb.b].map((value) => {
            return Math.max(0, Math.min(255, Math.round(value))).toString(16).padStart(2, '0');
        }).join('')}`;
    }

    function mixColor(colorA, colorB, weight = 0.55) {
        const a = hexToRgb(colorA);
        const b = hexToRgb(colorB);
        if (!a || !b) return colorA || '#A8A77A';

        return rgbToHex({
            r: a.r * weight + b.r * (1 - weight),
            g: a.g * weight + b.g * (1 - weight),
            b: a.b * weight + b.b * (1 - weight),
        });
    }

    function badge(type) {
        return `<span class="pokedex-type-badge" style="--badge-color:${typeColor(type)}">${escapeHtml(formatName(type))}</span>`;
    }

    function officialArtworkUrl(id) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${id}.png`;
    }

    function spriteUrl(id) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${id}.png`;
    }

    function imageFromPokemon(pokemon, shiny = false) {
        if (!pokemon) return PLACEHOLDER_IMAGE;

        const sprites = pokemon.sprites || {};
        if (shiny) {
            return sprites.other?.['official-artwork']?.front_shiny
                || sprites.other?.home?.front_shiny
                || sprites.front_shiny
                || imageFromPokemon(pokemon, false);
        }

        return sprites.other?.['official-artwork']?.front_default
            || sprites.other?.home?.front_default
            || sprites.front_default
            || (pokemon.id ? officialArtworkUrl(pokemon.id) : PLACEHOLDER_IMAGE);
    }

    function attachImageFallbacks(root = document) {
        $$('img[data-fallback]', root).forEach((image) => {
            if (image.dataset.fallbackReady === 'true') return;
            image.dataset.fallbackReady = 'true';
            image.addEventListener('error', () => {
                const fallback = image.dataset.fallback;
                if (fallback && image.src !== fallback) {
                    image.src = fallback;
                    image.dataset.fallback = PLACEHOLDER_IMAGE;
                    return;
                }

                image.src = PLACEHOLDER_IMAGE;
            });
        });
    }

    function extractIdFromUrl(url) {
        const match = String(url || '').match(/\/(\d+)\/?$/);
        return match ? Number(match[1]) : null;
    }

    function generationForId(id) {
        return GENERATIONS.find((generation) => id >= generation.min && id <= generation.max) || null;
    }

    async function mapWithConcurrency(items, limit, callback) {
        const results = new Array(items.length);
        let nextIndex = 0;

        async function worker() {
            while (nextIndex < items.length) {
                const current = nextIndex;
                nextIndex += 1;
                results[current] = await callback(items[current], current);
            }
        }

        const workers = Array.from({ length: Math.min(limit, items.length) }, worker);
        await Promise.all(workers);
        return results;
    }

    function parseEvolutionChain(root) {
        const nodes = [];

        function walk(node, stage) {
            if (!node) return;

            const name = node.species?.name || '';
            const id = extractIdFromUrl(node.species?.url);

            if (name && id) {
                nodes.push({ id, name, stage });
            }

            (node.evolves_to || []).forEach((child) => walk(child, stage + 1));
        }

        walk(root, 1);

        return {
            nodes,
            stageCount: nodes.reduce((max, node) => Math.max(max, node.stage), 1),
        };
    }

    function cleanDescription(text) {
        return String(text || '')
            .replace(/\f/g, ' ')
            .replace(/\n/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function bestDescription(species) {
        const entries = species?.flavor_text_entries || [];
        const priorities = ['pt-br', 'pt', 'en'];

        for (const language of priorities) {
            const found = entries.find((entry) => String(entry.language?.name || '').toLowerCase() === language);
            if (found?.flavor_text) {
                return cleanDescription(found.flavor_text);
            }
        }

        return 'Descricao nao disponivel.';
    }

    function typeListFromPokemon(pokemon) {
        return (pokemon.types || [])
            .slice()
            .sort((a, b) => Number(a.slot || 0) - Number(b.slot || 0))
            .map((entry) => entry.type?.name)
            .filter(Boolean)
            .slice(0, 2);
    }

    function normalizeCustomPokemons(items) {
        return (items || []).map((pokemon) => ({
            id: Number(pokemon.id || pokemon.pokemon_id),
            name: String(pokemon.name || 'pokemon').toLowerCase().replace(/\s+/g, '-'),
            displayName: pokemon.name || 'Pokemon criado',
            types: Array.isArray(pokemon.types) && pokemon.types.length ? pokemon.types.slice(0, 2) : ['normal'],
            image: pokemon.image || null,
            sprite: pokemon.sprite || null,
            height: pokemon.height || null,
            weight: pokemon.weight || null,
            description: pokemon.description || 'Descricao nao disponivel.',
            abilities: Array.isArray(pokemon.abilities) ? pokemon.abilities : [],
            generation: 'created',
            isCustom: true,
            detailUrl: pokemon.detailUrl || `${ROUTE_BASE}/${pokemon.id || pokemon.pokemon_id}`,
        })).filter((pokemon) => pokemon.id > MAX_POKEMON);
    }

    function mergeOfficialAndCustom(officialPokemons) {
        const officialOnly = (officialPokemons || []).filter((pokemon) => !pokemon.isCustom && Number(pokemon.id) <= MAX_POKEMON);
        const customIds = new Set(CUSTOM_POKEMONS.map((pokemon) => pokemon.id));
        const withoutDuplicateCustom = officialOnly.filter((pokemon) => !customIds.has(Number(pokemon.id)));

        return withoutDuplicateCustom.concat(CUSTOM_POKEMONS).sort((a, b) => a.id - b.id);
    }

    function chooseMoveDetail(move) {
        const details = move.version_group_details || [];
        const latest = details[details.length - 1] || {};
        const method = latest.move_learn_method?.name ? formatName(latest.move_learn_method.name) : 'Metodo desconhecido';
        const level = Number(latest.level_learned_at || 0);

        return level > 0 ? `${method} Nv. ${level}` : method;
    }

    function resetActiveTab() {
        $$('.screen-tab').forEach((button) => {
            button.classList.toggle('is-active', button.dataset.tab === 'stats');
        });
        $$('.screen-pane').forEach((pane) => {
            pane.classList.toggle('is-active', pane.id === 'statsPane');
        });
    }

    function setActiveTab(tabName) {
        $$('.screen-tab').forEach((button) => {
            button.classList.toggle('is-active', button.dataset.tab === tabName);
        });
        $$('.screen-pane').forEach((pane) => {
            pane.classList.toggle('is-active', pane.id === `${tabName}Pane`);
        });
    }

    function initListPage() {
        const grid = $('#pokemonGrid');
        const listLoading = $('#listLoading');
        const loadingProgress = $('#loadingProgress');
        const listError = $('#listError');
        const emptyState = $('#emptyState');
        const emptyStateTitle = $('#emptyState strong');
        const emptyStateHint = $('#emptyStateHint');
        const loadedCount = $('#loadedCount');
        const cacheStatus = $('#cacheStatus');
        const searchInput = $('#searchInput');
        const generationFilter = $('#generationFilter');
        const sortSelect = $('#sortSelect');
        const clearFiltersButton = $('#clearFiltersButton');
        const typeFilterGrid = $('#typeFilterGrid');
        const prevPage = $('#prevPage');
        const nextPage = $('#nextPage');
        const pageNumbers = $('#pageNumbers');
        const pageSummary = $('#pageSummary');
        const pagination = $('#pagination');
        const retryListButton = $('#retryListButton');

        if (!grid) return;

        let allPokemon = [];
        let filteredPokemon = [];
        let currentPage = 1;
        let selectedTypes = [];
        let evolutionCache = readCache(EVOLUTION_CACHE_KEY, EVOLUTION_CACHE_TTL) || {};
        const pendingEvolution = new Map();

        function setLoading(message) {
            listLoading.classList.add('is-visible');
            listLoading.hidden = false;
            listError.hidden = true;
            if (loadingProgress && message) loadingProgress.textContent = message;
        }

        function clearLoading() {
            listLoading.classList.remove('is-visible');
            listLoading.hidden = true;
        }

        function showError() {
            clearLoading();
            grid.innerHTML = '';
            listError.hidden = false;
            emptyState.hidden = true;
            pagination.hidden = true;
            loadedCount.textContent = '0 Pokemon carregados';
            cacheStatus.textContent = 'Falha ao conectar com a PokeAPI';
        }

        async function loadData(forceRefresh = false) {
            setLoading('Buscando lista principal...');
            listError.hidden = true;
            emptyState.hidden = true;
            pagination.hidden = true;

            try {
                if (!forceRefresh) {
                    const cached = readCache(LIST_CACHE_KEY, LIST_CACHE_TTL);
                    if (cached?.pokemons?.length) {
                        allPokemon = mergeOfficialAndCustom(cached.pokemons);
                        loadedCount.textContent = `${allPokemon.length} Pokemon carregados`;
                        cacheStatus.textContent = 'Dados carregados do cache local';
                        clearLoading();
                        applyFilters(true);
                        return;
                    }
                }

                const pokemons = await buildMainPokemonList((message) => {
                    if (loadingProgress) loadingProgress.textContent = message;
                });

                allPokemon = mergeOfficialAndCustom(pokemons);
                writeCache(LIST_CACHE_KEY, { pokemons });
                loadedCount.textContent = `${allPokemon.length} Pokemon carregados`;
                cacheStatus.textContent = 'Dados atualizados pela PokeAPI';
                clearLoading();
                applyFilters(true);
            } catch (error) {
                showError();
            }
        }

        async function buildMainPokemonList(onProgress) {
            onProgress('Buscando os 1025 Pokemon principais...');
            const list = await fetchJson(`${API_BASE}/pokemon?limit=${MAX_POKEMON}&offset=0`);
            const baseItems = (list.results || [])
                .map((item) => ({
                    id: extractIdFromUrl(item.url),
                    name: item.name,
                    url: item.url,
                }))
                .filter((item) => item.id >= 1 && item.id <= MAX_POKEMON)
                .slice(0, MAX_POKEMON);

            if (!baseItems.length) {
                throw new Error('Lista principal vazia');
            }

            onProgress('Mapeando tipagens...');
            const typeMap = new Map(baseItems.map((item) => [item.id, []]));
            let typeFailures = 0;

            await mapWithConcurrency(TYPE_NAMES, 6, async (type) => {
                try {
                    const data = await fetchJson(`${API_BASE}/type/${type}`);
                    (data.pokemon || []).forEach((entry) => {
                        const id = extractIdFromUrl(entry.pokemon?.url);
                        if (!id || id < 1 || id > MAX_POKEMON) return;
                        const current = typeMap.get(id) || [];
                        if (!current.includes(type)) current.push(type);
                        typeMap.set(id, current);
                    });
                } catch (error) {
                    typeFailures += 1;
                }
            });

            if (typeFailures >= TYPE_NAMES.length) {
                throw new Error('Falha ao carregar tipagens');
            }

            onProgress('Montando cards...');

            return baseItems.map((item) => {
                const types = (typeMap.get(item.id) || ['normal'])
                    .sort((a, b) => TYPE_NAMES.indexOf(a) - TYPE_NAMES.indexOf(b))
                    .slice(0, 2);
                const generation = generationForId(item.id);

                return {
                    id: item.id,
                    name: item.name,
                    types: types.length ? types : ['normal'],
                    image: officialArtworkUrl(item.id),
                    sprite: spriteUrl(item.id),
                    generation: generation?.value || '',
                };
            });
        }

        function applyFilters(resetPage = false) {
            const query = normalizeSearch(searchInput.value);
            const generation = generationFilter.value;
            const sort = sortSelect.value;

            if (resetPage) currentPage = 1;

            if (generation === 'created') {
                filteredPokemon = allPokemon.filter((pokemon) => pokemon.isCustom);
                if (query) {
                    filteredPokemon = filteredPokemon.filter((pokemon) => {
                        const numericQuery = query.replace(/^#/, '');
                        if (/^\d+$/.test(numericQuery)) {
                            return Number(numericQuery) === pokemon.id;
                        }

                        return pokemon.name.includes(query) || formatName(pokemon.displayName || pokemon.name).toLowerCase().includes(query);
                    });
                }
                if (selectedTypes.length) {
                    filteredPokemon = filteredPokemon.filter((pokemon) => selectedTypes.every((type) => pokemon.types.includes(type)));
                }
                filteredPokemon.sort((a, b) => sortPokemon(a, b, sort));
                renderPage(filteredPokemon.length ? null : 'Nenhum Pokemon criado ainda.');
                return;
            }

            filteredPokemon = allPokemon.filter((pokemon) => {
                if (generation !== 'all') {
                    if (pokemon.isCustom) return false;
                    const selectedGeneration = GENERATIONS.find((entry) => entry.value === generation);
                    if (!selectedGeneration || pokemon.id < selectedGeneration.min || pokemon.id > selectedGeneration.max) {
                        return false;
                    }
                }

                if (selectedTypes.length && !selectedTypes.every((type) => pokemon.types.includes(type))) {
                    return false;
                }

                if (!query) return true;

                const numericQuery = query.replace(/^#/, '');
                if (/^\d+$/.test(numericQuery)) {
                    return Number(numericQuery) === pokemon.id;
                }

                return pokemon.name.includes(query) || formatName(pokemon.displayName || pokemon.name).toLowerCase().includes(query);
            });

            filteredPokemon.sort((a, b) => sortPokemon(a, b, sort));

            renderPage();
        }

        function sortPokemon(a, b, sort) {
            const nameA = a.displayName || a.name;
            const nameB = b.displayName || b.name;

            if (sort === 'id-desc') return b.id - a.id;
            if (sort === 'name-asc') return nameA.localeCompare(nameB);
            if (sort === 'name-desc') return nameB.localeCompare(nameA);
            return a.id - b.id;
        }

        function renderPage(customEmptyMessage = null) {
            const totalPages = Math.max(1, Math.ceil(filteredPokemon.length / PER_PAGE));
            currentPage = Math.max(1, Math.min(currentPage, totalPages));

            if (!filteredPokemon.length) {
                grid.innerHTML = '';
                emptyState.hidden = false;
                emptyStateTitle.textContent = customEmptyMessage || 'Nenhum Pokemon encontrado com esses filtros.';
                emptyStateHint.textContent = customEmptyMessage
                    ? 'Esse espaco sera usado na proxima etapa do sistema.'
                    : 'Ajuste a busca ou limpe os tipos selecionados.';
                pagination.hidden = true;
                return;
            }

            emptyState.hidden = true;
            pagination.hidden = false;

            const start = (currentPage - 1) * PER_PAGE;
            const pageItems = filteredPokemon.slice(start, start + PER_PAGE);

            grid.innerHTML = pageItems.map(cardTemplate).join('');
            attachImageFallbacks(grid);
            hydrateEvolutionCounts(pageItems);
            renderPagination(totalPages);
        }

        function cardTemplate(pokemon) {
            const primary = pokemon.types[0] || 'normal';
            const secondaryColor = pokemon.types[1]
                ? typeColor(pokemon.types[1])
                : mixColor(typeColor(primary), '#08121e', 0.56);
            const generation = generationForId(pokemon.id);
            const href = pokemon.detailUrl || `${ROUTE_BASE}/${pokemon.id}`;
            const displayName = pokemon.displayName || formatName(pokemon.name);
            const imageUrl = pokemon.image || PLACEHOLDER_IMAGE;
            const fallbackUrl = pokemon.sprite || PLACEHOLDER_IMAGE;

            return `
                <a
                    class="pokemon-dex-card"
                    href="${href}"
                    data-pokemon-id="${pokemon.id}"
                    style="--primary-type-color:${typeColor(primary)}; --secondary-type-color:${secondaryColor}"
                    aria-label="Abrir detalhes de ${escapeHtml(displayName)}"
                >
                    <div class="pokemon-card-top">
                        <span class="pokemon-id">${formatId(pokemon.id)}</span>
                        <span class="pokemon-gen">${pokemon.isCustom ? 'Custom' : (generation ? `Gen ${generation.value}` : 'Dex')}</span>
                    </div>
                    <div class="pokemon-image-wrap">
                        <img src="${imageUrl}" data-fallback="${fallbackUrl}" alt="${escapeHtml(displayName)}" loading="lazy">
                    </div>
                    <strong class="pokemon-card-name">${escapeHtml(displayName)}</strong>
                    <div class="type-badge-row">${pokemon.types.map(badge).join('')}</div>
                    <div class="evolution-count" data-evolution-count="${pokemon.id}">${pokemon.isCustom ? 'Pokemon personalizado' : 'Estagios: carregando...'}</div>
                </a>
            `;
        }

        function renderPagination(totalPages) {
            prevPage.disabled = currentPage <= 1;
            nextPage.disabled = currentPage >= totalPages;
            pageSummary.textContent = `Pagina ${currentPage} de ${totalPages}`;

            const pages = visiblePages(currentPage, totalPages);
            pageNumbers.innerHTML = pages.map((page) => {
                if (page === 'gap') {
                    return '<span class="page-gap">...</span>';
                }

                return `
                    <button
                        type="button"
                        class="page-number-button ${page === currentPage ? 'is-active' : ''}"
                        data-page="${page}"
                        aria-label="Ir para pagina ${page}"
                    >${page}</button>
                `;
            }).join('');
        }

        function visiblePages(current, total) {
            if (total <= 7) {
                return Array.from({ length: total }, (_, index) => index + 1);
            }

            const pages = [1];
            const start = Math.max(2, current - 1);
            const end = Math.min(total - 1, current + 1);

            if (start > 2) pages.push('gap');
            for (let page = start; page <= end; page += 1) pages.push(page);
            if (end < total - 1) pages.push('gap');
            pages.push(total);

            return pages;
        }

        async function hydrateEvolutionCounts(pageItems) {
            pageItems.forEach((pokemon) => {
                const target = $(`[data-evolution-count="${pokemon.id}"]`, grid);
                if (!target) return;
                if (pokemon.isCustom) {
                    target.textContent = 'Pokemon personalizado';
                    return;
                }

                if (evolutionCache[pokemon.id]) {
                    target.textContent = `Estagios: ${evolutionCache[pokemon.id]}`;
                } else {
                    target.textContent = 'Estagios: carregando...';
                }
            });

            await mapWithConcurrency(pageItems, 4, async (pokemon) => {
                if (pokemon.isCustom) return;
                if (evolutionCache[pokemon.id]) return;

                const count = await getEvolutionCount(pokemon.id);
                evolutionCache[pokemon.id] = count;
                writeCache(EVOLUTION_CACHE_KEY, evolutionCache);

                const target = $(`[data-evolution-count="${pokemon.id}"]`, grid);
                if (target) {
                    target.textContent = `Estagios: ${count}`;
                }
            });
        }

        async function getEvolutionCount(id) {
            if (evolutionCache[id]) return evolutionCache[id];
            if (pendingEvolution.has(id)) return pendingEvolution.get(id);

            const request = (async () => {
                try {
                    const species = await fetchJson(`${API_BASE}/pokemon-species/${id}`);
                    if (!species.evolution_chain?.url) return 1;

                    const chain = await fetchJson(species.evolution_chain.url);
                    const parsed = parseEvolutionChain(chain.chain);
                    parsed.nodes.forEach((node) => {
                        if (node.id >= 1 && node.id <= MAX_POKEMON) {
                            evolutionCache[node.id] = parsed.stageCount || 1;
                        }
                    });

                    return parsed.stageCount || 1;
                } catch (error) {
                    return 1;
                } finally {
                    pendingEvolution.delete(id);
                }
            })();

            pendingEvolution.set(id, request);
            return request;
        }

        searchInput.addEventListener('input', () => applyFilters(true));
        generationFilter.addEventListener('change', () => applyFilters(true));
        sortSelect.addEventListener('change', () => applyFilters(true));

        clearFiltersButton?.addEventListener('click', () => {
            searchInput.value = '';
            generationFilter.value = 'all';
            sortSelect.value = 'id-asc';
            selectedTypes = [];
            $$('[data-type]', typeFilterGrid).forEach((typeButton) => {
                typeButton.classList.remove('is-active');
            });
            currentPage = 1;
            applyFilters(true);
        });

        typeFilterGrid.addEventListener('click', (event) => {
            const button = event.target.closest('[data-type]');
            if (!button) return;

            const type = button.dataset.type;
            if (selectedTypes.includes(type)) {
                selectedTypes = selectedTypes.filter((selected) => selected !== type);
            } else {
                selectedTypes.push(type);
                selectedTypes = selectedTypes.slice(-2);
            }

            $$('[data-type]', typeFilterGrid).forEach((typeButton) => {
                typeButton.classList.toggle('is-active', selectedTypes.includes(typeButton.dataset.type));
            });

            applyFilters(true);
        });

        prevPage.addEventListener('click', () => {
            if (currentPage <= 1) return;
            currentPage -= 1;
            renderPage();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        nextPage.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(filteredPokemon.length / PER_PAGE));
            if (currentPage >= totalPages) return;
            currentPage += 1;
            renderPage();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        pageNumbers.addEventListener('click', (event) => {
            const button = event.target.closest('[data-page]');
            if (!button) return;

            currentPage = Number(button.dataset.page);
            renderPage();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        retryListButton.addEventListener('click', () => loadData(true));
        loadData(false);
    }

    function initDetailsPage() {
        const loader = $('#detailLoader');
        const shell = $('#detailShell');
        const errorBox = $('#detailError');
        const content = $('#detailContent');
        const detailTitle = $('#detailTitle');
        const detailNumber = $('#detailNumber');
        const image = $('#pokemonImage');
        const imagePlaceholder = $('#imagePlaceholder');
        const artScreen = $('#artScreen');
        const pokemonDisplayName = $('#pokemonDisplayName');
        const activeFormName = $('#activeFormName');
        const pokemonTypes = $('#pokemonTypes');
        const imageOptionButtons = $('#imageOptionButtons');
        const pokemonDescription = $('#pokemonDescription');
        const evolutionStatus = $('#evolutionStatus');
        const evolutionTree = $('#evolutionTree');
        const formsStatus = $('#formsStatus');
        const formsGrid = $('#formsGrid');
        const statsList = $('#statsList');
        const movesList = $('#movesList');
        const moveDetailPanel = $('#moveDetailPanel');
        const randomPokemonButton = $('#randomPokemonButton');

        if (!loader || !shell) return;

        let currentBundle = null;
        let activeImageKey = 'official';
        const moveCache = new Map();

        function showLoader() {
            loader.classList.add('is-active');
        }

        function hideLoader() {
            loader.classList.remove('is-active');
        }

        function showDetailError() {
            hideLoader();
            shell.hidden = false;
            errorBox.hidden = false;
            content.hidden = true;
            detailTitle.textContent = 'Erro';
            detailNumber.textContent = '#000';
        }

        async function loadDetails(pokemonKey, options = {}) {
            activeImageKey = 'official';
            showLoader();
            errorBox.hidden = true;
            content.hidden = false;

            try {
                const normalizedKey = String(pokemonKey || '').trim().toLowerCase();
                const bundle = config.customPokemon && String(config.customPokemon.id) === normalizedKey
                    ? buildCustomDetailBundle(config.customPokemon)
                    : await buildDetailBundle(normalizedKey);
                currentBundle = bundle;
                renderDetails(bundle);

                if (options.push) {
                    const routeKey = bundle.routeKey || bundle.pokemon.name || bundle.pokemon.id;
                    history.pushState({ pokemonKey: routeKey }, '', `${ROUTE_BASE}/${routeKey}`);
                }

                hideLoader();
            } catch (error) {
                showDetailError();
            }
        }

        async function buildDetailBundle(pokemonKey) {
            const pokemon = await fetchJson(`${API_BASE}/pokemon/${pokemonKey}`);
            const species = pokemon.species?.url
                ? await fetchJson(pokemon.species.url)
                : null;

            let evolution = {
                available: false,
                message: 'Linha evolutiva nao disponivel.',
                nodes: [],
                stageCount: 1,
            };

            if (species?.evolution_chain?.url) {
                try {
                    const chain = await fetchJson(species.evolution_chain.url);
                    const parsed = parseEvolutionChain(chain.chain);
                    evolution = {
                        available: parsed.nodes.length > 0,
                        message: parsed.nodes.length > 0 ? '' : 'Linha evolutiva nao disponivel.',
                        nodes: parsed.nodes,
                        stageCount: parsed.stageCount,
                    };
                } catch (error) {
                    evolution.message = 'Linha evolutiva nao disponivel para esta forma.';
                }
            }

            let forms = [];
            let formsMessage = '';

            if (species?.varieties?.length) {
                const varieties = species.varieties
                    .map((entry) => ({
                        isDefault: Boolean(entry.is_default),
                        name: entry.pokemon?.name,
                        url: entry.pokemon?.url,
                        id: extractIdFromUrl(entry.pokemon?.url),
                    }))
                    .filter((entry) => entry.id && entry.name && entry.url);

                const formDetails = await mapWithConcurrency(varieties, 6, async (variety) => {
                    try {
                        const formPokemon = variety.id === pokemon.id ? pokemon : await fetchJson(variety.url);
                        return {
                            id: formPokemon.id,
                            name: formPokemon.name,
                            displayName: variety.isDefault ? 'Forma principal' : formatName(formPokemon.name),
                            image: imageFromPokemon(formPokemon),
                            fallback: formPokemon.sprites?.front_default || spriteUrl(formPokemon.id),
                            types: typeListFromPokemon(formPokemon),
                            isDefault: variety.isDefault,
                            isActive: formPokemon.id === pokemon.id,
                            virtual: null,
                        };
                    } catch (error) {
                        return null;
                    }
                });

                forms = formDetails.filter(Boolean);
            }

            const shinyImage = imageFromPokemon(pokemon, true);
            if (shinyImage && shinyImage !== imageFromPokemon(pokemon, false)) {
                forms.push({
                    id: pokemon.id,
                    name: `${pokemon.name}-shiny`,
                    displayName: 'Shiny',
                    image: shinyImage,
                    fallback: pokemon.sprites?.front_shiny || imageFromPokemon(pokemon, false),
                    types: typeListFromPokemon(pokemon),
                    isDefault: false,
                    isActive: false,
                    virtual: 'shiny',
                });
            }

            const uniqueForms = [];
            const seenForms = new Set();
            forms.forEach((form) => {
                const key = `${form.virtual || 'form'}:${form.id}:${form.name}`;
                if (seenForms.has(key)) return;
                seenForms.add(key);
                uniqueForms.push(form);
            });

            const hasAlternative = uniqueForms.some((form) => form.virtual || form.id !== pokemon.id);
            if (!hasAlternative) {
                uniqueForms.length = 0;
            }

            if (!uniqueForms.length) {
                formsMessage = 'Nenhuma forma alternativa disponivel.';
            }

            return {
                pokemon,
                species,
                types: typeListFromPokemon(pokemon),
                description: bestDescription(species),
                evolution,
                forms: uniqueForms,
                formsMessage,
                isCustom: false,
                routeKey: pokemon.name,
            };
        }

        function buildCustomDetailBundle(customPokemon) {
            const types = Array.isArray(customPokemon.types) && customPokemon.types.length
                ? customPokemon.types.slice(0, 2)
                : ['normal'];
            const abilities = Array.isArray(customPokemon.abilities) && customPokemon.abilities.length
                ? customPokemon.abilities
                : ['Habilidade Especial'];

            return {
                pokemon: {
                    id: Number(customPokemon.id || customPokemon.pokemon_id),
                    name: customPokemon.name || 'pokemon-criado',
                    sprites: {},
                    stats: customStats(),
                    moves: [],
                    customImage: customPokemon.image || null,
                    customAbilities: abilities,
                    height: customPokemon.height || null,
                    weight: customPokemon.weight || null,
                },
                species: {
                    name: customPokemon.name || 'pokemon-criado',
                },
                types,
                description: customPokemon.description || 'Descricao nao disponivel.',
                evolution: {
                    available: false,
                    message: 'Linha evolutiva nao disponivel para Pokemon personalizado.',
                    nodes: [],
                    stageCount: 1,
                },
                forms: [],
                formsMessage: 'Nenhuma forma alternativa disponivel.',
                isCustom: true,
                routeKey: String(customPokemon.id || customPokemon.pokemon_id),
            };
        }

        function customStats() {
            return [
                ['hp', 80],
                ['attack', 80],
                ['defense', 80],
                ['special-attack', 80],
                ['special-defense', 80],
                ['speed', 80],
            ].map(([name, value]) => ({ base_stat: value, stat: { name } }));
        }

        function renderDetails(bundle) {
            shell.hidden = false;
            errorBox.hidden = true;
            content.hidden = false;
            resetActiveTab();

            const pokemon = bundle.pokemon;
            const currentTypes = bundle.types.length ? bundle.types : ['normal'];
            const primary = currentTypes[0] || 'normal';
            const secondaryColor = currentTypes[1]
                ? typeColor(currentTypes[1])
                : mixColor(typeColor(primary), '#08121e', 0.56);
            const imageOptions = imageVariants(pokemon, bundle);
            if (!imageOptions.some((option) => option.key === activeImageKey)) {
                activeImageKey = imageOptions[0]?.key || 'official';
            }
            const currentOption = imageOptions.find((option) => option.key === activeImageKey);
            const displayName = currentOption?.isShiny ? `Shiny ${formatName(pokemon.name)}` : formatName(pokemon.name);
            const currentImage = bundle.isCustom
                ? (currentOption?.url || null)
                : (currentOption?.url || imageFromPokemon(pokemon, false));
            const currentForm = bundle.forms.find((form) => !form.virtual && form.id === pokemon.id);

            document.title = `Pokedex - ${displayName}`;
            detailTitle.textContent = displayName;
            detailNumber.textContent = formatId(pokemon.id);
            pokemonDisplayName.textContent = displayName;
            activeFormName.textContent = currentOption?.label || (currentForm?.displayName || 'Forma ativa');
            pokemonDescription.textContent = bundle.description || 'Descricao nao disponivel.';
            pokemonTypes.innerHTML = currentTypes.map(badge).join('');

            artScreen.style.setProperty('--primary-type-color', typeColor(primary));
            artScreen.style.setProperty('--secondary-type-color', secondaryColor);

            if (currentImage) {
                image.hidden = false;
                imagePlaceholder.hidden = true;
                image.src = currentImage;
                image.alt = displayName;
                image.dataset.fallback = currentOption?.fallback || pokemon.sprites?.front_default || PLACEHOLDER_IMAGE;
                attachImageFallbacks(artScreen);
            } else {
                image.hidden = true;
                imagePlaceholder.hidden = false;
            }

            renderImageOptions(imageOptions);
            renderEvolution(bundle);
            renderForms(bundle);
            renderStats(pokemon);
            renderMoves(pokemon);
        }

        function imageVariants(pokemon, bundle) {
            if (bundle.isCustom) {
                return pokemon.customImage ? [{
                    key: 'custom',
                    label: 'Imagem enviada',
                    url: pokemon.customImage,
                    fallback: PLACEHOLDER_IMAGE,
                    isShiny: false,
                }] : [];
            }

            const sprites = pokemon.sprites || {};
            const candidates = [
                ['official', 'Arte oficial', sprites.other?.['official-artwork']?.front_default],
                ['front', 'Sprite normal', sprites.front_default],
                ['shiny', 'Sprite shiny', sprites.front_shiny],
                ['home', 'Home', sprites.other?.home?.front_default],
                ['home-shiny', 'Home shiny', sprites.other?.home?.front_shiny],
                ['animated', 'Animado', sprites.versions?.['generation-v']?.['black-white']?.animated?.front_default],
                ['animated-shiny', 'Animado shiny', sprites.versions?.['generation-v']?.['black-white']?.animated?.front_shiny],
                ['dream-world', 'Dream World', sprites.other?.dream_world?.front_default],
            ];

            return candidates
                .filter(([, , url]) => Boolean(url))
                .map(([key, label, url]) => ({
                    key,
                    label,
                    url,
                    fallback: sprites.front_default || PLACEHOLDER_IMAGE,
                    isShiny: key.includes('shiny'),
                }));
        }

        function renderImageOptions(options) {
            if (!imageOptionButtons) return;

            if (!options.length || options.length === 1) {
                imageOptionButtons.innerHTML = '';
                return;
            }

            imageOptionButtons.innerHTML = options.map((option) => `
                <button
                    type="button"
                    class="image-option-button ${option.key === activeImageKey ? 'is-active' : ''}"
                    data-image-key="${option.key}"
                >${escapeHtml(option.label)}</button>
            `).join('');
        }

        function renderEvolution(bundle) {
            const evolution = bundle.evolution;
            const currentSpeciesName = bundle.species?.name || bundle.pokemon.species?.name || bundle.pokemon.name;

            evolutionStatus.textContent = evolution.available ? `${evolution.stageCount} estagio(s)` : '';

            if (!evolution.available) {
                evolutionTree.innerHTML = `<p>${escapeHtml(evolution.message || 'Linha evolutiva nao disponivel.')}</p>`;
                return;
            }

            evolutionTree.innerHTML = evolution.nodes.map((node) => {
                const isCurrent = node.name === currentSpeciesName;

                return `
                    <button
                        type="button"
                        class="evolution-card ${isCurrent ? 'is-current' : ''}"
                        data-evolution-id="${node.id}"
                        aria-label="Abrir ${escapeHtml(formatName(node.name))}"
                    >
                        <img src="${officialArtworkUrl(node.id)}" data-fallback="${spriteUrl(node.id)}" alt="${escapeHtml(formatName(node.name))}" loading="lazy">
                        <strong>${escapeHtml(formatName(node.name))}</strong>
                        <small>Estagio ${node.stage}</small>
                    </button>
                `;
            }).join('');
            attachImageFallbacks(evolutionTree);
        }

        function renderForms(bundle) {
            formsStatus.textContent = bundle.forms.length ? `${bundle.forms.length} forma(s)` : '';

            if (!bundle.forms.length) {
                formsGrid.innerHTML = `<p>${escapeHtml(bundle.formsMessage || 'Nenhuma forma alternativa disponivel.')}</p>`;
                return;
            }

            formsGrid.innerHTML = bundle.forms.map((form) => {
                const isActive = form.virtual === 'shiny' ? activeImageKey.includes('shiny') : (!activeImageKey.includes('shiny') && form.id === bundle.pokemon.id);
                const idLabel = form.virtual === 'shiny' ? 'Shiny' : formatId(form.id);

                return `
                    <button
                        type="button"
                        class="form-card ${isActive ? 'is-active' : ''}"
                        data-form-id="${form.id}"
                        data-form-key="${escapeHtml(form.name)}"
                        data-virtual="${form.virtual || ''}"
                        aria-label="Ver forma ${escapeHtml(formatName(form.name))}"
                    >
                        <img src="${form.image}" data-fallback="${form.fallback || PLACEHOLDER_IMAGE}" alt="${escapeHtml(formatName(form.name))}" loading="lazy">
                        <strong>${escapeHtml(form.displayName || formatName(form.name))}</strong>
                        <small>${idLabel}</small>
                        <div class="type-badge-row">${(form.types || ['normal']).map(badge).join('')}</div>
                    </button>
                `;
            }).join('');
            attachImageFallbacks(formsGrid);
        }

        function renderStats(pokemon) {
            const stats = pokemon.stats || [];

            if (!stats.length) {
                statsList.innerHTML = '<p>Status nao disponiveis.</p>';
                return;
            }

            statsList.innerHTML = stats.map((entry) => {
                const value = Number(entry.base_stat || 0);
                const width = Math.min(100, Math.max(4, (value / 255) * 100));

                return `
                    <div class="stat-row">
                        <strong>${escapeHtml(formatStatName(entry.stat?.name))}</strong>
                        <span>${value}</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width:${width}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderMoves(pokemon) {
            const moves = pokemon.moves || [];

            if (!moves.length) {
                movesList.innerHTML = '<p>Ataques nao disponiveis.</p>';
                moveDetailPanel.hidden = true;
                return;
            }

            movesList.innerHTML = `
                <div class="moves-list">
                    ${moves.map((move) => `
                        <button type="button" class="move-row" data-move-name="${escapeHtml(move.move?.name || '')}">
                            <strong>${escapeHtml(formatName(move.move?.name))}</strong>
                            <span>${escapeHtml(chooseMoveDetail(move))}</span>
                        </button>
                    `).join('')}
                </div>
            `;
            moveDetailPanel.hidden = true;
        }

        async function showMoveDetails(moveName) {
            if (!moveName) return;

            $$('.move-row', movesList).forEach((row) => {
                row.classList.toggle('is-active', row.dataset.moveName === moveName);
            });

            moveDetailPanel.hidden = false;
            moveDetailPanel.innerHTML = '<p>Carregando ataque...</p>';

            try {
                const details = moveCache.has(moveName)
                    ? moveCache.get(moveName)
                    : await fetchJson(`${API_BASE}/move/${moveName}`);
                moveCache.set(moveName, details);
                renderMoveDetails(details);
            } catch (error) {
                moveDetailPanel.innerHTML = '<p>Ataque nao disponivel.</p>';
            }
        }

        function renderMoveDetails(move) {
            const description = bestMoveDescription(move);
            const power = move.power ?? null;
            const accuracy = move.accuracy ?? null;
            const pp = move.pp ?? null;

            moveDetailPanel.innerHTML = `
                <h3>${escapeHtml(formatName(move.name))}</h3>
                <div class="move-detail-grid">
                    <span><strong>Poder</strong>${power ?? '-'}</span>
                    <span><strong>Precisao</strong>${accuracy ?? '-'}</span>
                    <span><strong>PP</strong>${pp ?? '-'}</span>
                </div>
                <p>${escapeHtml(description)}</p>
            `;
        }

        function bestMoveDescription(move) {
            const entries = move.flavor_text_entries || [];
            for (const language of ['pt-br', 'pt', 'en']) {
                const found = entries.find((entry) => String(entry.language?.name || '').toLowerCase() === language);
                if (found?.flavor_text) {
                    return cleanDescription(found.flavor_text);
                }
            }

            return 'Descricao nao disponivel.';
        }

        $$('.screen-tab').forEach((button) => {
            button.addEventListener('click', () => setActiveTab(button.dataset.tab));
        });

        imageOptionButtons?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-image-key]');
            if (!button || !currentBundle) return;

            activeImageKey = button.dataset.imageKey;
            renderDetails(currentBundle);
        });

        movesList.addEventListener('click', (event) => {
            const row = event.target.closest('[data-move-name]');
            if (!row) return;
            showMoveDetails(row.dataset.moveName);
        });

        evolutionTree.addEventListener('click', (event) => {
            const card = event.target.closest('[data-evolution-id]');
            if (!card) return;
            loadDetails(Number(card.dataset.evolutionId), { push: true });
        });

        formsGrid.addEventListener('click', (event) => {
            const card = event.target.closest('[data-form-id]');
            if (!card || !currentBundle) return;

            if (card.dataset.virtual === 'shiny') {
                activeImageKey = 'shiny';
                renderDetails(currentBundle);
                return;
            }

            const formKey = card.dataset.formKey || card.dataset.formId;
            if (formKey === currentBundle.pokemon.name || Number(card.dataset.formId) === currentBundle.pokemon.id) {
                activeImageKey = 'official';
                renderDetails(currentBundle);
                return;
            }

            loadDetails(formKey, { push: true });
        });

        randomPokemonButton.addEventListener('click', () => {
            const randomId = Math.floor(Math.random() * MAX_POKEMON) + 1;
            window.location.href = `${ROUTE_BASE}/${randomId}`;
        });

        window.addEventListener('popstate', () => {
            const key = window.location.pathname.split('/').filter(Boolean).pop();
            if (key) loadDetails(key);
        });

        loadDetails(config.initialPokemonId || document.body.dataset.pokemonId || '1');
    }

    function initCustomFormPage() {
        const nameInput = $('#customNameInput');
        const heightInput = $('#customHeightInput');
        const weightInput = $('#customWeightInput');
        const abilitiesInput = $('#customAbilitiesInput');
        const descriptionInput = $('#customDescriptionInput');
        const imageInput = $('#customImageInput');
        const typeGrid = $('#customTypeGrid');
        const typeLimit = $('#customTypeLimit');
        const previewCard = $('#customPreviewCard');
        const previewImage = $('#customPreviewImage');
        const previewPlaceholder = $('#customPreviewPlaceholder');
        const previewName = $('#customPreviewName');
        const previewTypes = $('#customPreviewTypes');
        const previewHeight = $('#customPreviewHeight');
        const previewWeight = $('#customPreviewWeight');
        const previewAbilities = $('#customPreviewAbilities');
        const previewDescription = $('#customPreviewDescription');

        if (!typeGrid || !previewCard) return;

        function selectedTypes() {
            return $$('input[name="types[]"]:checked', typeGrid).map((input) => input.value).slice(0, 2);
        }

        function refreshTypeButtons() {
            $$('.custom-type-option', typeGrid).forEach((label) => {
                const input = $('input', label);
                label.classList.toggle('is-active', Boolean(input?.checked));
            });
        }

        function renderPreview() {
            const types = selectedTypes();
            const primary = types[0] || 'normal';
            const secondaryColor = types[1] ? typeColor(types[1]) : mixColor(typeColor(primary), '#08121e', 0.56);

            previewCard.style.setProperty('--primary-type-color', typeColor(primary));
            previewCard.style.setProperty('--secondary-type-color', secondaryColor);
            previewName.textContent = nameInput.value.trim() || 'Nome do Pokemon';
            previewTypes.innerHTML = (types.length ? types : ['normal']).map(badge).join('');
            previewHeight.textContent = `Altura: ${heightInput.value || '1.70'} m`;
            previewWeight.textContent = `Peso: ${weightInput.value || '65.5'} kg`;
            previewAbilities.textContent = abilitiesInput.value.trim() || 'Habilidades';
            previewDescription.textContent = descriptionInput.value.trim() || 'Descricao do Pokemon';
            refreshTypeButtons();
        }

        typeGrid.addEventListener('change', (event) => {
            const input = event.target.closest('input[name="types[]"]');
            if (!input) return;

            const checked = $$('input[name="types[]"]:checked', typeGrid);
            if (checked.length > 2) {
                input.checked = false;
                typeLimit.hidden = false;
            } else {
                typeLimit.hidden = true;
            }

            renderPreview();
        });

        [nameInput, heightInput, weightInput, abilitiesInput, descriptionInput].forEach((input) => {
            input?.addEventListener('input', renderPreview);
        });

        imageInput?.addEventListener('change', () => {
            const file = imageInput.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                previewImage.src = event.target.result;
                previewImage.classList.remove('is-empty');
                previewPlaceholder.hidden = true;
            };
            reader.readAsDataURL(file);
        });

        renderPreview();
    }

    if (config.page === 'list') {
        initListPage();
    }

    if (config.page === 'details') {
        initDetailsPage();
    }

    if (config.page === 'customForm') {
        initCustomFormPage();
    }
})();
