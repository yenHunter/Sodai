<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderConfirmController extends Controller
{
    public function show(Request $request)
    {
        $orderId = $request->session()->get('last_order_id');

        $order = $orderId
            ? Order::with('items')->find($orderId)
            : null;

        return view('visitor.pages.order-confirm', compact('order'));
    }
}
