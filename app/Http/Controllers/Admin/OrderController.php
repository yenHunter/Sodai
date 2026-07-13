<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\StoreOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $orders = $this->orderService->getOrdersList($request->only(['search', 'status', 'date_from', 'date_to']));

        return view('admin.ecommerce.order.index', compact('orders'));
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

    public function show(Order $order)
    {
        $order = $this->orderService->getOrderForDetails($order);

        return view('admin.ecommerce.order.show', compact('order'));
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function create()
    {
        return view('admin.ecommerce.order.create');
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.order.show', $order)
                ->with('success', "Order {$order->order_number} created successfully.");
        } catch (\Exception $e) {
            Log::error('Admin failed to create order.', [
                'exception' => $e,
                'admin_id'  => Auth::guard('admin')->id(),
            ]);

            return redirect()
                ->route('admin.ecommerce.order.create')
                ->with('error', 'Failed to create order: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────

    public function edit(Order $order)
    {
        if (!$order->isEditable()) {
            return redirect()
                ->route('admin.ecommerce.order.show', $order)
                ->with('error', 'This order can no longer be edited because of its current status.');
        }

        $order = $this->orderService->getOrderForEdit($order);

        return view('admin.ecommerce.order.edit', compact('order'));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $this->orderService->update($order, $request->validated());

            return redirect()
                ->route('admin.ecommerce.order.show', $order)
                ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            Log::error('Admin failed to update order.', [
                'exception' => $e,
                'admin_id'  => Auth::guard('admin')->id(),
                'order_id'  => $order->id,
            ]);

            return redirect()
                ->route('admin.ecommerce.order.edit', $order)
                ->with('error', 'Failed to update order: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Order $order)
    {
        try {
            $this->orderService->delete($order);

            return redirect()
                ->route('admin.ecommerce.order.index')
                ->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.order.index')
                ->with('error', $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // STATUS / CANCEL
    // ─────────────────────────────────────────────

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', Order::STATUSES)],
        ]);

        try {
            $this->orderService->updateStatus($order, $request->input('status'));

            return redirect()
                ->back()
                ->with('success', 'Order status updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->orderService->cancel($order, $request->input('cancel_reason'));

            return redirect()
                ->back()
                ->with('success', 'Order cancelled.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // POS AJAX ENDPOINTS
    // ─────────────────────────────────────────────

    public function searchCustomers(Request $request)
    {
        $search = $request->input('q', '');

        $customers = User::select('id', 'name', 'email', 'phone')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->where('status', 'active')
            ->limit(20)
            ->get();

        return response()->json($customers);
    }

    public function getCustomerAddress(User $customer)
    {
        $address = $customer->defaultAddress()->first() ?? $customer->addresses()->first();

        return response()->json([
            'name'    => $customer->name,
            'email'   => $customer->email,
            'phone'   => $customer->phone ?? $address?->recipient_phone,
            'address' => $address ? trim("{$address->address_line_1} {$address->address_line_2}") : null,
            'city'    => $address?->city,
            'state'   => $address?->state,
            'zip'     => $address?->zip_code,
            'country' => $address?->country ?? 'Bangladesh',
        ]);
    }

    public function quickCreateCustomer(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        try {
            $customer = $this->orderService->quickCreateCustomer($request->only(['name', 'email', 'phone']));

            return response()->json([
                'success' => true,
                'customer' => $customer->only(['id', 'name', 'email', 'phone']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function searchProducts(Request $request)
    {
        $search = $request->input('q', '');

        $products = Product::active()
            ->inStock()
            ->select('id', 'name', 'sku', 'thumbnail', 'price', 'discount_type', 'discount_value', 'stock_quantity')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(fn ($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'sku'            => $p->sku,
                'thumbnail_url'  => $p->thumbnail_url,
                'price'          => (float) $p->final_price,
                'stock_quantity' => $p->stock_quantity,
            ]);

        return response()->json($products);
    }
}