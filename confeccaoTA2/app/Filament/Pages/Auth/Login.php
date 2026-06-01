<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected static bool $isDiscovered = false;

    private const TEST_PASSWORD = 'password';

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE),
                $this->getFormContentComponent(),
                $this->getTestLoginShortcutsComponent(),
                $this->getMultiFactorChallengeFormContentComponent(),
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER),
            ]);
    }

    public function fillTestLogin(string $profile): void
    {
        if (! $this->shouldShowTestLoginShortcuts()) {
            return;
        }

        $shortcut = collect($this->getTestLoginShortcuts())
            ->firstWhere('key', $profile);

        if (! $shortcut) {
            return;
        }

        $this->data = [
            ...($this->data ?? []),
            'email' => $shortcut['email'],
            'password' => self::TEST_PASSWORD,
            'remember' => false,
        ];

        $this->form->fill($this->data);
    }

    protected function getTestLoginShortcutsComponent(): Component
    {
        return Html::make(fn (): HtmlString => new HtmlString($this->renderTestLoginShortcuts()))
            ->visible(fn (): bool => $this->shouldShowTestLoginShortcuts() && blank($this->userUndertakingMultiFactorAuthentication));
    }

    protected function shouldShowTestLoginShortcuts(): bool
    {
        return app()->environment('local') && (bool) config('app.debug');
    }

    protected function getTestLoginShortcuts(): array
    {
        return [
            [
                'key' => 'administrador',
                'label' => 'Administrador',
                'email' => 'admin@sistema.com',
                'icon' => 'AD',
                'accent' => '#6d28d9',
            ],
            [
                'key' => 'cliente',
                'label' => 'Cliente',
                'email' => 'cliente@sistema.com',
                'icon' => 'CL',
                'accent' => '#2563eb',
            ],
            [
                'key' => 'fornecedor',
                'label' => 'Fornecedor',
                'email' => 'fornecedor@sistema.com',
                'icon' => 'FN',
                'accent' => '#059669',
            ],
        ];
    }

    protected function renderTestLoginShortcuts(): string
    {
        $buttons = collect($this->getTestLoginShortcuts())
            ->map(function (array $shortcut): string {
                $key = e($shortcut['key']);
                $label = e($shortcut['label']);
                $email = e($shortcut['email']);
                $icon = e($shortcut['icon']);
                $accent = e($shortcut['accent']);

                return <<<HTML
                    <button
                        type="button"
                        wire:click="fillTestLogin('{$key}')"
                        wire:loading.attr="disabled"
                        style="display: flex; width: 100%; align-items: center; gap: 12px; border: 1px solid #e4e4e7; border-radius: 8px; background: #fafafa; padding: 12px; text-align: left; cursor: pointer;"
                    >
                        <span style="display: inline-flex; width: 36px; height: 36px; flex: 0 0 36px; align-items: center; justify-content: center; border-radius: 8px; background: {$accent}; color: #ffffff; font-size: 12px; font-weight: 800;">
                            {$icon}
                        </span>

                        <span style="display: grid; gap: 2px; min-width: 0;">
                            <span style="color: #27272a; font-size: 14px; font-weight: 700;">Entrar como {$label}</span>
                            <span style="color: #71717a; font-size: 12px; overflow-wrap: anywhere;">{$email}</span>
                        </span>
                    </button>
                HTML;
            })
            ->implode('');

        return <<<HTML
            <section style="margin-top: 18px; border: 1px solid #e9d5ff; border-radius: 8px; background: #ffffff; padding: 16px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);">
                <div style="margin-bottom: 12px;">
                    <h2 style="margin: 0; color: #18181b; font-size: 15px; font-weight: 700;">Acessos rápidos para teste</h2>
                    <p style="margin: 4px 0 0; color: #71717a; font-size: 13px; line-height: 1.45;">Clique em um perfil para preencher o login automaticamente.</p>
                </div>

                <div style="display: grid; gap: 10px;">
                    {$buttons}
                </div>
            </section>
        HTML;
    }
}
