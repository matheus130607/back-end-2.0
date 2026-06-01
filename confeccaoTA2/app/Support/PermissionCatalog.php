<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;

class PermissionCatalog
{
    public const DEFINITIONS = [
        'Dashboard' => [
            'dashboard.visualizar' => ['label' => 'Visualizar dashboard', 'description' => 'Acessar os indicadores e pedidos recentes.'],
        ],
        'Cadastros Gerais' => [
            'clientes.gerenciar' => ['label' => 'Gerenciar clientes', 'description' => 'Criar, editar, visualizar e excluir clientes.'],
            'fornecedores.gerenciar' => ['label' => 'Gerenciar fornecedores', 'description' => 'Criar, editar, visualizar e excluir fornecedores.'],
            'usuarios.gerenciar' => ['label' => 'Gerenciar usuários', 'description' => 'Criar e alterar usuários do painel.'],
        ],
        'Vendas' => [
            'pedidos.gerenciar' => ['label' => 'Gerenciar pedidos', 'description' => 'Criar, editar, visualizar e excluir pedidos.'],
            'pedidos.criar' => ['label' => 'Criar pedidos', 'description' => 'Cadastrar novos pedidos.'],
            'pedidos.editar' => ['label' => 'Editar pedidos', 'description' => 'Alterar pedidos existentes.'],
            'caixa_email.visualizar' => ['label' => 'Visualizar caixa de e-mail', 'description' => 'Consultar e-mails de pedidos finalizados.'],
        ],
        'Estoques' => [
            'produtos.gerenciar' => ['label' => 'Gerenciar produtos', 'description' => 'Criar, editar, visualizar e excluir produtos.'],
            'insumos.gerenciar' => ['label' => 'Gerenciar insumos', 'description' => 'Criar, editar, visualizar e excluir insumos.'],
            'estoque.gerenciar' => ['label' => 'Gerenciar estoque', 'description' => 'Registrar e consultar movimentações de estoque.'],
            'estoque.visualizar' => ['label' => 'Visualizar estoque', 'description' => 'Consultar produtos, insumos e movimentações.'],
        ],
        'Permissões' => [
            'cargos.gerenciar' => ['label' => 'Gerenciar cargos', 'description' => 'Criar e alterar cargos do sistema.'],
            'permissoes.gerenciar' => ['label' => 'Gerenciar permissões', 'description' => 'Criar e alterar permissões críticas.'],
        ],
    ];

    public const ROLE_PERMISSIONS = [
        'Administrador' => [
            'dashboard.visualizar',
            'clientes.gerenciar',
            'fornecedores.gerenciar',
            'usuarios.gerenciar',
            'pedidos.gerenciar',
            'pedidos.criar',
            'pedidos.editar',
            'caixa_email.visualizar',
            'produtos.gerenciar',
            'insumos.gerenciar',
            'estoque.gerenciar',
            'estoque.visualizar',
            'cargos.gerenciar',
            'permissoes.gerenciar',
        ],
        'Gerente' => [
            'dashboard.visualizar',
            'clientes.gerenciar',
            'pedidos.gerenciar',
            'pedidos.criar',
            'pedidos.editar',
            'caixa_email.visualizar',
            'produtos.gerenciar',
            'estoque.visualizar',
        ],
        'Vendedor' => [
            'dashboard.visualizar',
            'clientes.gerenciar',
            'pedidos.criar',
            'pedidos.editar',
            'produtos.gerenciar',
            'estoque.visualizar',
        ],
        'Usuário comum' => [
            'dashboard.visualizar',
        ],
        'Cliente' => [
            'dashboard.visualizar',
        ],
        'Fornecedor' => [
            'dashboard.visualizar',
        ],
    ];

    public static function moduleOptions(): array
    {
        return collect(array_keys(self::DEFINITIONS))
            ->mapWithKeys(fn (string $module): array => [$module => $module])
            ->all();
    }

    public static function flatOptions(): array
    {
        return collect(self::DEFINITIONS)
            ->flatMap(fn (array $permissions): array => collect($permissions)
                ->mapWithKeys(fn (array $definition, string $name): array => [$name => $definition['label']])
                ->all())
            ->all();
    }

    public static function groupedPermissionOptions(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission): string => self::moduleFor($permission->name))
            ->map(fn ($permissions): array => $permissions
                ->mapWithKeys(fn (Permission $permission): array => [
                    $permission->id => self::labelFor($permission->name),
                ])
                ->all())
            ->sortKeys()
            ->all();
    }

    public static function allPermissionNames(): array
    {
        return array_keys(self::flatOptions());
    }

    public static function labelFor(?string $permission): string
    {
        if (! $permission) {
            return 'Sem permissão';
        }

        foreach (self::DEFINITIONS as $permissions) {
            if (isset($permissions[$permission])) {
                return $permissions[$permission]['label'];
            }
        }

        return str($permission)->replace(['.', '_'], ' ')->headline()->toString();
    }

    public static function descriptionFor(?string $permission): ?string
    {
        if (! $permission) {
            return null;
        }

        foreach (self::DEFINITIONS as $permissions) {
            if (isset($permissions[$permission])) {
                return $permissions[$permission]['description'];
            }
        }

        return null;
    }

    public static function moduleFor(?string $permission): string
    {
        if (! $permission) {
            return 'Outras';
        }

        foreach (self::DEFINITIONS as $module => $permissions) {
            if (isset($permissions[$permission])) {
                return $module;
            }
        }

        return 'Outras';
    }
}
