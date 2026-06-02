<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Kalau belum login, lempar ke halaman login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->role_id;

        // Peta Role biar gampang panggilnya di route
        $roles = [
            'super_admin' => 1,
            'kasir'       => 2,
            'pelanggan'   => 3,
            'koki'   => 4,
        ];

        // Cek apakah role user sesuai dengan role yang diizinkan untuk buka halaman tersebut
        if ($userRole != $roles[$role]) {
            // Kalau ketahuan nyusup, kasih error 403 (Akses Ditolak)
            abort(403, 'Hayo! Lu nggak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}