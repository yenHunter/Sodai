<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Faq\StoreFaqCategoryRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqCategoryRequest;
use App\Models\FaqCategory;
use App\Services\Admin\FaqService;

class FaqCategoryController extends Controller
{
    public function __construct(
        private FaqService $faqService
    ) {}

    public function index()
    {
        $categories = $this->faqService->getCategoriesWithFaqs();

        return view('admin.cms.faq.index', compact('categories'));
    }

    public function store(StoreFaqCategoryRequest $request)
    {
        try {
            $category = $this->faqService->storeCategory($request->validated());

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', "Category \"{$category->name}\" created successfully.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', 'Failed to create category: '.$e->getMessage());
        }
    }

    public function update(UpdateFaqCategoryRequest $request, FaqCategory $faq_category)
    {
        try {
            $this->faqService->updateCategory($faq_category, $request->validated());

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', 'Failed to update category: '.$e->getMessage());
        }
    }

    public function destroy(FaqCategory $faq_category)
    {
        try {
            $this->faqService->deleteCategory($faq_category);

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(FaqCategory $faq_category)
    {
        try {
            $updated = $this->faqService->toggleCategoryStatus($faq_category);
            $status = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', "Category {$status} successfully.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', 'Failed to update status.');
        }
    }
}
