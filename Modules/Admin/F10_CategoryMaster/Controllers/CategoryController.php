<?php

namespace Modules\Admin\F10_CategoryMaster\Controllers;

use App\Http\Controllers\Controller; // Gunakan base controller yang benar
use Modules\Admin\F10_CategoryMaster\Services\CategoryService;
use Modules\Admin\F10_CategoryMaster\Requests\StoreCategoryRequest;
use Modules\Admin\F10_CategoryMaster\Requests\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getHierarchy();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category->load(['parent'])
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = $this->categoryService->find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category->load(['parent', 'children'])
        ]);
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $success = $this->categoryService->update($id, $request->validated());

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $success = $this->categoryService->delete($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}