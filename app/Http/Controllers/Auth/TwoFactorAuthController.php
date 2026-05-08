<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * 2FA TOTP cho admin login. Optional — admin tự enable trong settings.
 *
 * Flow:
 *   - GET /2fa/setup — sinh secret tạm + QR. User scan vào Google Authenticator.
 *   - POST /2fa/enable — verify code 6-digit lần đầu → save secret + sinh recovery codes.
 *   - POST /2fa/disable — yêu cầu password để tắt.
 *   - GET /2fa/challenge — sau khi login (password OK) nhưng chưa verify code.
 *   - POST /2fa/verify — verify code 6-digit (hoặc recovery code) → set session 2fa.passed.
 */
class TwoFactorAuthController extends Controller
{
    /**
     * Cookie name + thời hạn cho "Tin tưởng thiết bị này" — sau verify 2FA
     * thành công, browser được flag trong 30 ngày để khỏi phải nhập code mỗi
     * sáng (session admin chỉ giữ 8h theo session.lifetime). Cookie được
     * Laravel encrypt mặc định qua EncryptCookies middleware.
     *
     * Verify cookie: payload chứa fingerprint của two_factor_secret hiện tại
     * → khi user disable + re-enable 2FA (secret đổi), cookie cũ tự invalid
     * → buộc verify lại trên các thiết bị khác. Pattern này tương tự GitHub
     * "trusted devices" / Google "Don't ask again on this device".
     */
    public const TRUSTED_COOKIE_NAME = '2fa_trusted_device';
    public const TRUSTED_COOKIE_DAYS = 30;

    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Build fingerprint = hash(user_id + two_factor_secret).
     *
     * Khi 2FA secret thay đổi (disable→enable lại) → fingerprint đổi → cookie
     * cũ tự invalid mà không cần xoá. Khi user disable 2FA, secret=null →
     * fingerprint khác null-state → cookie không match.
     */
    public static function trustedFingerprint(\App\Models\User $user): string
    {
        return hash('sha256', $user->id . '|' . ($user->two_factor_secret ?? ''));
    }

    /**
     * Decrypt + verify cookie. Trả true nếu cookie hợp lệ cho user hiện tại.
     * Dùng trong middleware EnsureTwoFactorVerified.
     */
    public static function isTrustedDevice(Request $request, \App\Models\User $user): bool
    {
        $raw = $request->cookie(self::TRUSTED_COOKIE_NAME);
        if (!is_array($raw) && !is_string($raw)) return false;

        // Laravel auto decrypt cookie qua EncryptCookies middleware → cookie()
        // trả ra plain value. Nếu admin store array thì nhận array; nếu đã
        // serialize (vd queue() truyền string) thì cần decode.
        $payload = is_array($raw) ? $raw : json_decode($raw, true);
        if (!is_array($payload)) return false;

        if (($payload['uid'] ?? null) !== $user->id) return false;
        if (($payload['fp'] ?? null) !== self::trustedFingerprint($user)) return false;

        $ts = (int) ($payload['ts'] ?? 0);
        if ($ts <= 0) return false;
        // Hard expiry: 30 ngày kể từ verify (cookie lifetime cũng 30 ngày
        // nhưng check thêm phòng cookie bị tampered/clock-skew)
        if (now()->timestamp - $ts > self::TRUSTED_COOKIE_DAYS * 86400) return false;

        return true;
    }

    /**
     * Queue cookie trusted device — sau verify 2FA thành công, nếu user check
     * "Tin tưởng thiết bị này".
     */
    private function queueTrustedCookie(\App\Models\User $user): void
    {
        $payload = json_encode([
            'uid' => $user->id,
            'fp' => self::trustedFingerprint($user),
            'ts' => now()->timestamp,
        ], JSON_UNESCAPED_UNICODE);

        // Cookie::queue lifetime tính bằng phút
        Cookie::queue(
            self::TRUSTED_COOKIE_NAME,
            $payload,
            self::TRUSTED_COOKIE_DAYS * 24 * 60,
        );
    }

    /**
     * Forget cookie trên thiết bị hiện tại (dùng khi disable 2FA hoặc admin
     * muốn revoke trust trên browser này). Note: cookie chỉ xoá trên client
     * gửi request hiện tại — các thiết bị khác vẫn còn cookie cũ nhưng tự
     * invalid khi 2FA secret bị xoá (fingerprint mismatch).
     */
    private function forgetTrustedCookie(): void
    {
        Cookie::queue(Cookie::forget(self::TRUSTED_COOKIE_NAME));
    }

    /** GET /2fa/setup — màn hình bắt đầu setup 2FA */
    public function showSetup(Request $request)
    {
        $user = $request->user();
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.settings')
                ->with('info', '2FA đã được bật cho tài khoản này.');
        }

