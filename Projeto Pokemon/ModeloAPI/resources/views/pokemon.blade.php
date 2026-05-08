<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<title>Pokédex - Enciclopédia Pokémon</title>

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
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 0;
}

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

.pokedex {
    display: flex;
    background: #e33535;
    border-radius: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.3), 0 0 30px rgba(0,0,0,0.5);
    overflow: hidden;
    position: relative;
    border: 2px solid #aa2222;
    z-index: 1;
}

.pokedex::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #aa2222;
    transform: translateX(-50%);
    box-shadow: 0 0 0 2px #cc3333;
}

.left {
    padding: 25px;
    width: 340px;
    background: #e33535;
    position: relative;
}

.top-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    background: rgba(0,0,0,0.1);
    border-radius: 15px;
}

.led-lights {
    display: flex;
    gap: 12px;
}

.led {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    position: relative;
}

.led-red {
    background: #ff0000;
    box-shadow: 0 0 10px rgba(255,0,0,0.8);
    animation: blink 2s infinite;
}

.led-blue {
    background: #2196F3;
    box-shadow: 0 0 8px rgba(33,150,243,0.6);
}

.led-green {
    background: #4CAF50;
    box-shadow: 0 0 8px rgba(76,175,80,0.6);
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.screen {
    background: #2c3e2e;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    border: 4px solid #1a2a1c;
    box-shadow: inset 0 0 10px rgba(0,0,0,0.5), 0 5px 0 rgba(0,0,0,0.2);
    margin-bottom: 15px;
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.screen img {
    width: 200px;
    height: 200px;
    object-fit: contain;
    filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));
    transition: transform 0.3s ease;
}

.screen img:hover {
    transform: scale(1.05);
}

/* Efeito brilhante para Shiny */
.shiny-effect {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    border-radius: 15px;
    animation: shinyPulse 2s ease-in-out infinite;
}

@keyframes shinyPulse {
    0%, 100% { box-shadow: 0 0 10px rgba(255, 215, 0, 0); }
    50% { box-shadow: 0 0 30px rgba(255, 215, 0, 0.5); }
}

/* Botão Shiny */
.shiny-btn {
    background: linear-gradient(135deg, #ffd700, #ff8c00);
    color: #333;
    border: none;
    border-radius: 25px;
    padding: 8px 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Courier New', monospace;
    margin-bottom: 10px;
    width: 100%;
}

.shiny-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.5);
}

.shiny-btn.active {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    box-shadow: 0 0 15px #ffd700;
}

/* Botão de som */
.sound-btn {
    background: #2c3e2e;
    color: #00ff41;
    border: 1px solid #00ff4133;
    border-radius: 25px;
    padding: 8px 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Courier New', monospace;
    width: 100%;
}

.sound-btn:hover {
    background: #3a5a3e;
    transform: translateY(-2px);
}

/* Seletor de estilo */
.image-style-selector {
    margin-bottom: 10px;
}

.style-select {
    width: 100%;
    padding: 8px 12px;
    border: none;
    border-radius: 20px;
    background: #2c3e2e;
    color: #00ff41;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    cursor: pointer;
    border: 1px solid #00ff4133;
}

.d-pad {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 15px;
}

