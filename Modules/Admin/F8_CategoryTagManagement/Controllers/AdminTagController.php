<?php

namespace Modules\Admin\F8_CategoryTagManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Admin\F8_CategoryTagManagement\Requests\StoreTagRequest;
use Modules\Admin\F8_CategoryTagManagement\Services\CategoryTagService;

class AdminTagController extends Controller
{
    protected CategoryTagService $service;

    public function __construct(CategoryTagService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $tags = $this->service->getTags((int) $perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil daftar tag.',
            'data'    => $tags->items(),
            'meta'    => [
                'current_page' => $tags->currentPage(),
                'last_page'    => $tags->lastPage(),
                'per_page'     => $tags->perPage(),
                'total'        => $tags->total(),
            ]
        ], 200);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->service->createTag($request->validated());
        return response()->json([
            'status'  => 'success',
            'message' => 'Tag baru berhasil ditambahkan.',
            'data'    => $tag
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->deleteTag($id);
            return response()->json([
                'status'  => 'success',
                'message' => 'Tag berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}