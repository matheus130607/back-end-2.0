<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<title>Pokédex - A História do Pokémon</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Courier New', 'VT323', monospace;
        background-image: url('https://images.unsplash.com/photo-1448375240586-882707db888b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        position: relative;
        min-height: 100vh;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 0;
    }

    /* Logo */
    .logo-container {
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1000;
    }

    .pokemon-logo {
        width: 280px;
        height: auto;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
        transition: transform 0.3s ease;
    }

    .pokemon-logo:hover {
        transform: scale(1.05);
    }

    /* Header Hero */
    .hero {
        background: linear-gradient(135deg, #e33535 0%, #cc0000 100%);
        padding: 80px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid #ffd700;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/></svg>') repeat;
        opacity: 0.3;
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero h1 {
        font-size: 3.5rem;
        text-shadow: 3px 3px 6px rgba(0,0,0,0.5);
        margin-bottom: 20px;
        color: white;
        font-family: 'Courier New', monospace;
    }

    .hero p {
        font-size: 1.3rem;
        opacity: 0.95;
        color: #ffeb3b;
    }

    /* Navegação estilo Pokédex */
    .nav-timeline {
        background: rgba(227, 53, 53, 0.95);
        backdrop-filter: blur(10px);
        padding: 15px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        border-bottom: 2px solid #aa2222;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }

    .nav-timeline a {
        color: white;
        text-decoration: none;
        padding: 8px 18px;
        border-radius: 25px;
        transition: all 0.3s ease;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        background: rgba(0,0,0,0.2);
    }

    .nav-timeline a:hover {
        background: #ffd700;
        color: #333;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Container Principal */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        position: relative;
        z-index: 1;
    }

    /* Seções */
    .section {
        margin: 80px 0;
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.8s ease;
        background: rgba(227, 53, 53, 0.9);
        border-radius: 30px;
        padding: 40px;
        border: 2px solid #aa2222;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .section.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .section-title {
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 40px;
        color: #ffeb3b;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        font-family: 'Courier New', monospace;
    }

    /* Cards estilo Pokédex */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin: 40px 0;
    }

    .card {
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 25px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.2);
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        background: rgba(0,0,0,0.4);
        border-color: #ffd700;
    }

    .card img {
        width: 100%;
        border-radius: 15px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .card:hover img {
        transform: scale(1.05);
    }

    .card h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #ffd700;
    }

    .card p {
        line-height: 1.6;
        opacity: 0.9;
        color: white;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #ffd700, #e33535);
        transform: translateX(-50%);
    }

    .timeline-item {
        display: flex;
        justify-content: space-between;
        margin: 50px 0;
        position: relative;
    }

    .timeline-item:nth-child(even) {
        flex-direction: row-reverse;
    }

    .timeline-content {
        width: 45%;
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }

    .timeline-content:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        border-color: #ffd700;
    }

    .timeline-content h3 {
        color: #ffd700;
        margin-bottom: 10px;
    }

    .timeline-year {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        background: #e33535;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 1rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        border: 1px solid #ffd700;
        color: white;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 40px 0;
    }

    .stat-card {
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(0,0,0,0.5);
        border-color: #ffd700;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        color: #ffd700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    /* Galeria */
    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin: 40px 0;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        cursor: pointer;
        aspect-ratio: 1;
        border: 2px solid #aa2222;
        transition: all 0.3s ease;
    }

    .gallery-item:hover {
        transform: scale(1.05);
        border-color: #ffd700;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.2);
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.9));
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    .gallery-overlay h3 {
        color: #ffd700;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.95);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: #e33535;
        border-radius: 30px;
        padding: 40px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        border: 3px solid #ffd700;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #ff4444;
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .close-btn:hover {
        transform: rotate(90deg);
        background: #cc0000;
    }

    /* Botões */
    .back-btn, .pokedex-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #e33535, #aa2222);
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        transition: all 0.3s ease;
        z-index: 100;
        font-family: 'Courier New', monospace;
    }

    .pokedex-btn {
        right: 30px;
        bottom: 100px;
        background: linear-gradient(135deg, #2196F3, #1565C0);
    }

    .back-btn:hover, .pokedex-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.7);
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .pokemon-logo {
            width: 200px;
        }
        
        .hero h1 {
            font-size: 2rem;
        }
        
        .hero p {
            font-size: 1rem;
        }
        
        .section {
            padding: 25px;
        }
        
        .section-title {
            font-size: 1.8rem;
        }
        
        .timeline::before {
            left: 20px;
        }
        
        .timeline-item,
        .timeline-item:nth-child(even) {
            flex-direction: column;
            margin-left: 40px;
        }
        
        .timeline-content {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .timeline-year {
            left: 20px;
            transform: translateX(-50%);
            font-size: 0.8rem;
        }
        
        .nav-timeline {
            gap: 8px;
        }
        
        .nav-timeline a {
            padding: 5px 12px;
            font-size: 0.7rem;
        }
        
        .back-btn, .pokedex-btn {
            padding: 8px 15px;
            font-size: 0.8rem;
        }
        
        .pokedex-btn {
            bottom: 90px;
        }
    }

    /* Scrollbar personalizada */
    ::-webkit-scrollbar {
        width: 10px;
    }

    ::-webkit-scrollbar-track {
        background: #1a1a2e;
    }

    ::-webkit-scrollbar-thumb {
        background: #e33535;
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #ffd700;
    }
