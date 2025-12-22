<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatifyRoleMiddleware
{
    /**
     * Handle an incoming request.
     * Validates that users can only interact with allowed contacts
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow access to main chat interface
        if ($request->is('chatify') || $request->is('chatify/')) {
            return $next($request);
        }

        // For actions involving another user (send, fetch, etc.)
        $targetUserId = $request->input('id') ?? $request->route('id');

        if ($targetUserId && $targetUserId != $user->id) {
            $targetUser = \App\Models\User::find($targetUserId);

            if ($targetUser && !$user->canChatWith($targetUser)) {
                if ($request->expectsJson() || $request->is('chatify/*')) {
                    return response()->json([
                        'error' => 'Anda tidak memiliki akses untuk berkomunikasi dengan pengguna ini.'
                    ], 403);
                }

                abort(403, 'Akses ditolak');
            }
        }

        return $next($request);
    }
}
