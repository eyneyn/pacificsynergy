<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $user = Auth::user();

        // If first login & 2FA not enabled → generate secret & show QR
        if (!$user->two_factor_enabled || !$user->google2fa_secret) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();

            $user->google2fa_secret = $secret;
            $user->save();

            session(['2fa:user:id' => $user->id]);
            Auth::logout();

            return redirect()->route('2fa.setup');
        }

        // If 2FA already enabled → ask for OTP
        if ($user->two_factor_enabled) {
            session(['2fa:user:id' => $user->id]);
            Auth::logout();

            return redirect()->route('2fa.verify');
        }

        // Fallback normal login
        $request->session()->regenerate();
        return redirect()->route('2fa.verify.form');
    }

    public function setup2fa(): View
    {
        $user = \App\Models\User::find(session('2fa:user:id'));
        if (!$user) return redirect()->route('login');

        $google2fa = new Google2FA();
        $qrData = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        // Render QR
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = base64_encode($writer->writeString($qrData));

        return view('auth.2fa-setup', compact('qrCode'));
    }
public function show2faForm(): View
{
    $userId = session('2fa:user:id');
    if (!$userId) {
        return redirect()->route('login');
    }

    return view('auth.2fa-verify');
}
    public function verify2fa(Request $request): RedirectResponse
    {
        $request->validate(['otp' => 'required|digits:6']);
        $user = \App\Models\User::find(session('2fa:user:id'));
        if (!$user) return redirect()->route('login');

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($user->google2fa_secret, $request->otp)) {
            $user->two_factor_enabled = true;
            $user->save();

            Auth::login($user);
            session()->forget('2fa:user:id');
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo());
        }

        return back()->withErrors(['otp' => 'Invalid code, please try again.']);
    }

    protected function redirectTo(): string
    {
        $user = Auth::user();
        $redirectMap = [
            'user.dashboard'  => route('admin.dashboard'),
            'report.index'    => route('report.index'),
            'analytics.index' => route('analytics.index'),
        ];
        foreach ($redirectMap as $permission => $route) {
            if ($user->can($permission)) {
                return $route;
            }
        }
        return '/dashboard';
    }
    /**
     * Log out and destroy the session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('message', 'Your session has expired. Please log in again.');
    }

}
