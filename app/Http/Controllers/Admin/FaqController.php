<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Faq\StoreFaqRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqRequest;
use App\Models\Faq;
use App\Services\Admin\FaqService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct(
        private FaqService $faqService
    ) {}

    public function store(StoreFaqRequest $request)
    {
        try {
            $this->faqService->storeFaq($request->validated());

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', 'FAQ added successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', 'Failed to add FAQ: '.$e->getMessage());
        }
    }

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        try {
            $this->faqService->updateFaq($faq, $request->validated());

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', 'FAQ updated successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', 'Failed to update FAQ: '.$e->getMessage());
        }
    }

    public function destroy(Faq $faq)
    {
        try {
            $this->faqService->deleteFaq($faq);

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', 'FAQ deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.cms.faq.index')->with('error', 'No FAQs selected.');
        }

        $count = $this->faqService->bulkDeleteFaqs($ids);

        return redirect()
            ->route('admin.cms.faq.index')
            ->with('success', "{$count} FAQ(s) deleted successfully.");
    }

    public function toggleStatus(Faq $faq)
    {
        try {
            $updated = $this->faqService->toggleFaqStatus($faq);
            $status = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.cms.faq.index')
                ->with('success', "FAQ {$status} successfully.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.faq.index')
                ->with('error', 'Failed to update status.');
        }
    }
}
