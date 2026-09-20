<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Offer\StoreOfferRequest;
use App\Http\Requests\Admin\Offer\UpdateOfferRequest;
use App\Models\Offer;
use App\Services\Admin\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function __construct(
        private OfferService $offerService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index()
    {
        $offers = $this->offerService->getOffersList();
        $categories = $this->offerService->getCategoriesForSelect();

        return view('admin.cms.offer.index', compact('offers', 'categories'));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreOfferRequest $request)
    {
        try {
            $this->offerService->store($request->validated());

            return redirect()
                ->route('admin.cms.offer.index')
                ->with('success', 'Offer created successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.offer.index')
                ->with('error', 'Failed to create offer: '.$e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        try {
            $this->offerService->update($offer, $request->validated());

            return redirect()
                ->route('admin.cms.offer.index')
                ->with('success', 'Offer updated successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.offer.index')
                ->with('error', 'Failed to update offer: '.$e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Offer $offer)
    {
        try {
            $this->offerService->delete($offer);

            return redirect()
                ->route('admin.cms.offer.index')
                ->with('success', 'Offer deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.offer.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.cms.offer.index')->with('error', 'No offers selected.');
        }

        $count = 0;
        foreach (Offer::whereIn('id', $ids)->get() as $offer) {
            $this->offerService->delete($offer);
            $count++;
        }

        return redirect()
            ->route('admin.cms.offer.index')
            ->with('success', "{$count} offer(s) deleted successfully.");
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Offer $offer)
    {
        try {
            $updated = $this->offerService->toggleStatus($offer);
            $status = $updated->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.cms.offer.index')
                ->with('success', "Offer {$status} successfully.");
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.cms.offer.index')
                ->with('error', 'Failed to update status.');
        }
    }
}
