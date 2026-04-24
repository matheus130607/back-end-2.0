<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>História do Pokémon - A Jornada Completa</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #0a0a2e 0%, #1a1a4e 50%, #0a0a2e 100%);
        color: white;
        overflow-x: hidden;
    }

    /* Header Hero */
    .hero {
        background: linear-gradient(135deg, #ff6b6b 0%, #e33535 50%, #cc0000 100%);
        padding: 60px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/></svg>') repeat;
        opacity: 0.5;
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
        font-size: 4rem;
        text-shadow: 3px 3px 6px rgba(0,0,0,0.5);
        margin-bottom: 20px;
        animation: fadeInDown 1s ease;
    }

    .hero p {
        font-size: 1.5rem;
        opacity: 0.9;
        animation: fadeInUp 1s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Navegação */
    .nav-timeline {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        padding: 15px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .nav-timeline a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 25px;
        transition: all 0.3s ease;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .nav-timeline a:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Container Principal */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Seções */
    .section {
        margin: 80px 0;
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.8s ease;
    }

    .section.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .section-title {
        font-size: 3rem;
        text-align: center;
        margin-bottom: 50px;
        background: linear-gradient(45deg, #ff6b6b, #ffd93d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    /* Cards */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin: 40px 0;
    }

    .card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.2);
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        background: rgba(255,255,255,0.15);
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
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #ffd93d;
    }

    .card p {
        line-height: 1.6;
        opacity: 0.9;
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
        background: linear-gradient(to bottom, #ff6b6b, #ffd93d);
        transform: translateX(-50%);
    }

    .timeline-item {
        display: flex;
        justify-content: space-between;
        margin: 40px 0;
        position: relative;
    }

    .timeline-item:nth-child(even) {
        flex-direction: row-reverse;
    }

    .timeline-content {
        width: 45%;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }

    .timeline-content:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .timeline-year {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #ff6b6b, #e33535);
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 40px 0;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255,255,255,0.2);
    }

    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        background: linear-gradient(45deg, #ff6b6b, #ffd93d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Galeria Interativa */
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
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: linear-gradient(135deg, #1a1a4e, #0a0a2e);
        border-radius: 30px;
        padding: 40px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        border: 2px solid rgba(255,255,255,0.2);
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #ff6b6b;
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
        background: #e33535;
    }

    /* Botão Voltar */
    .back-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #ff6b6b, #e33535);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        transition: all 0.3s ease;
        z-index: 100;
    }

    .back-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.7);
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
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
        }
        
        .timeline-year {
            left: 20px;
            transform: translateX(-50%);
        }
        
        .nav-timeline {
            gap: 10px;
        }
        
        .nav-timeline a {
            padding: 8px 15px;
            font-size: 0.9rem;
        }
    }
