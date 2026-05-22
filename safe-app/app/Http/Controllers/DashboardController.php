<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'secretaria' => redirect()->route('secretaria.dashboard'),
            'professor' => redirect()->route('professor.dashboard'),
            'portaria' => redirect()->route('portaria.dashboard'),
            default => abort(403, 'Este usuario nao possui um painel operacional.'),
        };
    }
}
