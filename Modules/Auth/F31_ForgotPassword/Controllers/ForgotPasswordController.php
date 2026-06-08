<?php

namespace Modules\Auth\F31_ForgotPassword\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Auth\F31_ForgotPassword\Requests\ForgotPasswordRequest;
use Modules\Auth\F31_ForgotPassword\Requests\ResetPasswordRequest;
use Modules\Auth\F31_ForgotPassword\Services\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
    public function __construct(
        protected ForgotPasswordService $service
    ) {}

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->sendResetLink($request->email);

        return response()->json([
            'success' => true,
            'message' => 'Jika email terdaftar, link reset password akan dikirim.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->service->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset. Silakan login.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }
}