<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundService
{
    public function __construct(
        private OrderService $orderService,
        private SettingService $settingService
    ) {}

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Refund
    {
        return DB::transaction(function () use ($data) {
            $order = Order::findOrFail($data['order_id']);

            return Refund::create([
                'refund_number' => $this->generateUniqueRefundNumber(),
                'order_id'      => $order->id,
                'user_id'       => $order->user_id,
                'amount'        => $data['amount'],
                'reason'        => $data['reason'],
                'status'        => 'pending',
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Refund $refund, array $data): Refund
    {
        if (!$refund->isEditable()) {
            throw new \Exception('Only pending refunds can be edited.');
        }

        $refund->update([
            'order_id' => $data['order_id'],
            'amount'   => $data['amount'],
            'reason'   => $data['reason'],
        ]);

        return $refund->fresh();
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Refund $refund): bool
    {
        if (!$refund->isDeletable()) {
            throw new \Exception('Approved refunds cannot be deleted.');
        }

        return $refund->delete();
    }

    // ─────────────────────────────────────────────
    // APPROVE / REJECT
    // ─────────────────────────────────────────────

    public function approve(Refund $refund, ?string $note = null): Refund
    {
        if ($refund->status !== 'pending') {
            throw new \Exception('Only pending refunds can be approved.');
        }

        return DB::transaction(function () use ($refund, $note) {
            $refund->update([
                'status'       => 'approved',
                'admin_note'   => $note,
                'processed_by' => Auth::guard('admin')->id(),
                'processed_at' => now(),
            ]);

            $order = $refund->order;

            // Sync order status + restore stock, unless already refunded/cancelled (idempotency guard).
            if ($order && !in_array($order->status, ['refunded', 'cancelled'])) {
                $this->orderService->updateStatus($order, 'refunded', "Refund #{$refund->refund_number} approved.");
            }

            Log::info('Refund approved.', ['refund_id' => $refund->id]);

            return $refund->fresh();
        });
    }

    public function reject(Refund $refund, ?string $note = null): Refund
    {
        if ($refund->status !== 'pending') {
            throw new \Exception('Only pending refunds can be rejected.');
        }

        $refund->update([
            'status'       => 'rejected',
            'admin_note'   => $note,
            'processed_by' => Auth::guard('admin')->id(),
            'processed_at' => now(),
        ]);

        Log::info('Refund rejected.', ['refund_id' => $refund->id]);

        return $refund->fresh();
    }

    // ─────────────────────────────────────────────
    // REFUND NUMBER
    // ─────────────────────────────────────────────

    private function generateUniqueRefundNumber(): string
    {
        $prefix = $this->settingService->getGroup('invoice')['invoice_prefix'] ?? 'INV-';
        $prefix = rtrim($prefix, '-') . '-REF-';

        $lastNumber = Refund::where('refund_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderBy('refund_number', 'desc')
            ->value('refund_number');

        $next = 1;
        if ($lastNumber) {
            $next = (int) substr($lastNumber, strrpos($lastNumber, '-') + 1) + 1;
        }

        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getRefundsList(array $filters = [])
    {
        $query = Refund::with(['order', 'user', 'processedBy']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function getRefundStats(): array
    {
        $counts = Refund::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        return [
            'total'    => (int) $counts->sum(),
            'pending'  => (int) ($counts->get('pending') ?? 0),
            'approved' => (int) ($counts->get('approved') ?? 0),
            'rejected' => (int) ($counts->get('rejected') ?? 0),
        ];
    }

    public function getOrdersForSelect()
    {
        return Order::select('id', 'order_number', 'total_amount')->latest()->limit(100)->get();
    }
}
