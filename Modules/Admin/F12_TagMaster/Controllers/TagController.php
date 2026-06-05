<?php

namespace Modules\Admin\F12_TagMaster\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Admin\F12_TagMaster\Services\TagService;
use Modules\Admin\F12_TagMaster\Requests\StoreTagRequest;
use Modules\Admin\F12_TagMaster\Requests\UpdateTagRequest;

class TagController extends Controller
{
    public function __construct(
        protected TagService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tags = $this->service->getAllPaginated(15, $request->search);
        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Tag berhasil dibuat.',
            'data' => $tag
        ], 201);
    }

    // Modifikasi di TagController.php
public function update(UpdateTagRequest $request, string $id): JsonResponse
{
    $tag = $this->service->update($id, $request->validated());
    
    return response()->json([
        'success' => true,
        'message' => 'Tag berhasil diperbarui.',
        'data' => $tag
    ]);
}

public function destroy(string $id): JsonResponse
{
    try {
        $this->service->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Tag berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus tag: ' . $e->getMessage()
        ], 422);
    }
}
}