<?php

namespace App\Http\Controllers;

use App\Models\AnarbeitungOption;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('placed_at', 'desc')
            ->with('items')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items', 'customer')
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    public function reorder(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items')
            ->firstOrFail();

        $cart = Auth::user()->getOrCreateCart();

        foreach ($order->items as $orderItem) {
            if (!$orderItem->product_id || !Product::find($orderItem->product_id)) {
                continue;
            }

            $cartItem = $cart->items()->create([
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
                'length_mm' => $orderItem->length_mm,
                'anarbeitung' => $orderItem->anarbeitung ?? [],
                'certificate_code' => $orderItem->certificate_code,
                'unit_price_eur' => 0,
                'line_total_eur' => 0,
            ]);

            $cartItem->recalculate();
        }

        return redirect()->route('cart.index')->with('success', 'Artikel aus Bestellung ' . $order->order_number . ' wurden in den Warenkorb gelegt.');
    }
}