</style>
</head>
<body>

    <!-- Header Hero -->
    <div class="hero">
        <div class="hero-content">
            <h1>🎮 A História do Pokémon</h1>
            <p>Descubra a jornada épica da franquia que conquistou o mundo</p>
        </div>
    </div>

    <!-- Navegação -->
    <nav class="nav-timeline">
        <a href="#origem">🎯 Origem</a>
        <a href="#criador">👤 Criador</a>
        <a href="#timeline">📅 Linha do Tempo</a>
        <a href="#jogos">🎮 Jogos</a>
        <a href="#anime">📺 Anime</a>
        <a href="#curiosidades">✨ Curiosidades</a>
        <a href="#impacto">🌍 Impacto</a>
    </nav>

    <!-- Container Principal -->
    <div class="container">

        <!-- Seção: A Origem -->
        <div class="section" id="origem">
            <h2 class="section-title">🎯 A Origem de Tudo</h2>
            
            <div class="card-grid">
                <div class="card" onclick="openModal('modal1')">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/International_Pok%C3%A9mon_logo.svg/1200px-International_Pok%C3%A9mon_logo.svg.png" alt="Pokémon Logo">
                    <h3>O Início da Aventura</h3>
                    <p>Pokémon começou como uma ideia simples que se tornou um fenômeno global. A palavra "Pokémon" é uma contração de "Pocket Monsters" (Monstros de Bolso).</p>
                </div>

                <div class="card" onclick="openModal('modal2')">
                    <img src="https://images.unsplash.com/photo-1542779283-429940ce8336?w=500" alt="Japão">
                    <h3>Raízes Japonesas</h3>
                    <p>Nascido no Japão, Pokémon foi inspirado na paixão de Satoshi Tajiri por colecionar insetos durante sua infância.</p>
                </div>

                <div class="card" onclick="openModal('modal3')">
                    <img src="https://images.unsplash.com/photo-1612404730960-5c71577fca11?w=500" alt="Game Boy">
                    <h3>A Revolução Game Boy</h3>
                    <p>Os primeiros jogos Pokémon Red e Green foram lançados para Game Boy em 1996, revolucionando os RPGs portáteis.</p>
                </div>
            </div>
        </div>

        <!-- Seção: O Criador -->
        <div class="section" id="criador">
            <h2 class="section-title">👤 Satoshi Tajiri - O Visionário</h2>
            
            <div class="card-grid">
                <div class="card">
                    <h3>🦋 O Colecionador de Insetos</h3>
                    <p>Satoshi Tajiri nasceu em 28 de agosto de 1965 em Machida, Tóquio. Quando criança, era fascinado por colecionar insetos, uma paixão que mais tarde inspiraria a criação de Pokémon.</p>
                </div>

                <div class="card">
                    <h3>🎮 Game Freak</h3>
                    <p>Em 1982, Tajiri e seus amigos fundaram a revista Game Freak, dedicada a jogos. Em 1989, transformaram a revista em uma empresa de desenvolvimento de jogos.</p>
                </div>

                <div class="card">
                    <h3>💡 A Grande Ideia</h3>
                    <p>Inspirado pelo Game Link Cable do Game Boy, Tajiri imaginou um jogo onde jogadores poderiam trocar criaturas entre si, dando origem ao conceito de Pokémon.</p>
                </div>
            </div>
        </div>

        <!-- Seção: Linha do Tempo -->
        <div class="section" id="timeline">
            <h2 class="section-title">📅 Linha do Tempo Pokémon</h2>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">1996</div>
                    <div class="timeline-content">
                        <h3>Pokémon Red & Green</h3>
                        <p>Lançamento no Japão dos primeiros jogos Pokémon, com 151 criaturas para colecionar.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">1998</div>
                    <div class="timeline-content">
                        <h3>Pokémon Chega ao Ocidente</h3>
                        <p>Pokémon Red & Blue são lançados nos Estados Unidos, iniciando a febre mundial.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">1999</div>
                    <div class="timeline-content">
                        <h3>Anime e Filme</h3>
                        <p>O anime Pokémon estreia mundialmente e "Pokémon: O Filme" quebra recordes de bilheteria.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2016</div>
                    <div class="timeline-content">
                        <h3>Pokémon GO</h3>
                        <p>O jogo de realidade aumentada se torna um fenômeno global com mais de 500 milhões de downloads.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Estatísticas -->
        <div class="section" id="jogos">
            <h2 class="section-title">📊 Pokémon em Números</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">1000+</div>
                    <p>Criaturas Pokémon</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number">380M+</div>
                    <p>Jogos Vendidos</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number">25+</div>
                    <p>Anos de História</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number">1200+</div>
                    <p>Episódios de Anime</p>
                </div>
            </div>
        </div>

        <!-- Seção: Galeria Interativa -->
        <div class="section" id="anime">
            <h2 class="section-title">🖼️ Galeria Pokémon</h2>
            
            <div class="gallery">
                <div class="gallery-item">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Pikachu">
                    <div class="gallery-overlay">
                        <h3>Pikachu</h3>
                        <p>O mascote oficial da franquia</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png" alt="Charizard">
                    <div class="gallery-overlay">
                        <h3>Charizard</h3>
                        <p>Um dos Pokémon mais populares</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/150.png" alt="Mewtwo">
                    <div class="gallery-overlay">
                        <h3>Mewtwo</h3>
                        <p>O lendário Pokémon psíquico</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Curiosidades -->
        <div class="section" id="curiosidades">
            <h2 class="section-title">✨ Curiosidades Incríveis</h2>
            
            <div class="card-grid">
                <div class="card">
                    <h3>🎵 Pikachu Original</h3>
                    <p>O design original de Pikachu era bem diferente! Ele parecia mais com um esquilo gordo e não tinha as bochechas vermelhas características.</p>
                </div>
                <div class="card">
                    <h3>🚫 Pokémon Banidos</h3>
                    <p>Alguns episódios do anime foram banidos em vários países, incluindo o famoso "Porygon Shock" que causou convulsões em crianças japonesas.</p>
                </div>
                <div class="card">
                    <h3>👻 Lavender Town</h3>
                    <p>A música de Lavender Town se tornou uma lenda urbana, com rumores de que causava pesadelos e até suicídios em crianças.</p>
                </div>
            </div>
        </div>

        <!-- Seção: Impacto Cultural -->
        <div class="section" id="impacto">
            <h2 class="section-title">🌍 Impacto Cultural</h2>
            
            <div class="card-grid">
                <div class="card">
                    <h3>🎨 Na Arte</h3>
                    <p>Pokémon influenciou gerações de artistas, designers e criadores de conteúdo em todo o mundo.</p>
                </div>
                <div class="card">
                    <h3>👥 Na Sociedade</h3>
                    <p>Eventos como o Pokémon GO Fest reúnem milhões de pessoas, criando comunidades globais.</p>
                </div>
                <div class="card">
                    <h3>💰 Na Economia</h3>
                    <p>Pokémon é a franquia de mídia mais lucrativa de todos os tempos, superando Star Wars e Marvel.</p>
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

    <!-- Botão Voltar -->
    <a href="javascript:history.back()" class="back-btn">← Voltar para Pokédex</a>

    <script>
        // Animação de scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.section').forEach(section => {
            observer.observe(section);
        });

        // Modal
        const modalContent = {
            modal1: `
                <h2>A Origem de Pokémon</h2>
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/International_Pok%C3%A9mon_logo.svg/1200px-International_Pok%C3%A9mon_logo.svg.png" style="width:100%; border-radius:15px; margin:20px 0;">
                <p>Pokémon, abreviação de "Pocket Monsters" (Monstros de Bolso em japonês: ポケットモンスター), é uma franquia de mídia que pertence a The Pokémon Company, tendo sido criada por Satoshi Tajiri em 1995.</p>
                <p>A ideia central gira em torno de criaturas fictícias chamadas "Pokémon", que os humanos, conhecidos como Treinadores Pokémon, capturam e treinam para batalhar uns contra os outros.</p>
            `,
            modal2: `
                <h2>Raízes Japonesas</h2>
                <p>O Japão dos anos 90 era o cenário perfeito para o nascimento de Pokémon. A cultura japonesa de colecionismo e a popularidade dos jogos portáteis criaram o ambiente ideal.</p>
                <p>Satoshi Tajiri se inspirou em sua infância colecionando insetos nos campos de Machida, Tóquio, para criar um jogo onde as crianças pudessem experimentar a mesma emoção de coletar e trocar.</p>
            `,
            modal3: `
                <h2>A Revolução Game Boy</h2>
                <p>O Game Boy da Nintendo foi crucial para o sucesso de Pokémon. O cabo Game Link, que permitia conectar dois consoles, inspirou Tajiri a criar um jogo focado em troca e interação social.</p>
                <p>Pokémon Red e Green foram desenvolvidos ao longo de 6 anos pela Game Freak, quase levando a empresa à falência antes do lançamento.</p>
            `
        };

        function openModal(id) {
            document.getElementById('myModal').classList.add('active');
            document.getElementById('modalText').innerHTML = modalContent[id];
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

        // Smooth scroll para links de navegação
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

</body>
</html>