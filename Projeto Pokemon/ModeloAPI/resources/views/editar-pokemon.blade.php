{{-- resources/views/editar-pokemon.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Editar Pokémon - {{ $customPokemon->name }}</title>
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
            min-height: 100vh;
            padding: 40px 20px;
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
            max-width: 900px;
            margin: 0 auto;
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
            width: 50%;
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
            width: 20px;
            height: 20px;
            border-radius: 50%;
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
            margin-bottom: 20px;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .screen img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #ffd700;
        }

        .preview-name {
            color: #ffeb3b;
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .preview-types {
            margin-top: 10px;
        }

        .type-badge {
            display: inline-block;
            padding: 4px 12px;
            margin: 3px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }

        .preview-stats {
            margin-top: 10px;
            font-size: 0.8rem;
            color: #00ff41;
        }

        .right {
            padding: 25px;
            width: 50%;
            background: #d62e2e;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: white;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            border: none;
            border-radius: 15px;
            background: rgba(255,255,255,0.95);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        input:focus, select:focus {
            outline: none;
            transform: scale(1.02);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .types-container {
            background: rgba(0,0,0,0.2);
            border-radius: 15px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
        }

        .type-checkboxes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .type-checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 8px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .type-checkbox input {
            width: auto;
            margin: 0;
        }

        .type-checkbox label {
            margin: 0;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .type-checkbox.selected {
            background: rgba(255,255,255,0.2);
        }

        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn-submit, .btn-back {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-family: 'Courier New', monospace;
            transition: all 0.3s ease;
        }

        .btn-submit {
            background: #4CAF50;
            color: white;
        }

        .btn-back {
            background: #666;
            color: white;
        }

        .btn-submit:hover, .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .current-image {
            text-align: center;
            margin-bottom: 15px;
        }

        .current-image img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #ffd700;
        }

        @media (max-width: 768px) {
            .pokedex {
                flex-direction: column;
                width: 100%;
                max-width: 400px;
                margin-top: 60px;
            }
            .pokedex::before {
                display: none;
            }
            .left, .right {
                width: 100%;
            }
            .pokemon-logo {
                width: 200px;
            }
        }
    </style>
</head>
<body>

<div class="logo-container">
    <a href="{{ route('pokedex.lista') }}">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/International_Pok%C3%A9mon_logo.svg" 
             alt="Pokémon Logo" 
             class="pokemon-logo">
    </a>
</div>

<div class="pokedex">
    <!-- LADO ESQUERDO - PREVIEW -->
    <div class="left">
        <div class="top-panel">
            <div class="led-lights">
                <div class="led led-red"></div>
                <div class="led led-blue"></div>
                <div class="led led-green"></div>
            </div>
            <div style="color: white; font-size: 11px; font-weight: bold;">PREVIEW</div>
        </div>

        <div class="screen">
            <img id="previewImage" src="{{ $customPokemon->image_path ? asset('storage/' . $customPokemon->image_path) : 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png' }}" alt="Preview">
            <div class="preview-name" id="previewName">{{ $customPokemon->name }}</div>
            <div class="preview-types" id="previewTypes"></div>
            <div class="preview-stats">
                <div id="previewHeight">📏 {{ $customPokemon->height ?? 1.0 }} m</div>
                <div id="previewWeight">⚖️ {{ $customPokemon->weight ?? 10.0 }} kg</div>
                <div id="previewAbility" style="color: #00ff41; margin-top: 5px;">⭐ {{ $customPokemon->ability ?? 'Habilidade Especial' }}</div>
            </div>
        </div>
    </div>

    <!-- LADO DIREITO - FORMULÁRIO -->
    <div class="right">
        <form action="{{ route('custom-pokemon.update', ['pokemon_id' => $customPokemon->pokemon_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="current-image">
                <img src="{{ $customPokemon->image_path ? asset('storage/' . $customPokemon->image_path) : 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png' }}" alt="Atual">
            </div>

            <div class="form-group">
                <label>🎯 NOME</label>
                <input type="text" name="name" required maxlength="100" id="nameInput" value="{{ $customPokemon->name }}">
            </div>

            <div class="form-group">
                <label>⚡ TIPOS (MAX 2)</label>
                <div class="types-container">
                    <div class="type-checkboxes" id="typesContainer">
                        @php
                            $tipos = [
                                'normal' => '#A8A878', 'fire' => '#F08030', 'water' => '#6890F0',
                                'electric' => '#F8D030', 'grass' => '#78C850', 'ice' => '#98D8D8',
                                'fighting' => '#C03028', 'poison' => '#A040A0', 'ground' => '#E0C068',
                                'flying' => '#A890F0', 'psychic' => '#F85888', 'bug' => '#A8B820',
                                'rock' => '#B8A038', 'ghost' => '#705898', 'dragon' => '#7038F8',
                                'dark' => '#705848', 'steel' => '#B8B8D0', 'fairy' => '#EE99AC'
                            ];
                            $selectedTypes = is_array($customPokemon->types)
                                ? $customPokemon->types
                                : (json_decode($customPokemon->types, true) ?? [$customPokemon->type]);
                        @endphp
                        @foreach($tipos as $tipo => $cor)
                            <div class="type-checkbox" style="background: {{ $cor }}20;">
                                <input type="checkbox" name="types[]" value="{{ $tipo }}" id="type_{{ $tipo }}" {{ in_array($tipo, $selectedTypes) ? 'checked' : '' }}>
                                <label for="type_{{ $tipo }}">{{ ucfirst($tipo) }}</label>
                            </div>
                        @endforeach
                    </div>
                    <small class="error" id="typeError" style="display: none;">Max 2 tipos!</small>
                </div>
            </div>

            <div class="stats-row">
                <div class="form-group">
                    <label>📏 ALTURA (m)</label>
                    <input type="number" name="height" step="0.1" min="0.1" max="100" value="{{ $customPokemon->height ?? 1.0 }}" id="heightInput">
                </div>
                <div class="form-group">
                    <label>⚖️ PESO (kg)</label>
                    <input type="number" name="weight" step="0.1" min="0.1" max="9999" value="{{ $customPokemon->weight ?? 10.0 }}" id="weightInput">
                </div>
            </div>

            <div class="form-group">
                <label>⭐ HABILIDADE</label>
                <input type="text" name="ability" maxlength="100" value="{{ $customPokemon->ability ?? 'Habilidade Especial' }}" id="abilityInput">
            </div>

            <div class="form-group">
                <label>🖼️ NOVA IMAGEM</label>
                <input type="file" name="image" accept="image/*" id="imageInput">
                <small class="error">Deixe em branco para manter a atual</small>
            </div>

            <div class="button-group">
                <a href="{{ route('custom-pokemon.index') }}" class="btn-back">← VOLTAR</a>
                <button type="submit" class="btn-submit">💾 SALVAR</button>
            </div>
        </form>
    </div>
</div>

<script>
    const typeColors = {
        normal: '#A8A878', fire: '#F08030', water: '#6890F0',
        electric: '#F8D030', grass: '#78C850', ice: '#98D8D8',
        fighting: '#C03028', poison: '#A040A0', ground: '#E0C068',
        flying: '#A890F0', psychic: '#F85888', bug: '#A8B820',
        rock: '#B8A038', ghost: '#705898', dragon: '#7038F8',
        dark: '#705848', steel: '#B8B8D0', fairy: '#EE99AC'
    };

    const previewTypes = document.getElementById('previewTypes');
    const previewName = document.getElementById('previewName');
    const previewImage = document.getElementById('previewImage');
    const previewHeight = document.getElementById('previewHeight');
    const previewWeight = document.getElementById('previewWeight');
    const previewAbility = document.getElementById('previewAbility');

    const nameInput = document.getElementById('nameInput');
    const heightInput = document.getElementById('heightInput');
    const weightInput = document.getElementById('weightInput');
    const abilityInput = document.getElementById('abilityInput');
    const imageInput = document.getElementById('imageInput');

    function updatePreview() {
        const selected = document.querySelectorAll('input[name="types[]"]:checked');
        previewTypes.innerHTML = '';
        selected.forEach(type => {
            const span = document.createElement('span');
            span.className = 'type-badge';
            span.style.background = typeColors[type.value];
            span.textContent = type.value.toUpperCase();
            previewTypes.appendChild(span);
        });
    }

    nameInput?.addEventListener('input', () => previewName.textContent = nameInput.value || '?');
    heightInput?.addEventListener('input', () => previewHeight.innerHTML = `📏 ${heightInput.value || 1.0} m`);
    weightInput?.addEventListener('input', () => previewWeight.innerHTML = `⚖️ ${weightInput.value || 10.0} kg`);
    abilityInput?.addEventListener('input', () => previewAbility.innerHTML = `⭐ ${abilityInput.value || 'Habilidade'}`);
    
    imageInput?.addEventListener('change', (e) => {
        if (e.target.files?.[0]) {
            const reader = new FileReader();
            reader.onload = (loadEvent) => previewImage.src = loadEvent.target.result;
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    const checkboxes = document.querySelectorAll('input[name="types[]"]');
    const typeError = document.getElementById('typeError');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checked = document.querySelectorAll('input[name="types[]"]:checked');
            if (checked.length > 2) {
                this.checked = false;
                typeError.style.display = 'block';
            } else {
                typeError.style.display = 'none';
            }
            updatePreview();
            
            document.querySelectorAll('.type-checkbox').forEach(div => {
                const input = div.querySelector('input');
                if (input?.checked) div.classList.add('selected');
                else div.classList.remove('selected');
            });
        });
    });

    updatePreview();
</script>
</body>
</html>
