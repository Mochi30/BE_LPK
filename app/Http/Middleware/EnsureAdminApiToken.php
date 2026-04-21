<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json([
                'message' => 'Token admin diperlukan.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::query()
            ->where('role', 'admin')
            ->where('api_token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $user) {
            return response()->json([
                'message' => 'Token admin tidak valid atau sudah kedaluwarsa.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $request->setUserResolver(static fn () => $user);

        return $next($request);
    }
}
