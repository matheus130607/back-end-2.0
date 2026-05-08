<link rel="stylesheet" href="{{ asset('css/pokemon-ui.css') }}">

<style>
    .main-menu {
        position: fixed;
        top: 18px;
        left: 18px;
        z-index: 1200;
    }

    .main-menu__button {
        width: min(250px, 54vw);
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 0;
        display: block;
        filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.42));
        transition: transform 180ms ease;
    }

    .main-menu__button:hover,
    .main-menu__button:focus-visible {
        transform: translateY(-2px) scale(1.02);
    }

    .main-menu__button img {
        width: 100%;
        height: auto;
        display: block;
    }

    .main-menu__content {
        position: absolute;
        top: calc(100% + 12px);
        left: 0;
        width: 270px;
        overflow: hidden;
        border: 2px solid #2b0b14;
        border-radius: 12px;
        background: linear-gradient(180deg, #df2442, #a91530);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.42);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: opacity 160ms ease, transform 160ms ease, visibility 160ms ease;
    }

    .main-menu.is-open .main-menu__content {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .main-menu__title {
        padding: 11px 16px;
        color: #ffe780;
        background: rgba(0, 0, 0, 0.2);
        font: 800 0.74rem "Courier New", monospace;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .main-menu__content a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 44px;
        padding: 0 16px;
        color: #fff;
        text-decoration: none;
        font: 800 0.9rem "Courier New", monospace;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }

    .main-menu__content a:hover,
    .main-menu__content a:focus-visible {
        background: rgba(255, 255, 255, 0.14);
    }

    .main-menu__mark {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ffe780;
        box-shadow: inset 0 0 0 4px rgba(0, 0, 0, 0.12);
        flex: 0 0 auto;
    }

    @media (max-width: 760px) {
        .main-menu {
            top: 12px;
            left: 12px;
        }

        .main-menu__button {
            width: 188px;
        }

        .main-menu__content {
            width: 236px;
        }
    }
</style>

<div class="pokedex-loader" id="pokedexLoader" aria-live="polite" aria-label="Carregamento">
    <div class="pokedex-loader__shell">
        <div class="pokedex-loader__lights">
            <div class="pokedex-loader__lens"></div>
            <div class="pokedex-loader__dot"></div>
            <div class="pokedex-loader__dot"></div>
            <div class="pokedex-loader__dot"></div>
        </div>
        <div class="pokedex-loader__screen">
            <div class="pokedex-loader__bar"></div>
            <span data-loader-label>Carregando</span>
        </div>
    </div>
</div>

<nav class="main-menu" id="mainMenu">
    <button class="main-menu__button" type="button" aria-label="Abrir menu" aria-expanded="false">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/International_Pok%C3%A9mon_logo.svg" alt="Pokemon">
    </button>
    <div class="main-menu__content">
        <div class="main-menu__title">Navegacao</div>
        <a href="{{ route('pokedex.random') }}"><span>Pokedex Random</span><span class="main-menu__mark"></span></a>
        <a href="{{ route('pokedex.lista') }}"><span>Lista de Pokemon</span><span class="main-menu__mark"></span></a>
        <a href="{{ route('custom-pokemon.create') }}"><span>Criar Pokemon</span><span class="main-menu__mark"></span></a>
        <a href="{{ route('custom-pokemon.index') }}"><span>Meus Pokemon</span><span class="main-menu__mark"></span></a>
        <a href="{{ route('cartas.index') }}"><span>Cartas Pokemon</span><span class="main-menu__mark"></span></a>
        <a href="{{ route('battle-cards.index') }}"><span>Batalha TCG</span><span class="main-menu__mark"></span></a>
        <a href="{{ route('historia') }}"><span>Hist&oacute;ria</span><span class="main-menu__mark"></span></a>
    </div>
</nav>

<script src="{{ asset('js/pokemon-ui.js') }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menu = document.getElementById('mainMenu');
        if (!menu) return;

        const button = menu.querySelector('.main-menu__button');

        button.addEventListener('click', function () {
            const isOpen = menu.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) {
                menu.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                menu.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>
