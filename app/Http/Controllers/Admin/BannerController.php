<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Services\Admin\BannerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;

class BannerController extends Controller
{
    public function __construct(
        private BannerService $bannerService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index()
    {
        $banners = $this->bannerService->getBannersList();

        return view('admin.ecommerce.banner.index', compact('banners'));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreBannerRequest $request)
    {
        try {
            $this->bannerService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('success', 'Banner created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('error', 'Failed to create banner: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        try {
            $this->bannerService->update($banner, $request->validated());

            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('success', 'Banner updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('error', 'Failed to update banner: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Banner $banner)
    {
        try {
            $this->bannerService->delete($banner);

            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('success', 'Banner deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.ecommerce.banner.index')->with('error', 'No banners selected.');
        }

        $successCount = 0;
        $failedTitles = [];

        foreach ($ids as $id) {
            $banner = Banner::find($id);
            if (!$banner) continue;

            try {
                $this->bannerService->delete($banner);
                $successCount++;
            } catch (\Exception $e) {
                $failedTitles[] = $banner->title ?? "#{$banner->id}";
            }
        }

        $message = "{$successCount} banner" . ($successCount === 1 ? '' : 's') . " deleted successfully.";

        if (!empty($failedTitles)) {
            $message .= ' Failed: ' . implode(', ', $failedTitles) . '.';
            return redirect()->route('admin.ecommerce.banner.index')->with('error', $message);
        }

        return redirect()->route('admin.ecommerce.banner.index')->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Banner $banner)
    {
        try {
            $updated = $this->bannerService->toggleStatus($banner);
            $status  = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('success', "Banner {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.banner.index')
                ->with('error', 'Failed to update status.');
        }
    }
}