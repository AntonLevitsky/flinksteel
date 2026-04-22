<?php

namespace App\Http\Controllers;

use App\Helpers\ShippingHelper;
use Illuminate\Support\Facades\Auth;

class AngebotController extends Controller
{
    public function generate()
    {
        $cart = Auth::user()->getOrCreateCart();
        $items = $cart->items()->with('product.material', 'product.form', 'product.anarbeitungOptions')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $customer = Auth::user()->customer;
        $user = Auth::user();
        $subtotal = $items->sum('line_total_eur');
        $totalWeight = ShippingHelper::calculateCartWeight($items);
        $angebotNr = 'AG-' . date('Y') . sprintf('%05d', rand(1, 99999));
        $validUntil = now()->addDays(14);

        return view('angebot.print', compact(
            'items', 'customer', 'user', 'subtotal', 'totalWeight', 'angebotNr', 'validUntil'
        ));
    }
}
