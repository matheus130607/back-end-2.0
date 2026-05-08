@php
    use App\Support\PokemonTypes;

    $types = PokemonTypes::TYPES;
    $colors = PokemonTypes::colors();
    $labels = PokemonTypes::labels();
    $backgroundUrl = PokemonTypes::typeBackgroundUrl(['normal']);
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Criar Pokemon</title>
    <style>
        body {
            margin: 0;
            font-family: "Trebuchet MS", Arial, sans-serif;
        }

        .create-page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 120px 20px 48px;
        }

        .create-shell {
            width: min(1040px, 100%);
            display: grid;
            grid-template-columns: 0.9fr 1fr;
            overflow: hidden;
        }

        .preview-side,
        .form-side {
            padding: 28px;
        }

        .preview-screen {
            min-height: 520px;
            display: grid;
            place-items: center;
            padding: 22px;
            background-image: var(--preview-bg);
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .preview-screen::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(14, 61, 52, 0.1), rgba(14, 61, 52, 0.72));
        }

        .preview-card {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .preview-card img {
            width: min(270px, 75vw);
            height: 270px;
            object-fit: contain;
        }

        .preview-name {
            margin-top: 10px;
            color: #fff7c7;
            font-size: 1.7rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .preview-types {
            margin-top: 12px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .preview-meta {
            margin-top: 16px;
            color: #9df5d0;
            font: 800 0.95rem "Courier New", monospace;
            line-height: 1.7;
        }

        .form-title {
            margin: 0 0 8px;
            color: #fff7c7;
            font-size: 2rem;
        }

        .form-copy {
            margin: 0 0 20px;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.5;
        }

        .form-grid {
            display: grid;
            gap: 15px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #ffdf6c;
            font: 900 0.78rem "Courier New", monospace;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            min-height: 42px;
            border: 2px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            padding: 0 12px;
            font: 700 0.95rem "Courier New", monospace;
            color: #172033;
            background: rgba(255, 255, 255, 0.92);
        }

        .type-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            max-height: 220px;
            overflow: auto;
            padding: 10px;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.18);
        }

        .type-option {
            display: flex;
            align-items: center;
            gap: 7px;
            min-height: 34px;
            padding: 0 9px;
            border-radius: 999px;
            color: white;
            font-size: 0.78rem;
            font-weight: 900;
            cursor: pointer;
            user-select: none;
        }

        .type-option input {
            width: auto;
            min-height: auto;
            accent-color: #ffdf6c;
        }

        .form-note,
        .error {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.82rem;
            line-height: 1.45;
        }

        .error {
            color: #ffdf6c;
        }

        .button-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 8px;
        }

        @media (max-width: 900px) {
            .create-page {
                padding-top: 92px;
            }

            .create-shell,
            .field-row,
            .button-row {
                grid-template-columns: 1fr;
            }

            .preview-screen {
                min-height: 360px;
            }

            .type-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body class="pokemon-page" style="--type-bg: url('{{ $backgroundUrl }}');">

@include('partials.menu-dropdown')

<main class="create-page">
    <section class="create-shell pokedex-shell">
        <div class="preview-side">
            <div class="preview-screen pokedex-screen" id="previewScreen" style="--preview-bg: url('{{ $backgroundUrl }}');">
                <div class="preview-card">
                    <img id="previewImage" class="pokemon-image-shadow" src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Preview">
                    <div class="preview-name" id="previewName">Novo Pokemon</div>
                    <div class="preview-types" id="previewTypes"></div>
                    <div class="preview-meta">
                        <div id="previewHeight">Altura: 1.0 m</div>
                        <div id="previewWeight">Peso: 10.0 kg</div>
                        <div id="previewAbility">Habilidade: Habilidade Especial</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-side">
            <h1 class="form-title">Criar Pokemon</h1>
            <p class="form-copy">Envie uma imagem para tentar remover o fundo automaticamente. Se nenhuma imagem for enviada, o sistema tenta gerar uma arte e usa um fallback local se a API estiver indisponivel.</p>

            @if($errors->any())
                <div class="ui-panel" style="padding: 12px; margin-bottom: 14px;">
                    @foreach($errors->all() as $error)
                        <div class="error">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('custom-pokemon.store') }}" method="POST" enctype="multipart/form-data" data-loading-message="Criando Pokemon">
                @csrf

                <div class="form-grid">
                    <div class="field-row">
                        <div class="form-group">
                            <label for="idInput">ID</label>
                            <input type="number" name="pokemon_id" required min="1026" max="9999" id="idInput" value="{{ old('pokemon_id') }}" placeholder="1026">
                        </div>
                        <div class="form-group">
                            <label for="nameInput">Nome</label>
                            <input type="text" name="name" required maxlength="100" id="nameInput" value="{{ old('name') }}" placeholder="Dragomaster">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipos (maximo 2)</label>
                        <div class="type-grid" id="typesContainer">
                            @foreach($types as $type)
                                <label class="type-option" style="background: {{ $colors[$type] }}">
                                    <input type="checkbox" name="types[]" value="{{ $type }}" {{ in_array($type, old('types', []), true) ? 'checked' : '' }}>
                                    <span>{{ $labels[$type] }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="error" id="typeError" style="display: none;">Escolha no maximo 2 tipos.</div>
                    </div>

                    <div class="field-row">
                        <div class="form-group">
                            <label for="heightInput">Altura (m)</label>
                            <input type="number" name="height" step="0.1" min="0.1" max="100" value="{{ old('height', '1.0') }}" id="heightInput">
                        </div>
                        <div class="form-group">
                            <label for="weightInput">Peso (kg)</label>
                            <input type="number" name="weight" step="0.1" min="0.1" max="9999" value="{{ old('weight', '10.0') }}" id="weightInput">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="abilityInput">Habilidade</label>
                        <input type="text" name="ability" maxlength="100" value="{{ old('ability', 'Habilidade Especial') }}" id="abilityInput">
                    </div>

                    <div class="form-group">
                        <label for="imageInput">Imagem opcional</label>
                        <input type="file" name="image" accept="image/*" id="imageInput">
                        <div class="form-note">JPG, PNG, GIF ou WebP. Maximo 4MB.</div>
                    </div>

                    <div class="button-row">
                        <a href="{{ route('pokedex.lista') }}" class="ui-button ui-button--dark">Voltar</a>
                        <button type="submit" class="ui-button ui-button--yellow">Criar Pokemon</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>

<script>
    const typeColors = @json($colors);
    const typeLabels = @json($labels);
    const nameInput = document.getElementById('nameInput');
    const heightInput = document.getElementById('heightInput');
    const weightInput = document.getElementById('weightInput');
    const abilityInput = document.getElementById('abilityInput');
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const previewName = document.getElementById('previewName');
    const previewTypes = document.getElementById('previewTypes');
    const previewHeight = document.getElementById('previewHeight');
    const previewWeight = document.getElementById('previewWeight');
    const previewAbility = document.getElementById('previewAbility');
    const previewScreen = document.getElementById('previewScreen');
    const typeError = document.getElementById('typeError');

    function selectedTypes() {
        return Array.from(document.querySelectorAll('input[name="types[]"]:checked')).map(input => input.value);
    }

    function renderTypes() {
        const types = selectedTypes();
        previewTypes.innerHTML = types.map(type => `<span class="type-badge" style="background:${typeColors[type]}">${typeLabels[type]}</span>`).join('');
        const bgTypes = types.length ? types : ['normal'];
        previewScreen.style.setProperty('--preview-bg', `url('/pokedex/type-background.svg?types=${encodeURIComponent(bgTypes.join(','))}')`);
        document.body.style.setProperty('--type-bg', `url('/pokedex/type-background.svg?types=${encodeURIComponent(bgTypes.join(','))}')`);
    }

    nameInput.addEventListener('input', () => previewName.textContent = nameInput.value || 'Novo Pokemon');
    heightInput.addEventListener('input', () => previewHeight.textContent = `Altura: ${heightInput.value || '1.0'} m`);
    weightInput.addEventListener('input', () => previewWeight.textContent = `Peso: ${weightInput.value || '10.0'} kg`);
    abilityInput.addEventListener('input', () => previewAbility.textContent = `Habilidade: ${abilityInput.value || 'Habilidade Especial'}`);

    imageInput.addEventListener('change', event => {
        const file = event.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = loadEvent => previewImage.src = loadEvent.target.result;
        reader.readAsDataURL(file);
    });

    document.querySelectorAll('input[name="types[]"]').forEach(input => {
        input.addEventListener('change', function () {
            const checked = selectedTypes();
            if (checked.length > 2) {
                this.checked = false;
                typeError.style.display = 'block';
            } else {
                typeError.style.display = 'none';
            }
            renderTypes();
        });
    });

    renderTypes();
</script>

</body>
</html>
