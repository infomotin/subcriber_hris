<?php

namespace App\Http\Middleware;

use App\Models\GatewayConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorChallenge
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $config = GatewayConfig::find(1);

        if ($config && $config->two_factor_enabled && $user && $user->hasConfirmedTwoFactor()) {
            if (!$request->session()->get('two_factor_verified')) {
                return redirect()->route('two-factor.challenge');
            }
        }

        return $next($request);
    }
}
