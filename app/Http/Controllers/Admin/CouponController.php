<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Services\Admin\CouponService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coupon\StoreCouponRequest;
use App\Http\Requests\Admin\Coupon\UpdateCouponRequest;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index()
    {
        $coupons = $this->couponService->getCouponsList();

        return view('admin.ecommerce.coupon.index', compact('coupons'));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreCouponRequest $request)
    {
        try {
            $this->couponService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('success', 'Coupon created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('error', 'Failed to create coupon: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        try {
            $this->couponService->update($coupon, $request->validated());

            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('success', 'Coupon updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('error', 'Failed to update coupon: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Coupon $coupon)
    {
        try {
            $this->couponService->delete($coupon);

            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('success', 'Coupon deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.ecommerce.coupon.index')->with('error', 'No coupons selected.');
        }

        $successCount = 0;
        $failedCodes  = [];

        foreach ($ids as $id) {
            $coupon = Coupon::find($id);
            if (!$coupon) continue;

            try {
                $this->couponService->delete($coupon);
                $successCount++;
            } catch (\Exception $e) {
                $failedCodes[] = $coupon->code;
            }
        }

        $message = "{$successCount} coupon" . ($successCount === 1 ? '' : 's') . " deleted successfully.";

        if (!empty($failedCodes)) {
            $message .= ' Skipped (already used): ' . implode(', ', $failedCodes) . '.';
            return redirect()->route('admin.ecommerce.coupon.index')->with('error', $message);
        }

        return redirect()->route('admin.ecommerce.coupon.index')->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Coupon $coupon)
    {
        try {
            $updated = $this->couponService->toggleStatus($coupon);
            $status  = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('success', "Coupon {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.coupon.index')
                ->with('error', 'Failed to update status.');
        }
    }
}