</style>
</head>
<body>

<!-- Logo -->
@include('partials.menu-dropdown')

<!-- Header Hero -->
<div class="hero">
    <div class="hero-content">
        <h1>🎮 A HISTÓRIA DO POKÉMON</h1>
        <p>Descubra a jornada épica da franquia que conquistou o mundo</p>
    </div>
</div>

<!-- Navegação -->
<nav class="nav-timeline">
    <a href="#origem">🎯 ORIGEM</a>
    <a href="#criador">👤 CRIADOR</a>
    <a href="#timeline">📅 LINHA DO TEMPO</a>
    <a href="#jogos">🎮 JOGOS</a>
    <a href="#anime">📺 ANIME</a>
    <a href="#curiosidades">✨ CURIOSIDADES</a>
    <a href="#impacto">🌍 IMPACTO</a>
</nav>

<!-- Container Principal -->
<div class="container">

    <!-- Seção: A Origem -->
    <div class="section" id="origem">
        <h2 class="section-title">🎯 A ORIGEM DE TUDO</h2>
        
        <div class="card-grid">
            <div class="card" onclick="openModal('modal1')">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/International_Pok%C3%A9mon_logo.svg/1200px-International_Pok%C3%A9mon_logo.svg.png" alt="Pokémon Logo">
                <h3>📖 O INÍCIO DA AVENTURA</h3>
                <p>Pokémon começou como uma ideia simples que se tornou um fenômeno global. A palavra "Pokémon" é uma contração de "Pocket Monsters" (Monstros de Bolso).</p>
            </div>

            <div class="card" onclick="openModal('modal2')">
                <img src="https://images.unsplash.com/photo-1542779283-429940ce8336?w=500" alt="Japão">
                <h3>🇯🇵 RAÍZES JAPONESAS</h3>
                <p>Nascido no Japão, Pokémon foi inspirado na paixão de Satoshi Tajiri por colecionar insetos durante sua infância.</p>
            </div>

            <div class="card" onclick="openModal('modal3')">
                <img src="https://images.unsplash.com/photo-1612404730960-5c71577fca11?w=500" alt="Game Boy">
                <h3>🎮 A REVOLUÇÃO GAME BOY</h3>
                <p>Os primeiros jogos Pokémon Red e Green foram lançados para Game Boy em 1996, revolucionando os RPGs portáteis.</p>
            </div>
        </div>
    </div>

    <!-- Seção: O Criador -->
    <div class="section" id="criador">
        <h2 class="section-title">👤 SATOSHI TAJIRI - O VISIONÁRIO</h2>
        
        <div class="card-grid">
            <div class="card">
                <h3>🦋 O COLECIONADOR DE INSETOS</h3>
                <p>Satoshi Tajiri nasceu em 28 de agosto de 1965 em Machida, Tóquio. Quando criança, era fascinado por colecionar insetos, uma paixão que mais tarde inspiraria a criação de Pokémon.</p>
            </div>

            <div class="card">
                <h3>🎮 GAME FREAK</h3>
                <p>Em 1982, Tajiri e seus amigos fundaram a revista Game Freak, dedicada a jogos. Em 1989, transformaram a revista em uma empresa de desenvolvimento de jogos.</p>
            </div>

            <div class="card">
                <h3>💡 A GRANDE IDEIA</h3>
                <p>Inspirado pelo Game Link Cable do Game Boy, Tajiri imaginou um jogo onde jogadores poderiam trocar criaturas entre si, dando origem ao conceito de Pokémon.</p>
            </div>
        </div>
    </div>

    <!-- Seção: Linha do Tempo -->
    <div class="section" id="timeline">
        <h2 class="section-title">📅 LINHA DO TEMPO POKÉMON</h2>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">1996</div>
                <div class="timeline-content">
                    <h3>🌟 Pokémon Red & Green</h3>
                    <p>Lançamento no Japão dos primeiros jogos Pokémon, com 151 criaturas para colecionar. O sucesso foi imediato e revolucionário.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">1998</div>
                <div class="timeline-content">
                    <h3>🌎 Pokémon Chega ao Ocidente</h3>
                    <p>Pokémon Red & Blue são lançados nos Estados Unidos, iniciando a febre mundial. O fenômeno cultural começa a se espalhar.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">1999</div>
                <div class="timeline-content">
                    <h3>🎬 Anime e Filme</h3>
                    <p>O anime Pokémon estreia mundialmente e "Pokémon: O Filme" quebra recordes de bilheteria. Pikachu se torna um ícone global.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">2016</div>
                <div class="timeline-content">
                    <h3>📱 Pokémon GO</h3>
                    <p>O jogo de realidade aumentada se torna um fenômeno global com mais de 500 milhões de downloads, unindo pessoas nas ruas.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">2025</div>
                <div class="timeline-content">
                    <h3>🚀 O Futuro</h3>
                    <p>Com mais de 1000 Pokémon e novas gerações lançadas regularmente, a franquia continua inovando e encantando fãs no mundo todo.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção: Estatísticas -->
    <div class="section" id="jogos">
        <h2 class="section-title">📊 POKÉMON EM NÚMEROS</h2>
        
        <div class="stats-grid">
            <div class="stat-card" onclick="animateNumber(this, 1025)">
                <div class="stat-number" data-target="1025">0+</div>
                <p>✨ POKÉMON DESCOBERTOS</p>
            </div>
            <div class="stat-card" onclick="animateNumber(this, 480)">
                <div class="stat-number" data-target="480">0M+</div>
                <p>🎮 JOGOS VENDIDOS</p>
            </div>
            <div class="stat-card" onclick="animateNumber(this, 29)">
                <div class="stat-number" data-target="29">0+</div>
                <p>⭐ ANOS DE HISTÓRIA</p>
            </div>
            <div class="stat-card" onclick="animateNumber(this, 1300)">
                <div class="stat-number" data-target="1300">0+</div>
                <p>📺 EPISÓDIOS DO ANIME</p>
            </div>
        </div>
    </div>

    <!-- Seção: Galeria -->
    <div class="section" id="anime">
        <h2 class="section-title">🖼️ GALERIA POKÉMON</h2>
        
        <div class="gallery">
            <div class="gallery-item" onclick="openModal('gallery1')">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Pikachu">
                <div class="gallery-overlay">
                    <h3>⚡ PIKACHU</h3>
                    <p>O mascote oficial da franquia</p>
                </div>
            </div>
            <div class="gallery-item" onclick="openModal('gallery2')">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png" alt="Charizard">
                <div class="gallery-overlay">
                    <h3>🔥 CHARIZARD</h3>
                    <p>O Pokémon dragão mais popular</p>
                </div>
            </div>
            <div class="gallery-item" onclick="openModal('gallery3')">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/150.png" alt="Mewtwo">
                <div class="gallery-overlay">
                    <h3>🧠 MEWTWO</h3>
                    <p>O lendário Pokémon psíquico</p>
                </div>
            </div>
            <div class="gallery-item" onclick="openModal('gallery4')">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/1.png" alt="Bulbasaur">
                <div class="gallery-overlay">
                    <h3>🌿 BULBASAUR</h3>
                    <p>O Pokémon semente #001</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção: Curiosidades -->
    <div class="section" id="curiosidades">
        <h2 class="section-title">✨ CURIOSIDADES INCRÍVEIS</h2>
        
        <div class="card-grid">
            <div class="card">
                <h3>🎵 PIKACHU ORIGINAL</h3>
                <p>O design original de Pikachu era bem diferente! Ele parecia mais com um esquilo gordo e não tinha as bochechas vermelhas características. A evolução do design levou meses até chegar ao Pikachu que conhecemos hoje.</p>
            </div>
            <div class="card">
                <h3>🚫 POKÉMON BANIDOS</h3>
                <p>Alguns episódios do anime foram banidos em vários países, incluindo o famoso "Porygon Shock" que causou convulsões em crianças japonesas em 1997, resultando em uma pausa de 4 meses na exibição.</p>
            </div>
            <div class="card">
                <h3>👻 LAVENDER TOWN</h3>
                <p>A música de Lavender Town se tornou uma lenda urbana, com rumores de que causava pesadelos e até suicídios em crianças. A atmosfera sinistra e as frequências da música geraram polêmica mundial.</p>
            </div>
            <div class="card">
                <h3>💰 FRANQUIA BILIONÁRIA</h3>
                <p>Pokémon é a franquia de mídia mais lucrativa de todos os tempos, acumulando mais de US$ 100 bilhões, superando Star Wars, Marvel e Mickey Mouse.</p>
            </div>
        </div>
    </div>

    <!-- Seção: Impacto Cultural -->
    <div class="section" id="impacto">
        <h2 class="section-title">🌍 IMPACTO CULTURAL</h2>
        
        <div class="card-grid">
            <div class="card">
                <h3>🎨 NA ARTE</h3>
                <p>Pokémon influenciou gerações de artistas, designers e criadores de conteúdo em todo o mundo, inspirando pinturas, esculturas, músicas e até moda.</p>
            </div>
            <div class="card">
                <h3>👥 NA SOCIEDADE</h3>
                <p>Eventos como o Pokémon GO Fest reúnem milhões de pessoas em parques e cidades, criando comunidades globais e promovendo a interação social.</p>
            </div>
            <div class="card">
                <h3>🏆 NA ECONOMIA</h3>
                <p>Pokémon movimenta uma economia bilionária com jogos, filmes, séries, brinquedos, roupas e produtos licenciados em mais de 100 países.</p>
            </div>
            <div class="card">
                <h3>🎮 NOS E-SPORTS</h3>
                <p>O Campeonato Mundial de Pokémon (Pokémon World Championships) reúne milhares de competidores anualmente, com premiações milionárias e transmissão global.</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal" id="myModal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeModal()">✕</button>
        <div id="modalText"></div>
    </div>
