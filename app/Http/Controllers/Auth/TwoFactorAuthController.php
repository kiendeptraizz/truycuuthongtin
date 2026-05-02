<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
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

        if (preg_match('/^\d{6}$/', $input) && $this->google2fa->verifyKey($user->two_factor_secret, $input)) {
            $request->session()->put('2fa.passed', true);
            Log::info('2FA verified via TOTP', ['user_id' => $user->id]);
            return redirect()->intended(route('admin.dashboard'));
        }

        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        $upper = Str::upper($input);
        if (in_array($upper, $recoveryCodes, true)) {
            $remaining = array_values(array_filter($recoveryCodes, fn($c) => $c !== $upper));
            $user->update(['two_factor_recovery_codes' => $remaining]);
            $request->session()->put('2fa.passed', true);
            Log::warning('2FA verified via recovery code', [
                'user_id' => $user->id,
                'remaining_codes' => count($remaining),
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
