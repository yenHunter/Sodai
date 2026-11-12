<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\TrackOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index(Request $request)
    {
        return view('visitor.pages.track-order', [
            'orderNumber' => (string) $request->query('order_number', ''),
        ]);
    }

    public function lookup(TrackOrderRequest $request)
    {
        $data = $request->validated();

        $order = Order::with('items')
            ->where('order_number', mb_strtoupper(trim($data['order_number'])))
            ->whereRaw('LOWER(shipping_email) = ?', [mb_strtolower(trim($data['email']))])
            ->first();

        if (! $order) {
            return redirect()
                ->route('visitor.track-order')
                ->withInput()
                ->with('error', 'We could not find an order with that number and email address.');
        }

        $trackingSteps = $this->buildTrackingSteps($order->status);

        return view('visitor.pages.track-order', compact('order', 'trackingSteps'));
    }

    private function buildTrackingSteps(string $status): array
    {
        $steps = [
            'pending' => 'Order Placed',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ];

        $statuses = array_keys($steps);
        $currentIdx = array_search($status, $statuses, true);

        // Cancelled/refunded orders never reach a linear step, so none is current.
        $currentIdx = $currentIdx === false ? -1 : $currentIdx;

        return collect($steps)->values()->map(function ($label, $i) use ($currentIdx) {
            return [
                'label' => $label,
                'icon' => $i + 1,
                'active' => $currentIdx >= 0 && $i <= $currentIdx,
            ];
        })->toArray();
    }
}
