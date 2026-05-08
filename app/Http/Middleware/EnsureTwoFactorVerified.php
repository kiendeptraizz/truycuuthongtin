<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\TwoFactorAuthController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sau khi user login (auth check), nếu user có 2FA enabled mà chưa verify
 * trong session thì redirect tới /2fa/challenge.
 *
 * Trusted device shortcut: nếu cookie 2fa_trusted_device hợp lệ (đã verify
 * trong 30 ngày qua, fingerprint match two_factor_secret hiện tại) → tự
 * mark session passed, không cần show challenge form. Cookie này được set
 * khi user check "Tin tưởng thiết bị này" lúc verify 2FA — tránh phải
 * nhập code mỗi sáng (session admin chỉ giữ 8h).
 *
 * Apply lên admin routes qua bootstrap/app.php alias.
 */
class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->hasTwoFactorEnabled() && !$request->session()->get('2fa.passed')) {
            // Trusted device cookie: nếu hợp lệ → mark session passed + tiếp tục
            if (TwoFactorAuthController::isTrustedDevice($request, $user)) {
                $request->session()->put('2fa.passed', true);
                Log::info('2FA passed via trusted device cookie', [
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                ]);
                return $next($request);
            }

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