</div>

<!-- Botões -->
<a href="{{ route('pokedex.lista') }}" class="back-btn">← VOLTAR</a>
<a href="{{ route('pokedex.lista') }}" class="pokedex-btn">📱 POKÉDEX</a>

<script>
    // Animação de scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.section').forEach(section => {
        observer.observe(section);
    });

    // Conteúdo do Modal
    const modalContent = {
        modal1: `
            <h2 style="color: #ffd700;">📖 A Origem de Pokémon</h2>
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/International_Pok%C3%A9mon_logo.svg/1200px-International_Pok%C3%A9mon_logo.svg.png" style="width:100%; border-radius:15px; margin:20px 0;">
            <p style="color: white;">Pokémon, abreviação de "Pocket Monsters" (Monstros de Bolso), é uma franquia de mídia que pertence a The Pokémon Company, tendo sido criada por Satoshi Tajiri em 1995.</p>
            <p style="color: white; margin-top: 10px;">A ideia central gira em torno de criaturas fictícias chamadas "Pokémon", que os humanos, conhecidos como Treinadores Pokémon, capturam e treinam para batalhar uns contra os outros.</p>
        `,
        modal2: `
            <h2 style="color: #ffd700;">🇯🇵 Raízes Japonesas</h2>
            <p style="color: white;">O Japão dos anos 90 era o cenário perfeito para o nascimento de Pokémon. A cultura japonesa de colecionismo e a popularidade dos jogos portáteis criaram o ambiente ideal.</p>
            <p style="color: white; margin-top: 10px;">Satoshi Tajiri se inspirou em sua infância colecionando insetos nos campos de Machida, Tóquio, para criar um jogo onde as crianças pudessem experimentar a mesma emoção de coletar e trocar.</p>
        `,
        modal3: `
            <h2 style="color: #ffd700;">🎮 A Revolução Game Boy</h2>
            <p style="color: white;">O Game Boy da Nintendo foi crucial para o sucesso de Pokémon. O cabo Game Link, que permitia conectar dois consoles, inspirou Tajiri a criar um jogo focado em troca e interação social.</p>
            <p style="color: white; margin-top: 10px;">Pokémon Red e Green foram desenvolvidos ao longo de 6 anos pela Game Freak, quase levando a empresa à falência antes do lançamento.</p>
        `,
        gallery1: `
            <h2 style="color: #ffd700;">⚡ PIKACHU</h2>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" style="width:200px; margin:20px auto; display:block;">
            <p style="color: white;">Pikachu é o Pokémon mascote da franquia. É um Pokémon do tipo Elétrico conhecido por suas bochechas vermelhas que liberam eletricidade. Ash Ketchum, o protagonista do anime, tem um Pikachu como seu parceiro principal.</p>
        `,
        gallery2: `
            <h2 style="color: #ffd700;">🔥 CHARIZARD</h2>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png" style="width:200px; margin:20px auto; display:block;">
            <p style="color: white;">Charizard é a evolução final de Charmander. É um Pokémon dos tipos Fogo e Voador, conhecido por sua força e poder de voo. É um dos Pokémon mais populares entre os fãs.</p>
        `,
        gallery3: `
            <h2 style="color: #ffd700;">🧠 MEWTWO</h2>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/150.png" style="width:200px; margin:20px auto; display:block;">
            <p style="color: white;">Mewtwo é um Pokémon lendário do tipo Psíquico. Foi criado por engenharia genética a partir do DNA de Mew. É conhecido por ser um dos Pokémon mais poderosos e inteligentes.</p>
        `,
        gallery4: `
            <h2 style="color: #ffd700;">🌿 BULBASAUR</h2>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/1.png" style="width:200px; margin:20px auto; display:block;">
            <p style="color: white;">Bulbasaur é o Pokémon #001 da Pokédex. É um Pokémon dos tipos Planta e Veneno. Tem uma semente em suas costas que cresce à medida que evolui.</p>
        `
    };

    function openModal(id) {
        document.getElementById('myModal').classList.add('active');
        document.getElementById('modalText').innerHTML = modalContent[id] || '<p style="color: white;">Informação não disponível.</p>';
    }

    function closeModal() {
        document.getElementById('myModal').classList.remove('active');
    }

    // Fechar modal clicando fora
    document.getElementById('myModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Animação de números
    function animateNumber(card, target) {
        const numberElement = card.querySelector('.stat-number');
        const current = parseInt(numberElement.innerText);
        if (current === target) return;
        
        let start = 0;
        const duration = 2000;
        const step = target / (duration / 16);
        
        const interval = setInterval(() => {
            start += step;
            if (start >= target) {
                numberElement.innerText = target.toLocaleString() + (target >= 1000 ? '+' : '');
                clearInterval(interval);
            } else {
                numberElement.innerText = Math.floor(start).toLocaleString() + (target >= 1000 ? '+' : '');
            }
        }, 16);
    }

    // Iniciar animação dos números quando visíveis
    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const target = parseInt(card.querySelector('.stat-number').dataset.target);
                animateNumber(card, target);
                statObserver.unobserve(card);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-card').forEach(card => {
        statObserver.observe(card);
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>

</body>
</html>
