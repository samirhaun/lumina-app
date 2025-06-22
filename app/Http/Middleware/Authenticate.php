<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Se não estiver autenticado, para onde redirecionar.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Se for rota /admin ou /admin/*
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        }

        // Se depois quiser ter um login público, basta criar essa rota:
        // return route('login');

        return null;
    }
}
