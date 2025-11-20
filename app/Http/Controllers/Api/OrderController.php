<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Orders\CreateOrderAction;
use App\Jobs\GenerateInvoicePdf;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // POST /api/v1/orders
    public function store(Request $request, CreateOrderAction $action)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // Execute Action
            $order = $action->execute($request->user(), $validated['items']);

            // Dispatch Invoice Job (Async)
            GenerateInvoicePdf::dispatch($order);

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // GET /api/v1/my-orders (Customer History)
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items.variant.product')
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

        // PATCH /api/v1/orders/{id}/cancel
    public function cancel(Order $order, \App\Actions\Orders\CancelOrderAction $action)
    {
        // Ensure user owns the order (or is admin)
        if (request()->user()->id !== $order->user_id && request()->user()->role->value !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $action->execute($order);
            return response()->json(['message' => 'Order cancelled and inventory restored.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // PATCH /api/v1/orders/{id}/status (Admin/Vendor only)
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered'
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json(['message' => 'Order status updated to ' . $validated['status']]);
    }
}
