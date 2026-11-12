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

        return view('visitor.pages.track-order', compact('order'));
    }
}
