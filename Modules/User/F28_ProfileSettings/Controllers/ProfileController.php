<?php

namespace Modules\User\F28_ProfileSettings\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\F28_ProfileSettings\Requests\UpdateProfileRequest;
use Modules\User\F28_ProfileSettings\Services\ProfileService;

class ProfileController extends Controller
{
    protected $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function show(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil dimuat',
            'data'    => auth()->user()
        ]);
    }

    public function showPublic(string $id): JsonResponse
    {
        $user = \App\Models\Auth\User::with('roles')
            ->withCount(['posts', 'followers', 'following'])
            ->findOrFail($id);
            
        // Map roles to array of strings
        $rolesList = $user->roles->pluck('name');
        $user->unsetRelation('roles');
        $user->roles = $rolesList;
        
        if (auth('sanctum')->check()) {
            $user->is_following = auth('sanctum')->user()->following()->where('following_id', $id)->exists();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil publik berhasil dimuat',
            'data'    => $user
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->service->updateProfile(auth()->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => $user
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        try {
            $this->service->updatePassword(auth()->user(), $request->old_password, $request->new_password);
            return response()->json(['success' => true, 'message' => 'Password berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
