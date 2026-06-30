<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index()
    {
        $categories       = $this->categoryService->getCategoriesWithChildren();
        $parentCategories = $this->categoryService->getParentCategories();

        return view(
            'admin.ecommerce.category.index',
            compact('categories', 'parentCategories')
        );
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreCategoryRequest $request)
    {
        try {
            $this->categoryService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // EDIT - Returns data for modal via redirect back
    // ─────────────────────────────────────────────

    public function edit(Category $category)
    {
        // Load relationships
        $category->load('parent', 'children');

        $categories       = $this->categoryService->getCategoriesWithChildren();
        $parentCategories = $this->categoryService->getParentCategories();

        return view('admin.ecommerce.category.index', compact(
            'categories',
            'parentCategories',
            'category',        // ← editing category passed to view
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->update($category, $request->validated());

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Category $category)
    {
        try {
            $this->categoryService->delete($category);

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'string'],
        ]);

        $ids = array_filter(
            explode(',', $request->input('ids')),
            fn($id) => is_numeric($id)
        );

        if (empty($ids)) {
            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('error', 'No categories selected.');
        }

        $successCount = 0;
        $failedNames  = [];

        foreach ($ids as $id) {
            $category = Category::find($id);
            if (!$category) continue;

            try {
                $this->categoryService->delete($category);
                $successCount++;
            } catch (\Exception $e) {
                $failedNames[] = $category->name;
            }
        }

        $message = "{$successCount} categor" .
            ($successCount === 1 ? 'y' : 'ies') .
            " deleted successfully.";

        if (!empty($failedNames)) {
            $message .= ' Failed: ' . implode(', ', $failedNames) . '.';
            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('error', $message);
        }

        return redirect()
            ->route('admin.ecommerce.category.index')
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS - Activate/Deactivate
    // ─────────────────────────────────────────────

    public function toggleStatus(Category $category)
    {
        try {
            $updated = $this->categoryService->toggleStatus($category);
            $status  = $updated->is_active ? 'activated' : 'deactivated';
            $message = "Category {$status} successfully.";

            if (!$updated->is_active && $category->children()->count() > 0) {
                $message .= ' All sub-categories deactivated too.';
            }

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('error', 'Failed to update status.');
        }
    }
}
