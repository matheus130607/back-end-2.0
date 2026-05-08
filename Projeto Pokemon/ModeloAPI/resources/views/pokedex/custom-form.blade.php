@php
    $isEdit = $mode === 'edit';
    $selectedTypes = old('types', $pokemon ? $pokemon->type_list : ['normal']);
    $abilities = old('abilities', $pokemon ? implode(', ', $pokemon->ability_list) : '');
    $imageUrl = $pokemon ? $pokemon->public_image_url : null;
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <title>{{ $isEdit ? 'Editar Pokemon' : 'Criar Pokemon' }}</title>
</head>
<body class="pokemon-page pokedex-app custom-pokemon-page">
@include('partials.menu-dropdown')

<main class="custom-form-shell">
    <section class="detail-topbar">
        <div>
            <p class="pokedex-kicker">{{ $isEdit ? 'Meus Pokemon' : 'Novo registro' }}</p>
            <h1>{{ $isEdit ? 'Editar Pokemon' : 'Criar Pokemon' }}</h1>
        </div>
        <span class="detail-number">{{ '#' . str_pad((string) $nextPokemonId, 4, '0', STR_PAD_LEFT) }}</span>
    </section>

    @if(session('success'))
        <div class="pokedex-alert">{{ session('success') }}</div>
    @endif

    <section class="custom-form-layout">
        <aside class="custom-preview-panel">
            <article
                class="pokemon-dex-card custom-preview-card"
                id="customPreviewCard"
                style="--primary-type-color: {{ $typeColors[$selectedTypes[0] ?? 'normal'] ?? '#A8A77A' }}; --secondary-type-color: {{ $typeColors[$selectedTypes[1] ?? ($selectedTypes[0] ?? 'normal')] ?? '#273244' }}"
            >
                <div class="pokemon-card-top">
                    <span class="pokemon-id">{{ '#' . str_pad((string) $nextPokemonId, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="pokemon-gen">Custom</span>
                </div>
                <div class="pokemon-image-wrap">
                    <img
                        id="customPreviewImage"
                        src="{{ $imageUrl ?: '' }}"
                        alt="Preview"
                        class="{{ $imageUrl ? '' : 'is-empty' }}"
                    >
                    <div class="custom-image-placeholder" id="customPreviewPlaceholder" {{ $imageUrl ? 'hidden' : '' }}>Sem imagem</div>
                </div>
                <strong class="pokemon-card-name" id="customPreviewName">{{ old('name', $pokemon->name ?? 'Nome do Pokemon') }}</strong>
                <div class="type-badge-row" id="customPreviewTypes"></div>
                <div class="custom-preview-meta">
                    <span id="customPreviewHeight">Altura: {{ old('height', $pokemon->height ?? '1.70') }} m</span>
                    <span id="customPreviewWeight">Peso: {{ old('weight', $pokemon->weight ?? '65.5') }} kg</span>
                    <span id="customPreviewAbilities">{{ $abilities ?: 'Habilidades' }}</span>
                    <p id="customPreviewDescription">{{ old('description', $pokemon->description ?? 'Descricao do Pokemon') }}</p>
                </div>
            </article>
        </aside>

        <form
            class="custom-form-panel"
            action="{{ $isEdit ? route('custom-pokemon.update', ['pokemon_id' => $pokemon->pokemon_id]) : route('custom-pokemon.store') }}"
            method="POST"
            enctype="multipart/form-data"
            data-loading-message="{{ $isEdit ? 'Atualizando Pokemon' : 'Criando Pokemon' }}"
        >
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="custom-form-grid">
                <label class="custom-field">
                    <span>ID automatico</span>
                    <input type="text" value="{{ '#' . str_pad((string) $nextPokemonId, 4, '0', STR_PAD_LEFT) }}" readonly>
                </label>

                <label class="custom-field">
                    <span>Nome do Pokemon</span>
                    <input id="customNameInput" type="text" name="name" value="{{ old('name', $pokemon->name ?? '') }}" required maxlength="100">
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>

                <div class="custom-field custom-field--full">
                    <span>Tipagens</span>
                    <div class="custom-type-grid" id="customTypeGrid">
                        @foreach($types as $type)
                            <label class="custom-type-option" style="--type-color: {{ $typeColors[$type] ?? '#A8A77A' }}">
                                <input type="checkbox" name="types[]" value="{{ $type }}" {{ in_array($type, $selectedTypes, true) ? 'checked' : '' }}>
                                <span>{{ ucfirst($type) }}</span>
                            </label>
                        @endforeach
                    </div>
                    <small id="customTypeLimit" hidden>Escolha no maximo dois tipos.</small>
                    @error('types') <small>{{ $message }}</small> @enderror
                    @error('types.*') <small>{{ $message }}</small> @enderror
                </div>

                <label class="custom-field">
                    <span>Altura em metros</span>
                    <input id="customHeightInput" type="number" name="height" step="0.01" min="0.1" max="100" value="{{ old('height', $pokemon->height ?? '') }}" required>
                    @error('height') <small>{{ $message }}</small> @enderror
                </label>

                <label class="custom-field">
                    <span>Peso em kg</span>
                    <input id="customWeightInput" type="number" name="weight" step="0.01" min="0.1" max="9999" value="{{ old('weight', $pokemon->weight ?? '') }}" required>
                    @error('weight') <small>{{ $message }}</small> @enderror
                </label>

                <label class="custom-field custom-field--full">
                    <span>Habilidades separadas por virgula</span>
                    <input id="customAbilitiesInput" type="text" name="abilities" value="{{ $abilities }}" required>
                    @error('abilities') <small>{{ $message }}</small> @enderror
                </label>

                <label class="custom-field custom-field--full">
                    <span>Descricao</span>
                    <textarea id="customDescriptionInput" name="description" required maxlength="1000">{{ old('description', $pokemon->description ?? '') }}</textarea>
                    @error('description') <small>{{ $message }}</small> @enderror
                </label>

                <label class="custom-field custom-field--full">
                    <span>Imagem</span>
                    <input id="customImageInput" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
                    <small>JPG, JPEG, PNG, WEBP ou GIF. Maximo 5MB.</small>
                    @error('image') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="custom-form-actions">
                <a href="{{ route('custom-pokemon.index') }}" class="pokedex-action pokedex-action--light">Voltar</a>
                <button type="submit" class="pokedex-action pokedex-action--blue">{{ $isEdit ? 'Salvar alteracoes' : 'Criar Pokemon' }}</button>
            </div>
        </form>
    </section>
</main>

<script>
    window.POKEDEX_CONFIG = {
        page: 'customForm',
        typeColors: @json($typeColors),
        typeNames: @json($types)
    };
</script>
<script src="{{ asset('js/pokedex.js') }}" defer></script>
</body>
</html>
