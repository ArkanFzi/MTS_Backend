<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!auth()->check()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $user = auth()->user();

    // PAKSA LOAD ROLES agar data terbaru dari database terbaca
    $user->load('roles'); 

    // Debugging: Lihat apa isi role user saat ini
    // dd($user->roles->pluck('name')->toArray()); 

    if (!$user->hasAnyRole(...$roles)) {
        return response()->json([
            'message' => 'Forbidden. You do not have the required role.',
            'debug_user_roles' => $user->roles->pluck('name') // Tambahkan ini sementara untuk cek
        ], 403);
    }

    return $next($request);
}
}