<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // WAJIB: cegah Session Fixation
            $request->session()->regenerate();

            // State sesi
            session(['login_timestamp' => now()->format('d M Y, H:i:s').' WIB']);

            // HTTP Cookie (7 hari = 10080 menit)
            Cookie::queue('del_last_user_email', $request->email, 10080);

            return redirect()->intended(route('bukus.index'))
                ->with('success', 'Login berhasil. Sesi Anda telah aktif.');
        }

        return back()->withInput($request->only('email'))
            ->with('error', 'Kredensial login tidak valid.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesi server telah dihapus dengan aman.');
    }
}
