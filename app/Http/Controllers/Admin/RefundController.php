<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Refund\StoreRefundRequest;
use App\Http\Requests\Admin\Refund\UpdateRefundRequest;
use App\Models\Refund;
use App\Services\Admin\RefundService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct(
        private RefundService $refundService
    ) {}

    public function index(Request $request)
    {
        $refunds = $this->refundService->getRefundsList($request->only(['search', 'status']));
        $stats = $this->refundService->getRefundStats();
        $orders = $this->refundService->getOrdersForSelect();

        return view('admin.ecommerce.refund.index', compact('refunds', 'stats', 'orders'));
    }

    public function store(StoreRefundRequest $request)
    {
        try {
            $this->refundService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.refund.index')
                ->with('success', 'Refund request created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.refund.index')
                ->with('error', 'Failed to create refund: '.$e->getMessage());
        }
    }

    public function update(UpdateRefundRequest $request, Refund $refund)
    {
        try {
            $this->refundService->update($refund, $request->validated());

            return redirect()
                ->route('admin.ecommerce.refund.index')
                ->with('success', 'Refund updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.refund.index')
                ->with('error', 'Failed to update refund: '.$e->getMessage());
        }
    }

    public function destroy(Refund $refund)
    {
        try {
            $this->refundService->delete($refund);

            return redirect()
                ->route('admin.ecommerce.refund.index')
                ->with('success', 'Refund deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.refund.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.ecommerce.refund.index')->with('error', 'No refunds selected.');
        }

        $successCount = 0;
        $failedNumbers = [];

        foreach ($ids as $id) {
            $refund = Refund::find($id);
            if (! $refund) {
                continue;
            }

            try {
                $this->refundService->delete($refund);
                $successCount++;
            } catch (\Exception $e) {
                $failedNumbers[] = $refund->refund_number;
            }
        }

        $message = "{$successCount} refund(s) deleted successfully.";
        if (! empty($failedNumbers)) {
            $message .= ' Skipped (approved): '.implode(', ', $failedNumbers).'.';
        }

        return redirect()->route('admin.ecommerce.refund.index')->with('success', $message);
    }

    public function approve(Request $request, Refund $refund)
    {
        $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);

        try {
            $this->refundService->approve($refund, $request->input('admin_note'));

            return redirect()->route('admin.ecommerce.refund.index')->with('success', 'Refund approved.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.refund.index')->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Refund $refund)
    {
        $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);

        try {
            $this->refundService->reject($refund, $request->input('admin_note'));

            return redirect()->route('admin.ecommerce.refund.index')->with('success', 'Refund rejected.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.refund.index')->with('error', $e->getMessage());
        }
    }
}
