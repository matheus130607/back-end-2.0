(function () {
    const loader = document.getElementById('pokedexLoader');

    window.showPokedexLoader = function (message) {
        if (!loader) return;

        const label = loader.querySelector('[data-loader-label]');
        if (label && message) {
            label.textContent = message;
        }

        loader.classList.add('is-active');
    };

    window.hidePokedexLoader = function () {
        if (!loader) return;
        loader.classList.remove('is-active');
    };

    window.addEventListener('pageshow', window.hidePokedexLoader);

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.dataset.noLoader === 'true') return;

        const submitButton = form.querySelector('[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.dataset.originalText = submitButton.textContent;
            submitButton.textContent = submitButton.dataset.loadingText || 'Processando...';
        }

        window.showPokedexLoader(form.dataset.loadingMessage || 'Carregando dados');
    }, true);

    document.addEventListener('click', function (event) {
        const link = event.target.closest('a');
        if (!link) return;
        if (link.target || link.hasAttribute('download')) return;
        if (link.dataset.noLoader === 'true') return;
        if (!link.href || link.href.startsWith('#') || link.href.startsWith('javascript:')) return;
        if (new URL(link.href, window.location.href).origin !== window.location.origin) return;

        window.showPokedexLoader(link.dataset.loadingMessage || 'Abrindo');
    });
})();
