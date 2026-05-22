<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class AuthorizationController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }
}
