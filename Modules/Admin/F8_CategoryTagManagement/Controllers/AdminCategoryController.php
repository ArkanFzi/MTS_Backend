<?php

namespace Modules\Admin\F8_CategoryTagManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Admin\F8_CategoryTagManagement\Requests\StoreCategoryRequest;
use Modules\Admin\F8_CategoryTagManagement\Services\CategoryTagService;

class AdminCategoryController extends Controller
{
    protected CategoryTagService $service;

    public function __construct(CategoryTagService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $categories = $this->service->getCategories();
        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil semua kategori.',
            'data'    => $categories
        ], 200);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->service->createCategory($request->validated());
        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori baru berhasil ditambahkan.',
            'data'    => $category
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->deleteCategory($id);
            return response()->json([
                'status'  => 'success',
                'message' => 'Kategori berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}