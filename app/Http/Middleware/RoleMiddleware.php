<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Pastikan relasi roles sudah ter-load
        $user->load('roles');

        // Ambil array nama role dari user (contoh: ['admin', 'moderator'])
        $userRoles = $user->roles->pluck('name')->toArray();

        // Cek apakah ada irisan antara role user dan role yang diminta di rute
        // array_intersect akan mengembalikan elemen yang sama
        $hasAccess = !empty(array_intersect($userRoles, $roles));

        if (!$hasAccess) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required role.',
                'user_has' => $userRoles,
                'required' => $roles
            ], 403);
        }

        return $next($request);
    }
}