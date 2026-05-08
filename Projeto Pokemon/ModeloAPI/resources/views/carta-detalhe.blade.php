{{-- resources/views/carta-detalhe.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card['name'] ?? 'Carta Pokémon' }} - Pokédex</title>
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', 'VT323', monospace;
            background:
                radial-gradient(circle at 17% 12%, rgba(84, 221, 234, 0.2), transparent 23%),
                radial-gradient(circle at 86% 7%, rgba(255, 216, 77, 0.16), transparent 18%),
                linear-gradient(135deg, #1b0b12 0%, #10151f 42%, #090d14 100%);
            min-height: 100vh;
            padding: 120px 20px 40px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(90deg, rgba(215, 25, 63, 0.3) 0 1px, transparent 1px 100%),
                linear-gradient(0deg, rgba(255, 255, 255, 0.045) 0 1px, transparent 1px 100%),
                linear-gradient(180deg, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.56));
            background-size: 54px 54px, 54px 54px, auto;
            z-index: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #e33535;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: 'Courier New', monospace;
        }

        .back-btn:hover {
            transform: translateX(-5px);
            background: #cc0000;
        }

        .card-3d-container {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            background: rgba(0,0,0,0.5);
            border-radius: 30px;
            padding: 30px;
            backdrop-filter: blur(5px);
        }

        .card-3d-side {
            flex: 1;
            min-width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
            perspective: 1500px;
        }

        .card-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        /* ===== CORREÇÃO DO FLIP 3D ===== */
        .card-3d {
            width: 100%;
            position: relative;
            cursor: grab;
            transition: transform 0.3s ease-out;
            transform-style: preserve-3d;
        }

        .card-3d:active {
            cursor: grabbing;
        }

        /* Container para frente e verso com altura automática */
        .card-front, .card-back {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transition: transform 0.6s cubic-bezier(0.4, 0.2, 0.2, 1);
        }

        /* Frente da carta - visível inicialmente */
        .card-front {
            transform: rotateY(0deg);
            position: relative;
            overflow: hidden;
            z-index: 2;
        }

        /* Verso da carta - escondido inicialmente */
        .card-back {
            position: absolute;
            top: 0;
            left: 0;
            transform: rotateY(180deg);
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border: 2px solid #ffd700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
            z-index: 1;
        }

        /* Quando a carta está virada */
        .card-3d.flipped .card-front {
            transform: rotateY(180deg);
        }

        .card-3d.flipped .card-back {
            transform: rotateY(0deg);
        }

        .card-image {
            width: 100%;
            border-radius: 20px;
            display: block;
        }

        /* Garantir que a imagem do verso tenha altura correta */
        .card-back img {
            width: 120px;
            opacity: 0.6;
            margin-bottom: 15px;
        }

        .card-back p {
            color: #ffd700;
            text-align: center;
            padding: 0 20px;
            font-size: 0.9rem;
        }

        /* Efeito de brilho reluzente */
        .shine-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 20px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            mix-blend-mode: overlay;
        }

        .shine-sparkle {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), 
                rgba(255,215,0,0.4) 0%, 
                rgba(255,215,0,0) 60%);
            pointer-events: none;
            border-radius: 20px;
            opacity: 0;
            transition: opacity 0.1s ease;
        }

        /* Controles */
        .card-controls {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .control-btn {
            background: rgba(227, 53, 53, 0.9);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .control-btn:hover {
            background: #ffd700;
            color: #333;
            transform: translateY(-2px);
        }

        .zoom-slider {
            width: 100%;
            margin-top: 15px;
        }

        .zoom-slider input {
            width: 100%;
            cursor: pointer;
            accent-color: #e33535;
        }

        /* Lado direito - Informações */
        .card-info-side {
            flex: 1;
            min-width: 300px;
            max-height: 80vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .card-info-side::-webkit-scrollbar {
            width: 8px;
        }

        .card-info-side::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
        }

        .card-info-side::-webkit-scrollbar-thumb {
            background: #e33535;
            border-radius: 10px;
        }

        .info-group {
            background: rgba(0,0,0,0.5);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            animation: fadeInUp 0.5s ease;
        }

        .info-label {
            font-weight: bold;
            color: #ffd700;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .info-value {
            color: white;
            font-size: 1rem;
        }

        .ability-item, .attack-item {
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .ability-item:last-child, .attack-item:last-child {
            border-bottom: none;
        }

        .ability-name, .attack-name {
            color: #ffd700;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .attack-cost {
            display: inline-block;
            background: rgba(0,0,0,0.5);
            padding: 2px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            margin-left: 10px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin: 5px;
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

        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            padding: 20px 30px;
            border-radius: 20px;
            color: white;
            display: none;
            z-index: 2000;
        }

        .spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #e33535;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .card-3d-container {
                flex-direction: column;
            }
            
            .card-3d-side {
                min-width: auto;
            }
            
            .card-info-side {
                max-height: none;
            }
        }
    </style>
</head>
<body>

@include('partials.menu-dropdown')

<div class="container">
    <a href="{{ url('/cartas-pokemon') }}" class="back-btn">
        ← VOLTAR PARA CARTAS
    </a>

    @if(isset($erro))
    <div class="info-group" style="text-align: center;">
        <div class="info-label">❌ ERRO</div>
        <div class="info-value">{{ $erro }}</div>
        <a href="{{ url('/cartas-pokemon') }}" class="back-btn" style="margin-top: 20px;">Voltar</a>
    </div>
    @elseif(isset($card))
    <div class="card-3d-container">
        <div class="card-3d-side">
            <div class="card-wrapper">
                <div class="card-3d" id="card3d">
                    <!-- FRENTE DA CARTA -->
                    <div class="card-front" id="cardFront">
                        <img class="card-image" id="cardImage" 
                             src="{{ $card['images']['large'] ?? ($card['images']['small'] ?? '') }}" 
                             alt="{{ $card['name'] }}"
                             onerror="this.src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png'">
                        <div class="shine-overlay"></div>
                        <div class="shine-sparkle" id="shineSparkle"></div>
                    </div>
                    
                    <!-- VERSO DA CARTA -->
                    <div class="card-back">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/International_Pok%C3%A9mon_logo.svg" alt="Pokémon">
                        <p>🃏 Carta Pokémon TCG</p>
                        <p style="font-size: 0.8rem;">{{ $card['set']['name'] ?? 'Coleção' }}<br>#{{ $card['number'] ?? '???' }}</p>
                        <div style="margin-top: 20px;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: radial-gradient(circle, #ffd700, #ff8c00); margin: 0 auto;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-controls">
                <button class="control-btn" onclick="flipCard()">
                    🔄 VIRAR CARTA
                </button>
                <button class="control-btn" onclick="zoomIn()">
                    🔍 ZOOM +
                </button>
                <button class="control-btn" onclick="zoomOut()">
                    🔍 ZOOM -
                </button>
                <button class="control-btn" onclick="resetZoom()">
                    🔄 RESETAR
                </button>
            </div>

            <div class="zoom-slider">
                <input type="range" id="zoomRange" min="0.5" max="2" step="0.01" value="1" onchange="updateZoom(this.value)">
            </div>

            <div style="text-align: center; margin-top: 10px; color: rgba(255,255,255,0.5); font-size: 0.7rem;">
                🖱️ Clique e arraste para girar a carta | 🔄 Clique em "Virar Carta" para ver o verso
            </div>
        </div>

        <!-- LADO DIREITO - INFORMAÇÕES -->
        <div class="card-info-side">
            <div class="info-group">
                <div class="info-label">🃏 CARTA</div>
                <div class="info-value" style="font-size: 1.5rem; color: #ffd700;">{{ $card['name'] }}</div>
                <div class="info-value">Nº {{ $card['number'] ?? '???' }} | {{ $card['set']['name'] ?? 'Coleção' }}</div>
                <div class="info-value">⭐ Raridade: {{ $card['rarity'] ?? 'Comum' }}</div>
            </div>

            @if(isset($card['types']) && !empty($card['types']))
            <div class="info-group">
                <div class="info-label">⚡ TIPOS</div>
                <div class="info-value">
                    @foreach($card['types'] as $type)
                        @php
                            $typeBg = match($type) {
                                'Fire' => '#F08030', 'Water' => '#6890F0', 'Grass' => '#78C850',
                                'Lightning' => '#F8D030', 'Psychic' => '#F85888', 'Fighting' => '#C03028',
                                'Darkness' => '#705848', 'Metal' => '#B8B8D0', 'Dragon' => '#7038F8',
                                'Fairy' => '#EE99AC', default => '#A8A878'
                            };
                        @endphp
                        <span class="badge" style="background: {{ $typeBg }};">{{ $type }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($card['hp']))
            <div class="info-group">
                <div class="info-label">❤️ HP</div>
                <div class="info-value" style="font-size: 1.8rem; color: #00ff41;">{{ $card['hp'] }}</div>
            </div>
            @endif

            @if(isset($card['abilities']) && !empty($card['abilities']))
            <div class="info-group">
                <div class="info-label">✨ HABILIDADES</div>
                @foreach($card['abilities'] as $ability)
                <div class="ability-item">
                    <div class="ability-name">{{ $ability['name'] ?? 'Habilidade' }}</div>
                    <div class="info-value">{{ $ability['text'] ?? 'Sem descrição' }}</div>
                </div>
                @endforeach
            </div>
            @endif

            @if(isset($card['attacks']) && !empty($card['attacks']))
            <div class="info-group">
                <div class="info-label">⚔️ ATAQUES</div>
                @foreach($card['attacks'] as $attack)
                <div class="attack-item">
                    <div class="attack-name">
                        {{ $attack['name'] ?? 'Ataque' }}
                        @if(isset($attack['damage']))
                            <span class="attack-cost">💥 {{ $attack['damage'] }}</span>
                        @endif
                    </div>
                    @if(isset($attack['cost']) && !empty($attack['cost']))
                    <div class="info-value">💰 Custo: {{ is_array($attack['cost']) ? implode(', ', $attack['cost']) : $attack['cost'] }}</div>
                    @endif
                    <div class="info-value">{{ $attack['text'] ?? '' }}</div>
                </div>
                @endforeach
            </div>
            @endif

            @if(isset($card['flavorText']))
            <div class="info-group">
                <div class="info-label">📝 DESCRIÇÃO</div>
                <div class="info-value" style="font-style: italic;">"{{ $card['flavorText'] }}"</div>
            </div>
            @endif

            @if(isset($card['artist']))
            <div class="info-group">
                <div class="info-label">🎨 ARTISTA</div>
                <div class="info-value">{{ $card['artist'] }}</div>
            </div>
            @endif

            @if(isset($card['legalities']))
            <div class="info-group">
                <div class="info-label">📜 FORMATOS LEGAIS</div>
                <div class="info-value">
                    @foreach($card['legalities'] as $format => $legal)
                        @if($legal == 'Legal')
                            <span class="badge" style="background: #4CAF50;">{{ ucfirst($format) }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
    let card3d = document.getElementById('card3d');
    let cardFront = document.getElementById('cardFront');
    let shineSparkle = document.getElementById('shineSparkle');
    let currentZoom = 1;
    let isDragging = false;
    let startX, startY, currentXRot = 0, currentYRot = 0;
    let targetXRot = 0, targetYRot = 0;

    function updateShinePosition(rotX, rotY) {
        let shineX = 50 + (rotY * 1.5);
        let shineY = 50 - (rotX * 1.5);
        
        shineX = Math.min(100, Math.max(0, shineX));
        shineY = Math.min(100, Math.max(0, shineY));
        
        if (shineSparkle) {
            shineSparkle.style.setProperty('--x', `${shineX}%`);
            shineSparkle.style.setProperty('--y', `${shineY}%`);
            shineSparkle.style.background = `radial-gradient(circle at ${shineX}% ${shineY}%, 
                rgba(255,215,0,0.5) 0%, 
                rgba(255,215,0,0.2) 30%,
                rgba(255,255,255,0) 70%)`;
            shineSparkle.style.opacity = '0.6';
        }
        
        if (cardFront) {
            let overlay = cardFront.querySelector('.shine-overlay');
            if (overlay) {
                overlay.style.background = `radial-gradient(circle at ${shineX}% ${shineY}%, 
                    rgba(255,215,0,0.3) 0%, 
                    rgba(255,255,255,0.1) 40%,
                    rgba(255,255,255,0) 80%)`;
                overlay.style.opacity = '0.7';
            }
        }
    }

    function flipCard() {
        card3d.classList.toggle('flipped');
    }

    function zoomIn() {
        currentZoom = Math.min(2, currentZoom + 0.1);
        updateZoom(currentZoom);
    }

    function zoomOut() {
        currentZoom = Math.min(2, Math.max(0.5, currentZoom - 0.1));
        updateZoom(currentZoom);
    }

    function resetZoom() {
        currentZoom = 1;
        updateZoom(currentZoom);
        resetRotation();
    }

    function updateZoom(value) {
        currentZoom = parseFloat(value);
        document.getElementById('zoomRange').value = currentZoom;
        card3d.style.transform = `scale(${currentZoom}) rotateX(${currentXRot}deg) rotateY(${currentYRot}deg)`;
        updateShinePosition(currentXRot, currentYRot);
    }

    function resetRotation() {
        targetXRot = 0;
        targetYRot = 0;
        currentXRot = 0;
        currentYRot = 0;
        card3d.style.transform = `scale(${currentZoom}) rotateX(0deg) rotateY(0deg)`;
        updateShinePosition(0, 0);
    }

    // Eventos de mouse
    card3d.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        card3d.style.cursor = 'grabbing';
        e.preventDefault();
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        
        let deltaX = e.clientX - startX;
        let deltaY = e.clientY - startY;
        
        targetYRot = Math.min(30, Math.max(-30, targetYRot + deltaX * 0.5));
        targetXRot = Math.min(30, Math.max(-30, targetXRot - deltaY * 0.5));
        
        currentXRot = targetXRot;
        currentYRot = targetYRot;
        
        card3d.style.transform = `scale(${currentZoom}) rotateX(${currentXRot}deg) rotateY(${currentYRot}deg)`;
        updateShinePosition(currentXRot, currentYRot);
        
        startX = e.clientX;
        startY = e.clientY;
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        card3d.style.cursor = 'grab';
    });

    // Touch events
    card3d.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        e.preventDefault();
    });

    document.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        
        let deltaX = e.touches[0].clientX - startX;
        let deltaY = e.touches[0].clientY - startY;
        
        targetYRot = Math.min(30, Math.max(-30, targetYRot + deltaX * 0.5));
        targetXRot = Math.min(30, Math.max(-30, targetXRot - deltaY * 0.5));
        
        currentXRot = targetXRot;
        currentYRot = targetYRot;
        
        card3d.style.transform = `scale(${currentZoom}) rotateX(${currentXRot}deg) rotateY(${currentYRot}deg)`;
        updateShinePosition(currentXRot, currentYRot);
        
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });

    document.addEventListener('touchend', () => {
        isDragging = false;
    });

    card3d.style.cursor = 'grab';

    window.addEventListener('pageshow', function() {
        const loaders = document.querySelectorAll('.loading');
        loaders.forEach(loader => loader.remove());
    });

    document.querySelectorAll('.info-group').forEach((group, index) => {
        group.style.animationDelay = `${index * 0.05}s`;
    });

    setTimeout(() => {
        updateShinePosition(0, 0);
    }, 100);
</script>

</body>
</html>
