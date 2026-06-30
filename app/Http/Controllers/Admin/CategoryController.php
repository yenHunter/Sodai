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
    // INDEX - List all categories
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $categories = $this->categoryService
                           ->getCategoriesWithChildren();

        return view('admin.ecommerce.category.index', compact('categories'));
    }

    // ─────────────────────────────────────────────
    // CREATE - Show create form
    // ─────────────────────────────────────────────

    public function create()
    {
        $parentCategories = $this->categoryService
                                 ->getParentCategories();

        return view('admin.ecommerce.category.create',
            compact('parentCategories')
        );
    }

    // ─────────────────────────────────────────────
    // STORE - Save new category
    // ─────────────────────────────────────────────

    public function store(StoreCategoryRequest $request)
    {
        try {
            $this->categoryService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', 'Category created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create category. ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // SHOW - Not needed, redirect to edit
    // ─────────────────────────────────────────────

    public function show(Category $category)
    {
        return redirect()->route('admin.ecommerce.category.edit', $category);
    }

    // ─────────────────────────────────────────────
    // EDIT - Show edit form
    // ─────────────────────────────────────────────

    public function edit(Category $category)
    {
        // Load parent for display
        $category->load('parent');

        $parentCategories = $this->categoryService
                                 ->getParentCategories();

        return view('admin.ecommerce.category.edit',
            compact('category', 'parentCategories')
        );
    }

    // ─────────────────────────────────────────────
    // UPDATE - Save updated category
    // ─────────────────────────────────────────────

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {
        try {
            $this->categoryService->update(
                $category,
                $request->validated()
            );

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', 'Category updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update category. ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY - Delete category
    // ─────────────────────────────────────────────

    public function destroy(Category $category)
    {
        try {
            $this->categoryService->delete($category);

            return redirect()
                ->route('admin.ecommerce.category.index')
                ->with('success', 'Category deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
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

            // If deactivated and had children, mention it
            if (!$updated->is_active && $category->hasChildren()) {
                $message .= ' All sub-categories have been deactivated too.';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update status. ' . $e->getMessage());
        }
    }
}