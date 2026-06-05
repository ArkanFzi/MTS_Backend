<?php

namespace Modules\Admin\F11_BadgeMaster\Controllers;

use App\Http\Controllers\Controller; // Gunakan base controller yang benar
use Illuminate\Http\JsonResponse;
use Modules\Admin\F11_BadgeMaster\Services\BadgeService;
use Modules\Admin\F11_BadgeMaster\Requests\StoreBadgeRequest;
use Modules\Admin\F11_BadgeMaster\Requests\UpdateBadgeRequest;

class BadgeController extends Controller
{
    public function __construct(protected BadgeService $service) {}

    public function index(): JsonResponse
    {
        // Return JSON, jangan view()
        return response()->json([
            'success' => true,
            'data' => $this->service->getAllPaginated(15)
        ]);
    }

    public function store(StoreBadgeRequest $request): JsonResponse
    {
        $badge = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Badge berhasil dibuat.',
            'data' => $badge
        ], 201);
    }

    public function update(UpdateBadgeRequest $request, string $id): JsonResponse
    {
        $this->service->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Badge berhasil diperbarui.'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Badge berhasil dihapus.'
        ]);
    }
}