<?php

namespace App\Http\Controllers;

use App\Helpers\ShippingHelper;
use App\Models\AnarbeitungOption;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->getOrCreateCart();
        $items = $cart->items()->with('product.material', 'product.anarbeitungOptions')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $customer = Auth::user()->customer;

        $subtotal = $items->sum('line_total_eur');
        $totalWeight = ShippingHelper::calculateCartWeight($items);
        $itemCount = $items->sum('quantity');

        // Build shipping options based on weight, item count, and PLZ
        $shippingOptions = ShippingHelper::getOptions($totalWeight, $itemCount, $customer->postal_code);
        $defaultOption = $shippingOptions[0] ?? null;
        $shipping = $defaultOption['cost'] ?? 0;

        $netTotal = $subtotal + $shipping;
        $vat = round($netTotal * 0.19, 2);
        $grossTotal = round($netTotal + $vat, 2);

        $hasBestellware = $items->contains(function ($i) { return $i->product->isBestellware(); });
        $hasLagerware = $items->contains(function ($i) { return $i->product->isLagerware(); });
        $isMixedOrder = $hasBestellware && $hasLagerware;

        return view('checkout.index', compact(
            'items', 'customer', 'subtotal', 'totalWeight', 'itemCount',
            'shippingOptions', 'shipping', 'netTotal', 'vat', 'grossTotal',
            'hasBestellware', 'hasLagerware', 'isMixedOrder'
        ));
    }

    public function place(Request $request)
    {
        $request->validate([
            'delivery_street' => 'required|string',
            'delivery_postal_code' => 'required|string',
            'delivery_city' => 'required|string',
            'requested_delivery_date' => 'required|date|after:today',
            'shipping_option' => 'required|string',
        ]);

        $user = Auth::user();
        $cart = $user->getOrCreateCart();
        $items = $cart->items()->with('product.material')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $items->sum('line_total_eur');

        $anarbeitungTotal = 0;
        $certificateTotal = 0;
        foreach ($items as $item) {
            $product = $item->product;
            $weight = $product->calculateWeight($item->quantity, $item->length_mm);

            if ($item->anarbeitung) {
                $options = AnarbeitungOption::whereIn('code', $item->anarbeitung)->get();
                foreach ($options as $opt) {
                    $anarbeitungTotal += $opt->calculateCost($weight, $item->quantity);
                }
            }
            if ($item->certificate_code) {
                $cert = Certificate::where('code', $item->certificate_code)->first();
                if ($cert) {
                    $certificateTotal += $cert->surcharge_eur;
                }
            }
        }

        $totalWeight = ShippingHelper::calculateCartWeight($items);
        $itemCount = $items->sum('quantity');
        $shipping = ShippingHelper::getCostForOption(
            $request->shipping_option,
            $totalWeight,
            $itemCount,
            $request->delivery_postal_code
        );

        $netTotal = $subtotal + $shipping;
        $grossTotal = round($netTotal * 1.19, 2);

        $order = Order::create([
            'customer_id' => $user->customer_id,
            'user_id' => $user->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => 'bestaetigt',
            'subtotal_eur' => $subtotal,
            'anarbeitung_total_eur' => $anarbeitungTotal,
            'certificate_total_eur' => $certificateTotal,
            'shipping_eur' => $shipping,
            'total_eur' => $grossTotal,
            'placed_at' => now(),
            'requested_delivery_date' => $request->requested_delivery_date,
            'delivery_street' => $request->delivery_street,
            'delivery_postal_code' => $request->delivery_postal_code,
            'delivery_city' => $request->delivery_city,
            'notes' => $request->notes,
        ]);

        foreach ($items as $item) {
            $product = $item->product;
            $weight = $product->calculateWeight($item->quantity, $item->length_mm);

            $anarbeitungCost = 0;
            if ($item->anarbeitung) {
                $options = AnarbeitungOption::whereIn('code', $item->anarbeitung)->get();
                foreach ($options as $opt) {
                    $anarbeitungCost += $opt->calculateCost($weight, $item->quantity);
                }
            }

            $certCost = 0;
            if ($item->certificate_code) {
                $cert = Certificate::where('code', $item->certificate_code)->first();
                if ($cert) $certCost = $cert->surcharge_eur;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'material_grade' => $product->material->grade,
                'quantity' => $item->quantity,
                'length_mm' => $item->length_mm,
                'anarbeitung' => $item->anarbeitung,
                'certificate_code' => $item->certificate_code,
                'unit_price_eur' => $item->unit_price_eur,
                'anarbeitung_cost_eur' => $anarbeitungCost,
                'certificate_cost_eur' => $certCost,
                'line_total_eur' => $item->line_total_eur,
                'weight_kg' => $weight,
            ]);
        }

        $cart->items()->delete();

        Log::info("Bestellbestätigung für {$user->email}: Bestellung {$order->order_number}, Gesamtbetrag: {$order->total_eur} EUR, Versandoption: {$request->shipping_option}");

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items')
            ->firstOrFail();

        return view('checkout.confirmation', compact('order'));
    }

    public function pdf(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items', 'customer', 'user')
            ->firstOrFail();

        return view('orders.pdf', compact('order'));
    }
}
