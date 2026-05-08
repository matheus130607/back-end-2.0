<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">
    <title>Meus Pokemon</title>
</head>
<body class="pokemon-page pokedex-app custom-pokemon-page">
@include('partials.menu-dropdown')

<main class="custom-list-shell">
    <section class="pokedex-hero">
        <div>
            <p class="pokedex-kicker">Pokemon personalizados</p>
            <h1>Meus Pokemon</h1>
            <p class="pokedex-lead">Gerencie os Pokemon criados por voce. Eles tambem aparecem na lista principal depois do ID #1025.</p>
        </div>
        <a href="{{ route('custom-pokemon.create') }}" class="pokedex-action pokedex-action--blue">Criar Pokemon</a>
    </section>

    @if(session('success'))
        <div class="pokedex-alert">{{ session('success') }}</div>
    @endif

    @if($customPokemons->isEmpty())
        <section class="list-feedback is-visible">
            <strong>Nenhum Pokemon criado ainda.</strong>
            <span>Crie seu primeiro Pokemon personalizado para ele aparecer aqui.</span>
            <a href="{{ route('custom-pokemon.create') }}" class="pokedex-action pokedex-action--blue">Criar Pokemon</a>
        </section>
    @else
        <section class="custom-management-grid">
            @foreach($customPokemons as $pokemon)
                @php
                    $types = $pokemon->type_list;
                    $primary = $types[0] ?? 'normal';
                    $secondary = $types[1] ?? $primary;
                @endphp
                <article
                    class="custom-management-card"
                    style="--primary-type-color: {{ $typeColors[$primary] ?? '#A8A77A' }}; --secondary-type-color: {{ $typeColors[$secondary] ?? '#273244' }}"
                >
                    <div class="custom-card-image">
                        @if($pokemon->public_image_url)
                            <img src="{{ $pokemon->public_image_url }}" alt="{{ $pokemon->name }}" loading="lazy">
                        @else
                            <div class="custom-image-placeholder">Sem imagem</div>
                        @endif
                    </div>
                    <div class="custom-card-body">
                        <div class="pokemon-card-top">
                            <span class="pokemon-id">#{{ str_pad((string) $pokemon->pokemon_id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="pokemon-gen">Custom</span>
                        </div>
                        <h2>{{ ucfirst($pokemon->name) }}</h2>
                        <div class="type-badge-row">
                            @foreach($types as $type)
                                <span class="pokedex-type-badge" style="--badge-color: {{ $typeColors[$type] ?? '#A8A77A' }}">{{ ucfirst($type) }}</span>
                            @endforeach
                        </div>
                        <div class="custom-card-meta">
                            <span>Altura: {{ number_format((float) $pokemon->height, 2, ',', '.') }} m</span>
                            <span>Peso: {{ number_format((float) $pokemon->weight, 2, ',', '.') }} kg</span>
                            <span>Habilidades: {{ implode(', ', $pokemon->ability_list) }}</span>
                        </div>
                        <p>{{ $pokemon->description ?: 'Descricao nao disponivel.' }}</p>
                    </div>
                    <div class="custom-card-actions">
                        <a href="{{ route('pokedex.detalhes', ['pokemon' => $pokemon->pokemon_id]) }}" class="pokedex-action pokedex-action--light">Ver</a>
                        <a href="{{ route('custom-pokemon.edit', ['pokemon_id' => $pokemon->pokemon_id]) }}" class="pokedex-action pokedex-action--blue">Editar</a>
                        <button
                            type="button"
                            class="pokedex-action custom-delete-trigger"
                            data-delete-url="{{ route('custom-pokemon.destroy', ['pokemon_id' => $pokemon->pokemon_id]) }}"
                            data-pokemon-name="{{ $pokemon->name }}"
                            data-pokemon-id="#{{ str_pad((string) $pokemon->pokemon_id, 4, '0', STR_PAD_LEFT) }}"
                            data-pokemon-image="{{ $pokemon->public_image_url }}"
                        >Excluir</button>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
</main>

<div class="delete-modal" id="deletePokemonModal" hidden>
    <div class="delete-modal__backdrop" data-delete-cancel></div>
    <section class="delete-modal__card" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="delete-modal__header">
            <h2 id="deleteModalTitle">Excluir Pokemon</h2>
            <button type="button" class="delete-modal__close" data-delete-cancel aria-label="Fechar">x</button>
        </div>
        <div class="delete-modal__body">
            <div class="delete-modal__image">
                <img id="deleteModalImage" src="" alt="" hidden>
                <span id="deleteModalPlaceholder">Sem imagem</span>
            </div>
            <div>
                <strong id="deleteModalPokemonName">Pokemon</strong>
                <span id="deleteModalPokemonId">#0000</span>
                <p>Tem certeza que deseja excluir este Pokemon? Essa acao nao podera ser desfeita.</p>
            </div>
        </div>
        <form id="deletePokemonForm" method="POST" action="" data-loading-message="Excluindo Pokemon">
            @csrf
            @method('DELETE')
            <div class="delete-modal__actions">
                <button type="button" class="pokedex-action pokedex-action--light" data-delete-cancel>Cancelar</button>
                <button type="submit" class="pokedex-action" id="deleteConfirmButton">Confirmar exclusao</button>
            </div>
        </form>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deletePokemonModal');
        const form = document.getElementById('deletePokemonForm');
        const image = document.getElementById('deleteModalImage');
        const placeholder = document.getElementById('deleteModalPlaceholder');
        const name = document.getElementById('deleteModalPokemonName');
        const number = document.getElementById('deleteModalPokemonId');
        const confirmButton = document.getElementById('deleteConfirmButton');

        function closeModal() {
            modal.hidden = true;
            form.action = '';
            confirmButton.disabled = false;
            confirmButton.textContent = 'Confirmar exclusao';
        }

        document.querySelectorAll('.custom-delete-trigger').forEach((button) => {
            button.addEventListener('click', function () {
                form.action = button.dataset.deleteUrl || '';
                name.textContent = button.dataset.pokemonName || 'Pokemon';
                number.textContent = button.dataset.pokemonId || '#0000';

                if (button.dataset.pokemonImage) {
                    image.src = button.dataset.pokemonImage;
                    image.alt = button.dataset.pokemonName || 'Pokemon';
                    image.hidden = false;
                    placeholder.hidden = true;
                } else {
                    image.hidden = true;
                    placeholder.hidden = false;
                }

                modal.hidden = false;
            });
        });

        document.querySelectorAll('[data-delete-cancel]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        form.addEventListener('submit', function () {
            confirmButton.disabled = true;
            confirmButton.textContent = 'Excluindo...';
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });
    });
</script>
</body>
</html>
