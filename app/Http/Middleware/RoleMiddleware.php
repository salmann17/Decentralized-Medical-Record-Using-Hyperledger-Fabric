<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();

        switch ($role) {
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Anda tidak memiliki akses sebagai admin.');
                }
                break;
            case 'doctor':
                if (!$user->isDoctor()) {
                    abort(403, 'Anda tidak memiliki akses sebagai dokter.');
                }
                break;
            case 'patient':
                if (!$user->isPatient()) {
                    abort(403, 'Anda tidak memiliki akses sebagai pasien.');
                }
                break;
            default:
                abort(403, 'Role tidak valid.');
        }

        return $next($request);
    }
}