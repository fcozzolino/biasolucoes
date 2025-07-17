<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não tem usuário autenticado, continua
        if (!$user) {
            return $next($request);
        }

        // Se não tem 2FA ativado, continua
        if (!$user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Se já verificou 2FA nesta sessão, continua
        if ($request->session()->get('2fa_verified')) {
            return $next($request);
        }

        // Se está tentando verificar 2FA, permite
        if ($request->routeIs('2fa.verify')) {
            return $next($request);
        }

        // Caso contrário, exige verificação 2FA
        return response()->json([
            'message' => 'Verificação de dois fatores necessária.',
            'requires_2fa' => true,
        ], 403);
    }
}
