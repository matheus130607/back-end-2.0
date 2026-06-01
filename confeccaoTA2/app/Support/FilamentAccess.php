<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Throwable;

class FilamentAccess
{
    public static function canAny(string|array $permissions): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Administrador', 'Admin'])) {
            return true;
        }

        $permissions = Arr::wrap($permissions);

        try {
            $permissionsExist = Permission::query()
                ->whereIn('name', $permissions)
                ->exists();
        } catch (Throwable) {
            return true;
        }

        if (! $permissionsExist) {
            return true;
        }

        return $user->canAny($permissions);
    }
}
