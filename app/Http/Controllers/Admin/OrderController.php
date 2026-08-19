<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admin\OrderService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\Admin\SettingService;
use App\Http\Requests\Admin\Order\StoreOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private SettingService $settingService
    ) {}

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $orders = $this->orderService->getOrdersList($request->only(['search', 'status', 'date_from', 'date_to']));
        $stats  = $this->orderService->getOrderStats();

        return view('admin.ecommerce.order.index', compact('orders', 'stats'));
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

    public function show(Order $order)
    {
        $order = $this->orderService->getOrderForDetails($order);

        return view('admin.ecommerce.order.details', compact('order'));
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
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->orderService->updateStatus($order, $request->input('status'), $request->input('note'));

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
            ->with('defaultVariant')
            ->select('id', 'name', 'thumbnail', 'min_price', 'max_price', 'total_stock')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('variants', fn($v) => $v->where('sku', 'like', "%{$search}%"));
            })
            ->limit(20)
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'sku'            => $p->defaultVariant?->sku,
                'thumbnail_url'  => $p->thumbnail_url,
                'price'          => (float) $p->final_price,
                'stock_quantity' => $p->total_stock,
            ]);

        return response()->json($products);
    }

    // ─────────────────────────────────────────────
    // SHIPPING CHARGE PREVIEW (AJAX)
    // Called from the POS form whenever the shipping city changes,
    // so the admin sees the settings-driven charge before saving.
    // ─────────────────────────────────────────────

    public function previewShippingCharge(Request $request)
    {
        $request->validate([
            'city'     => ['nullable', 'string', 'max:100'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $charge = $this->settingService->resolveShippingCharge(
            $request->input('city'),
            (float) $request->input('subtotal', 0)
        );

        return response()->json([
            'shipping_charge' => $charge,
            'within_area'     => $this->settingService->isWithinOperationArea($request->input('city')),
        ]);
    }

    // ─────────────────────────────────────────────
    // TAX PREVIEW (AJAX)
    // ─────────────────────────────────────────────

    public function previewTax(Request $request)
    {
        $request->validate([
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $taxSettings = $this->settingService->getGroup('tax');
        $enabled     = ($taxSettings['tax_enabled'] ?? '0') === '1';
        $rate        = (float) ($taxSettings['tax_rate'] ?? 0);
        $label       = $taxSettings['tax_label'] ?? 'Tax';
        $inclusive   = ($taxSettings['prices_include_tax'] ?? '0') === '1';

        $subtotal = (float) $request->input('subtotal');

        $taxAmount = 0.0;
        if ($enabled && $rate > 0) {
            $taxAmount = $inclusive
                ? round($subtotal - ($subtotal / (1 + ($rate / 100))), 2)
                : round($subtotal * ($rate / 100), 2);
        }

        return response()->json([
            'tax_enabled' => $enabled,
            'tax_label'   => $label,
            'tax_rate'    => $rate,
            'tax_amount'  => $taxAmount,
        ]);
    }

    // ─────────────────────────────────────────────
    // COUPON VALIDATE / APPLY (AJAX)
    // ─────────────────────────────────────────────

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code'     => ['required', 'string', 'max:50'],
            'user_id'  => ['required', 'integer', 'exists:users,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'order_id' => ['nullable', 'integer'],
        ]);

        $code    = strtoupper(trim($request->input('code')));
        $coupon  = Coupon::where('code', $code)->first();
        $subtotal = (float) $request->input('subtotal');

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => "Coupon \"{$code}\" does not exist."], 422);
        }

        if (!$coupon->isCurrentlyValid()) {
            return response()->json(['success' => false, 'message' => "Coupon \"{$coupon->code}\" is not currently valid."], 422);
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "This coupon requires a minimum order of {$coupon->minimum_order_amount}.",
            ], 422);
        }

        $usedByCustomer = \App\Models\Order::where('user_id', $request->input('user_id'))
            ->where('coupon_id', $coupon->id)
            ->when($request->input('order_id'), fn($q) => $q->where('id', '!=', $request->input('order_id')))
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($usedByCustomer >= $coupon->usage_per_user) {
            return response()->json([
                'success' => false,
                'message' => 'This customer has already used this coupon the maximum number of times.',
            ], 422);
        }

        $discount = $coupon->type === 'fixed'
            ? min((float) $coupon->value, $subtotal)
            : min($subtotal * ((float) $coupon->value / 100), $coupon->maximum_discount ?: PHP_FLOAT_MAX, $subtotal);

        return response()->json([
            'success'          => true,
            'code'             => $coupon->code,
            'type'             => $coupon->type,
            'value_label'      => $coupon->value_label,
            'discount_amount'  => round($discount, 2),
        ]);
    }
}
