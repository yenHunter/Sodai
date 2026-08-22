<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Admin\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\Admin\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(
        private BrandService $brandService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index()
    {
        $brands = $this->brandService->getBrandsList();

        return view('admin.ecommerce.product.brand', compact('brands'));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreBrandRequest $request)
    {
        try {
            $this->brandService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('error', 'Failed to create brand: '.$e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {
            $this->brandService->update($brand, $request->validated());

            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('error', 'Failed to update brand: '.$e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Brand $brand)
    {
        try {
            $this->brandService->delete($brand);

            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('success', 'Brand deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'string'],
        ]);

        $ids = array_filter(
            explode(',', $request->input('ids')),
            fn ($id) => is_numeric($id)
        );

        if (empty($ids)) {
            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('error', 'No brands selected.');
        }

        $successCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $brand = Brand::find($id);
            if (! $brand) {
                continue;
            }

            try {
                $this->brandService->delete($brand);
                $successCount++;
            } catch (\Exception $e) {
                $failedNames[] = $brand->name;
            }
        }

        $message = "{$successCount} brand".
            ($successCount === 1 ? '' : 's').
            ' deleted successfully.';

        if (! empty($failedNames)) {
            $message .= ' Failed: '.implode(', ', $failedNames).'.';

            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('error', $message);
        }

        return redirect()
            ->route('admin.ecommerce.brand.index')
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS - Activate/Deactivate
    // ─────────────────────────────────────────────

    public function toggleStatus(Brand $brand)
    {
        try {
            $updated = $this->brandService->toggleStatus($brand);
            $status = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('success', "Brand {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.brand.index')
                ->with('error', 'Failed to update status.');
        }
    }
}
