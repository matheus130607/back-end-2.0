<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Pokédex - Enciclopédia Pokémon</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Courier New', 'VT323', monospace;
    /* Background de floresta desfocada escura */
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

/* Overlay escuro sobre o background */
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

/* Logo do Pokémon no topo superior esquerdo */
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

/* CONTAINER PRINCIPAL - ESTILO POKÉDEX */
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

/* DOBRA CENTRAL */
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

/* LADO ESQUERDO */
.left {
    padding: 25px;
    width: 320px;
    background: #e33535;
    position: relative;
}

/* PAINEL SUPERIOR COM LUZES */
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

.led-red::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.3);
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

/* TELA PRINCIPAL */
.screen {
    background: #2c3e2e;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    border: 4px solid #1a2a1c;
    box-shadow: inset 0 0 10px rgba(0,0,0,0.5), 0 5px 0 rgba(0,0,0,0.2);
    margin-bottom: 20px;
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.screen img {
    width: 180px;
    height: 180px;
    image-rendering: pixelated;
    filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));
    transition: transform 0.3s ease;
}

.screen img:hover {
    transform: scale(1.05);
}

/* BOTÕES DE DIREÇÃO */
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

.direction-btn:active {
    transform: translateY(4px);
    box-shadow: 0 2px 0 #111;
}

/* LADO DIREITO */
.right {
    padding: 25px;
    width: 320px;
    background: #d62e2e;
}

/* DISPLAY DE INFORMAÇÕES */
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

/* TIPO DO POKÉMON */
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

/* CAMPO DE BUSCA */
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

.search-input::placeholder {
    color: #00ff4166;
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

/* Efeito de partículas ou folhas caindo (opcional) */
@keyframes floatingLeaves {
    0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 0.3;
    }
    90% {
        opacity: 0.3;
    }
    100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

/* RESPONSIVIDADE */
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
}
</style>

</head>
<body>

<!-- Overlay escuro e desfoque já está no CSS via body::before -->

<!-- Logo do Pokémon no topo superior esquerdo -->
<!-- Na sua página da pokédex, altere a div logo-container -->
<div class="logo-container">
    <a href="{{ url('/historia-pokemon') }}" target="_blank" title="Clique para conhecer a história do Pokémon">
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

        <div class="screen">
            @if(isset($erro))
                <div style="color: #ff0000; text-align: center;">
                    <p>{{ $erro }}</p>
                </div>
            @else
                <img src="{{ $pokemon['sprites']['front_default'] }}" alt="{{ $pokemon['name'] }}">
            @endif
        </div>

        <div class="d-pad">
            @php
                $idAtual = isset($pokemon['id']) ? $pokemon['id'] : 1;
                $idAnterior = $idAtual - 1;
                $idProximo = $idAtual + 1;
                
                if ($idAnterior < 1) $idAnterior = 1025;
                if ($idProximo > 1025) $idProximo = 1;
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
                <p><strong>Nº {{ sprintf('%03d', $pokemon['id']) }}</strong> <span style="color: #ffeb3b;">{{ strtoupper($pokemon['name']) }}</span></p>
                <p><strong>ALTURA</strong> <span>{{ $pokemon['height'] / 10 }} m</span></p>
                <p><strong>PESO</strong> <span>{{ $pokemon['weight'] / 10 }} kg</span></p>
                <p><strong>TIPO</strong> 
                    <span>
                        @foreach($pokemon['types'] as $type)
                            @php
                                $typeName = $type['type']['name'];
                                $typeColors = [
                                    'normal' => '#A8A878',
                                    'fire' => '#F08030',
                                    'water' => '#6890F0',
                                    'electric' => '#F8D030',
                                    'grass' => '#78C850',
                                    'ice' => '#98D8D8',
                                    'fighting' => '#C03028',
                                    'poison' => '#A040A0',
                                    'ground' => '#E0C068',
                                    'flying' => '#A890F0',
                                    'psychic' => '#F85888',
                                    'bug' => '#A8B820',
                                    'rock' => '#B8A038',
                                    'ghost' => '#705898',
                                    'dragon' => '#7038F8',
                                    'dark' => '#705848',
                                    'steel' => '#B8B8D0',
                                    'fairy' => '#EE99AC'
                                ];
                                $bgColor = $typeColors[$typeName] ?? '#68A090';
                            @endphp
                            <span class="type-badge" style="background: {{ $bgColor }}">{{ strtoupper($typeName) }}</span>
                        @endforeach
                    </span>
                </p>
                <p><strong>HABILIDADE</strong> <span>{{ ucfirst($pokemon['abilities'][0]['ability']['name']) }}</span></p>
            @endif
        </div>

        <!-- Campo de busca por nome -->
        <form method="GET" action="{{ url('/pokemon') }}">
            <div class="search-box">
                <input type="text" name="pokemon" class="search-input" placeholder="🔍 Buscar Pokémon por nome..." autocomplete="off">
                <button type="submit" class="search-btn">🔍 BUSCAR POKÉMON</button>
            </div>
        </form>
        
        <div style="margin-top: 15px; text-align: center; font-size: 11px; color: rgba(255,255,255,0.7);">
            ← → Clique nas setas para navegar
        </div>
    </div>

</div>

</body>
</html>