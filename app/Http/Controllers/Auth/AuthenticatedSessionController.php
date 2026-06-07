<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Ambil ID Role user yang lagi login
        $roleId = $request->user()->role_id;

        // Polisi Lalu Lintas: Arahkan sesuai jabatan!
        if ($roleId == 1) {
            // Jika Super Admin
            return redirect()->intended('/admin/dashboard');
        } elseif ($roleId == 2) {
            // Jika Kasir
            return redirect()->intended('/kasir/pos');
        } elseif ($roleId == 3) {
            // Jika Pelanggan (Role 3)
            return redirect()->intended('/pelanggan/orders');
        } else {
            return redirect()->intended('/koki');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
