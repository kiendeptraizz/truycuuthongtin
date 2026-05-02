<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sau khi user login (auth check), nếu user có 2FA enabled mà chưa verify
 * trong session thì redirect tới /2fa/challenge.
 *
 * Apply lên admin routes qua bootstrap/app.php alias.
 */
class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->hasTwoFactorEnabled() && !$request->session()->get('2fa.passed')) {
            // Tránh loop khi đang ở chính các route 2FA
            $allowed = ['two-factor.challenge', 'two-factor.verify', 'logout'];
            $currentRoute = $request->route()?->getName();
            if (!in_array($currentRoute, $allowed, true)) {
                return redirect()->route('two-factor.challenge');
            }
        }
        return $next($request);
    }
}
