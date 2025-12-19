<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Normalize compare (antisipasi case mismatch)
        $userRole = strtolower((string) $user->role);
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles, true)) {
            // UX: kalau user klik link (GET), arahkan ke dashboard yg benar
            if ($request->isMethod('get')) {
                $target = match ($user->role) {
                    User::ROLE_SUPER_ADMIN => route('dashboard.super-admin'),
                    User::ROLE_ADMIN_TOKO => route('dashboard.admin-toko'),
                    default => route('dashboard'),
                };

                return redirect($target)->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
