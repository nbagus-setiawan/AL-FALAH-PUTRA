<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! $user->is_active) {
            return back()->withErrors(['username' => 'Akun tidak ditemukan atau tidak aktif.'])->onlyInput('username');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectByRole(User $user)
    {
        return match ($user->role) {
            User::ROLE_KETUA_UMUM => redirect()->route('ketua-umum.dashboard'),
            User::ROLE_SEKRETARIS => redirect()->route('sekretaris.dashboard'),
            User::ROLE_BENDAHARA => redirect()->route('bendahara.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