        $secret = $request->session()->get('2fa.pending_secret')
            ?? $this->google2fa->generateSecretKey();
        $request->session()->put('2fa.pending_secret', $secret);

        $qrUrl = $this->buildQrCodeUrl($user, $secret);

        return view('auth.two-factor-setup', compact('secret', 'qrUrl'));
    }

    /** POST /2fa/enable — verify code và lưu secret */
    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $secret = $request->session()->get('2fa.pending_secret');
        if (!$secret) {
            return back()->withErrors(['code' => 'Phiên setup đã hết. Vui lòng quét QR lại.']);
        }

        if (!$this->google2fa->verifyKey($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Mã 6 chữ số không đúng. Vui lòng thử lại.']);
        }

        $recoveryCodes = collect(range(1, 8))->map(fn() => Str::upper(Str::random(10)))->all();

        $user = $request->user();
        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);
        $request->session()->forget('2fa.pending_secret');
        $request->session()->put('2fa.passed', true);

        Log::info('2FA enabled', ['user_id' => $user->id]);

        return view('auth.two-factor-recovery-codes', compact('recoveryCodes'));
    }

    /** GET /2fa/settings */
    public function settings(Request $request)
    {
        return view('auth.two-factor-settings', ['user' => $request->user()]);
    }

    /** POST /2fa/disable */
    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = $request->user();
        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Mật khẩu không đúng.']);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled_at' => null,
            'two_factor_recovery_codes' => null,
        ]);
        $request->session()->forget('2fa.passed');
        // Forget trusted cookie trên thiết bị này. Cookie trên các thiết bị
        // khác tự invalid khi user re-enable 2FA (secret mới → fingerprint
        // khác → cookie cũ không match).
        $this->forgetTrustedCookie();

        Log::warning('2FA disabled', ['user_id' => $user->id]);

        return redirect()->route('two-factor.settings')
            ->with('success', 'Đã tắt 2FA cho tài khoản.');
    }

    /** GET /2fa/challenge */
    public function showChallenge(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if ($request->session()->get('2fa.passed') || !$request->user()->hasTwoFactorEnabled()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return view('auth.two-factor-challenge');
    }

    /** POST /2fa/verify */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|min:6|max:20']);

        $user = $request->user();
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        $input = trim($request->input('code'));
        // Default tin tưởng thiết bị (admin tự uncheck nếu là máy public).
        // Form gửi giá trị '1' khi check; absent / '0' → false.
        $trustDevice = (bool) $request->input('trust_device', false);

        if (preg_match('/^\d{6}$/', $input) && $this->google2fa->verifyKey($user->two_factor_secret, $input)) {
            $request->session()->put('2fa.passed', true);
            if ($trustDevice) {
                $this->queueTrustedCookie($user);
            }
            Log::info('2FA verified via TOTP', [
                'user_id' => $user->id,
                'trust_device' => $trustDevice,
            ]);
            return redirect()->intended(route('admin.dashboard'));
        }

        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        $upper = Str::upper($input);
        if (in_array($upper, $recoveryCodes, true)) {
            $remaining = array_values(array_filter($recoveryCodes, fn($c) => $c !== $upper));
            $user->update(['two_factor_recovery_codes' => $remaining]);
            $request->session()->put('2fa.passed', true);
            if ($trustDevice) {
                $this->queueTrustedCookie($user);
            }
            Log::warning('2FA verified via recovery code', [
                'user_id' => $user->id,
                'remaining_codes' => count($remaining),
                'trust_device' => $trustDevice,
            ]);
            $msg = count($remaining) === 0
                ? 'Đăng nhập thành công bằng mã recovery (đã hết — hãy disable+enable 2FA để sinh mới).'
                : "Đăng nhập thành công bằng mã recovery (còn " . count($remaining) . " mã).";
            return redirect()->intended(route('admin.dashboard'))->with('warning', $msg);
        }

        Log::warning('2FA verify failed', ['user_id' => $user->id, 'input_length' => strlen($input)]);
        return back()->withErrors(['code' => 'Mã không đúng. Hãy thử mã 6 chữ số trên app authenticator hoặc 1 trong các mã recovery.']);
    }

    /**
     * Build QR code URL cho otpauth:// URI — dùng api.qrserver.com (free, no auth).
     */
    private function buildQrCodeUrl($user, string $secret): string
    {
        $issuer = config('app.name', 'TruyCuu');
        $label = $user->email ?? $user->name;

        $otpauth = $this->google2fa->getQRCodeUrl($issuer, $label, $secret);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=10&data='
            . urlencode($otpauth);
    }
}