.direction-btn {
    width: 60px;
    height: 60px;
    background: #333;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    border-radius: 10px;
    box-shadow: 0 4px 0 #111;
    transition: 0.1s linear;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.direction-btn:hover {
    background: #444;
    transform: translateY(-2px);
    box-shadow: 0 6px 0 #111;
}

.right {
    padding: 25px;
    width: 320px;
    background: #d62e2e;
}

.info-display {
    background: #1a3a1c;
    color: #00ff41;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
    font-family: 'Courier New', monospace;
    font-size: 16px;
    border: 2px solid #0a2a0c;
    box-shadow: inset 0 0 15px rgba(0,0,0,0.5), 0 5px 0 rgba(0,0,0,0.2);
    text-shadow: 0 0 5px #00ff41;
}

.info-display p {
    margin: 12px 0;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #00ff4133;
    padding: 5px 0;
}

.info-display strong {
    color: #ffeb3b;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.type-badge {
    display: inline-block;
    padding: 4px 12px;
    margin: 5px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
}

.search-box {
    margin-top: 20px;
}

.search-input {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 25px;
    background: #2c3e2e;
    color: #00ff41;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    outline: none;
    border: 1px solid #00ff4133;
}

.search-btn {
    width: 100%;
    margin-top: 10px;
    padding: 10px;
    background: #00c3ff;
    border: none;
    border-radius: 25px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    font-family: 'Courier New', monospace;
    transition: 0.3s;
}

.search-btn:hover {
    background: #0099cc;
    transform: translateY(-2px);
}

.custom-btn {
    display: block;
    text-align: center;
    padding: 10px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 10px;
    transition: all 0.3s ease;
}

.custom-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.custom-btn-secondary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.custom-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.custom-badge {
    display: inline-block;
    background: #ffd700;
    color: #333;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 8px;
    text-shadow: none;
}

.shiny-badge {
    display: inline-block;
    background: linear-gradient(135deg, #ffd700, #ff8c00);
    color: #333;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 8px;
    animation: shine 1s infinite;
}

@keyframes shine {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.button-group {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.button-group .shiny-btn,
.button-group .sound-btn {
    flex: 1;
}

@media (max-width: 700px) {
    .pokedex {
        flex-direction: column;
        width: 100%;
        max-width: 350px;
        margin-top: 80px;
    }
    
    .pokedex::before {
        display: none;
    }
    
    .left, .right {
        width: 100%;
    }
    
    .logo-container {
        top: 10px;
        left: 10px;
    }
    
    .pokemon-logo {
        width: 200px;
    }
    
    .screen img {
        width: 150px;
        height: 150px;
    }
}
</style>
</head>
<body>

<div class="logo-container">
    <a href="{{ url('/historia-pokemon') }}" target="_blank">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/International_Pok%C3%A9mon_logo.svg" 
             alt="Pokémon Logo" 
             class="pokemon-logo">
    </a>
</div>

<div class="pokedex">

    <!-- LADO ESQUERDO -->
    <div class="left">
        <div class="top-panel">
            <div class="led-lights">
                <div class="led led-red"></div>
                <div class="led led-blue"></div>
                <div class="led led-green"></div>
            </div>
            <div style="color: white; font-size: 12px; font-weight: bold;">POKÉDEX</div>
        </div>

        <div class="screen" id="screenContainer">
            @if(isset($erro))
                <div style="color: #ff0000; text-align: center;">
                    <p>{{ $erro }}</p>
                </div>
            @else
                <img id="pokemonImage" 
                     src="{{ $pokemon['sprites']['official_artwork'] ?? $pokemon['sprites']['front_default'] }}" 
                     alt="{{ $pokemon['name'] }}">
                <div id="shinyEffect" class="shiny-effect" style="display: none;"></div>
            @endif
        </div>

        @if(!isset($erro) && !isset($pokemon['is_custom']))
        <div class="button-group">
            <button id="shinyBtn" class="shiny-btn">✨ MODO SHINY</button>
            <button id="soundBtn" class="sound-btn">🔊 OUVIR CRY</button>
        </div>
        @endif

        @if(!isset($erro))
        <div class="image-style-selector">
            <select id="imageStyle" class="style-select">
                <option value="official_artwork">✨ Official Artwork (HD)</option>
                <option value="home">🏠 Home Style</option>
                <option value="dream_world">💭 Dream World</option>
                <option value="showdown">⚔️ Showdown Style</option>
                <option value="front_default">🎮 Pixel Art (Original)</option>
            </select>
        </div>
        @endif

        <div class="d-pad">
            @php
                $idAtual = isset($pokemon['id']) ? $pokemon['id'] : 1;
                $idAnterior = $idAtual - 1;
                $idProximo = $idAtual + 1;
                $maxId = 9999;
                
                if ($idAnterior < 1) $idAnterior = 1025;
                if ($idProximo > $maxId) $idProximo = 1;
            @endphp
            
            <a href="{{ url('/pokemon?id=' . $idAnterior) }}" class="direction-btn">◀</a>
            <a href="{{ url('/pokemon?id=' . $idProximo) }}" class="direction-btn">▶</a>
        </div>
    </div>

    <!-- LADO DIREITO -->
    <div class="right">
        <div class="info-display">
            @if(isset($erro))
                <p style="color: #ff0000; text-align: center;">{{ $erro }}</p>
            @else
                <p>
                    <strong>Nº {{ sprintf('%04d', $pokemon['id']) }}</strong> 
                    <span style="color: #ffeb3b;">
                        {{ strtoupper($pokemon['name']) }}
                        @if(isset($pokemon['is_custom']) && $pokemon['is_custom'])
                            <span class="custom-badge">CUSTOM</span>
                        @endif
                        <span id="shinyBadge" class="shiny-badge" style="display: none;">✨ SHINY</span>
                    </span>
                </p>
                <p><strong>ALTURA</strong> <span>{{ $pokemon['height'] / 10 }} m</span></p>
                <p><strong>PESO</strong> <span>{{ $pokemon['weight'] / 10 }} kg</span></p>
                <p><strong>TIPO</strong> 
                    <span>
                        @foreach($pokemon['types'] as $type)
                            @php
                                $typeName = $type['type']['name'];
                                $typeColors = [
                                    'normal' => '#A8A878', 'fire' => '#F08030', 'water' => '#6890F0',
                                    'electric' => '#F8D030', 'grass' => '#78C850', 'ice' => '#98D8D8',
                                    'fighting' => '#C03028', 'poison' => '#A040A0', 'ground' => '#E0C068',
                                    'flying' => '#A890F0', 'psychic' => '#F85888', 'bug' => '#A8B820',
                                    'rock' => '#B8A038', 'ghost' => '#705898', 'dragon' => '#7038F8',
                                    'dark' => '#705848', 'steel' => '#B8B8D0', 'fairy' => '#EE99AC'
                                ];
                                $bgColor = $typeColors[$typeName] ?? '#68A090';
                            @endphp
                            <span class="type-badge" style="background: {{ $bgColor }}">{{ strtoupper($typeName) }}</span>
                        @endforeach
                    </span>
                </p>
                <p><strong>HABILIDADE</strong> 
                    <span>
                        @if(isset($pokemon['is_custom']) && $pokemon['is_custom'])
                            {{ ucfirst($pokemon['abilities'][0]['ability']['name']) }}
                        @else
                            {{ ucfirst($pokemon['abilities'][0]['ability']['name']) }}
                        @endif
                    </span>
                </p>
                @if(isset($pokemon['is_custom']) && $pokemon['is_custom'])
                    <p><strong>CRIADO EM</strong> <span>{{ date('d/m/Y', strtotime($pokemon['created_at'])) }}</span></p>
                @endif
            @endif
        </div>

        <form method="GET" action="{{ url('/pokemon') }}">
            <div class="search-box">
                <input type="text" name="pokemon" class="search-input" placeholder="🔍 Buscar Pokémon por nome..." autocomplete="off">
                <button type="submit" class="search-btn">🔍 BUSCAR POKÉMON</button>
            </div>
        </form>

        <div style="margin-top: 15px;">
            <a href="{{ route('custom-pokemon.create') }}" class="custom-btn custom-btn-primary">
                ➕ Criar Novo Pokémon
            </a>
            <a href="{{ route('custom-pokemon.index') }}" class="custom-btn custom-btn-secondary">
                📋 Ver Meus Pokémon Customizados
            </a>
        </div>
        
        <div style="margin-top: 15px; text-align: center; font-size: 11px; color: rgba(255,255,255,0.7);">
            ← → Clique nas setas para navegar
        </div>
    </div>

</div>

@if(isset($pokemon['id']) && !isset($erro) && !isset($pokemon['is_custom']))
<script>
    const pokemonImage = document.getElementById('pokemonImage');
    const imageStyle = document.getElementById('imageStyle');
    const shinyBtn = document.getElementById('shinyBtn');
    const soundBtn = document.getElementById('soundBtn');
    const shinyBadge = document.getElementById('shinyBadge');
    const shinyEffect = document.getElementById('shinyEffect');
    const screenContainer = document.getElementById('screenContainer');
    const pokemonId = {{ $pokemon['id'] }};
    
    let isShiny = false;
    let currentStyle = 'official_artwork';
    let audio = null;

    // URLs das imagens
    const imageUrls = {
        normal: {
            official_artwork: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${pokemonId}.png`,
            home: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/${pokemonId}.png`,
            dream_world: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/dream-world/${pokemonId}.svg`,
            showdown: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/${pokemonId}.gif`,
            front_default: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemonId}.png`
        },
        shiny: {
            official_artwork: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/shiny/${pokemonId}.png`,
            home: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/shiny/${pokemonId}.png`,
            showdown: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/shiny/${pokemonId}.gif`,
            front_default: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/shiny/${pokemonId}.png`
        }
    };

    // URL do som (cry)
    const cryUrl = `https://raw.githubusercontent.com/PokeAPI/cries/main/cries/pokemon/latest/${pokemonId}.ogg`;

    // Função para atualizar a imagem
    function updateImage() {
        const url = isShiny ? imageUrls.shiny[currentStyle] : imageUrls.normal[currentStyle];
        if (url) {
            pokemonImage.src = url;
            pokemonImage.onerror = function() {
                // Fallback para o estilo padrão
                const fallbackUrl = isShiny ? imageUrls.shiny.official_artwork : imageUrls.normal.official_artwork;
                this.src = fallbackUrl;
            };
        }
    }

    // Função para tocar o som
    function playCry() {
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
        }
        audio = new Audio(cryUrl);
        audio.play().catch(e => console.log('Erro ao tocar som:', e));
    }

    // Alternar modo Shiny
    function toggleShiny() {
        isShiny = !isShiny;
        
        if (isShiny) {
            shinyBtn.classList.add('active');
            shinyBtn.textContent = '✨ MODO NORMAL';
            shinyBadge.style.display = 'inline-block';
            shinyEffect.style.display = 'block';
            screenContainer.style.animation = 'shinyPulse 2s ease-in-out infinite';
        } else {
            shinyBtn.classList.remove('active');
            shinyBtn.textContent = '✨ MODO SHINY';
            shinyBadge.style.display = 'none';
            shinyEffect.style.display = 'none';
            screenContainer.style.animation = 'none';
        }
        
        updateImage();
    }

    // Eventos
    if (shinyBtn) shinyBtn.addEventListener('click', toggleShiny);
    if (soundBtn) soundBtn.addEventListener('click', playCry);
    
    if (imageStyle) {
        imageStyle.addEventListener('change', function() {
            currentStyle = this.value;
            updateImage();
        });
    }

    // Fallback para imagem quebrada
    pokemonImage.onerror = function() {
        this.src = imageUrls.normal.official_artwork;
    };
</script>
@endif

@if(session('success'))
<script>
    setTimeout(function() {
        alert('✅ {{ session('success') }}');
    }, 100);
</script>
@endif

</body>
</html>