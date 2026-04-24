<?php

namespace App\Http\Controllers;

use App\Helpers\ShippingHelper;
use App\Mail\OrderConfirmation;
use App\Models\AnarbeitungOption;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $validated = $request->validate([
            // Delivery
            'delivery_company_name' => 'nullable|string|max:255',
            'delivery_contact_name' => 'nullable|string|max:255',
            'delivery_contact_phone' => 'nullable|string|max:50',
            'delivery_street' => 'required|string|max:255',
            'delivery_postal_code' => 'required|string|max:10',
            'delivery_city' => 'required|string|max:255',
            'delivery_window' => 'nullable|string|max:100',
            'requested_delivery_date' => 'required|date|after:today',
            'shipping_option' => 'required|string',
            // Billing
            'billing_same_as_delivery' => 'nullable|in:0,1',
            'billing_company_name' => 'nullable|string|max:255',
            'billing_street' => 'nullable|string|max:255',
            'billing_postal_code' => 'nullable|string|max:10',
            'billing_city' => 'nullable|string|max:255',
            'billing_vat_id' => 'nullable|string|max:50',
            'billing_email' => 'nullable|email|max:255',
            'po_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'terms_accepted' => 'accepted',
        ]);

        $user = Auth::user();
        $customer = $user->customer;
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

        $shippingOptions = ShippingHelper::getOptions($totalWeight, $itemCount, $validated['delivery_postal_code']);
        $selectedOption = collect($shippingOptions)->firstWhere('code', $validated['shipping_option']);
        $shipping = $selectedOption['cost'] ?? 0;
        $shippingLabel = $selectedOption['label'] ?? ucfirst($validated['shipping_option']);

        $netTotal = $subtotal + $shipping;
        $grossTotal = round($netTotal * 1.19, 2);

        // Billing defaults: when "same as customer" checkbox is checked, use customer stammdaten;
        // otherwise use the override fields (falling back to customer where fields are blank).
        $billingSame = $request->has('billing_same_as_delivery');
        $billingCompany = $billingSame ? $customer->company_name : (($validated['billing_company_name'] ?? null) ?: $customer->company_name);
        $billingStreet = $billingSame ? $customer->street : (($validated['billing_street'] ?? null) ?: $customer->street);
        $billingPostal = $billingSame ? $customer->postal_code : (($validated['billing_postal_code'] ?? null) ?: $customer->postal_code);
        $billingCity = $billingSame ? $customer->city : (($validated['billing_city'] ?? null) ?: $customer->city);
        $billingVat = ($validated['billing_vat_id'] ?? null) ?: $customer->vat_id;
        $billingEmail = ($validated['billing_email'] ?? null) ?: $user->email;

        $paymentTermsDays = $customer->payment_terms_days ?? 30;
        $placedAt = now();
        $paymentDueDate = $placedAt->copy()->addDays($paymentTermsDays)->toDateString();

        $order = Order::create([
            'customer_id' => $user->customer_id,
            'user_id' => $user->id,
            'order_number' => Order::generateOrderNumber(),
            'po_number' => $validated['po_number'] ?? null,
            'status' => 'bestaetigt',
            'subtotal_eur' => $subtotal,
            'anarbeitung_total_eur' => $anarbeitungTotal,
            'certificate_total_eur' => $certificateTotal,
            'shipping_eur' => $shipping,
            'total_eur' => $grossTotal,
            'placed_at' => $placedAt,
            'requested_delivery_date' => $validated['requested_delivery_date'],
            // Delivery
            'delivery_company_name' => $validated['delivery_company_name'] ?? $customer->company_name,
            'delivery_contact_name' => $validated['delivery_contact_name'] ?? $user->name,
            'delivery_contact_phone' => $validated['delivery_contact_phone'] ?? null,
            'delivery_street' => $validated['delivery_street'],
            'delivery_postal_code' => $validated['delivery_postal_code'],
            'delivery_city' => $validated['delivery_city'],
            'delivery_window' => $validated['delivery_window'] ?? null,
            // Billing
            'billing_company_name' => $billingCompany,
            'billing_street' => $billingStreet,
            'billing_postal_code' => $billingPostal,
            'billing_city' => $billingCity,
            'billing_vat_id' => $billingVat,
            'billing_email' => $billingEmail,
            // Payment
            'payment_terms_days' => $paymentTermsDays,
            'payment_due_date' => $paymentDueDate,
            'shipping_option_code' => $validated['shipping_option'],
            'shipping_option_label' => $shippingLabel,
            'notes' => $validated['notes'] ?? null,
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

        try {
            Mail::to($billingEmail)->send(new OrderConfirmation($order->fresh('items', 'customer', 'user')));
        } catch (\Throwable $e) {
            Log::warning("Bestätigungsmail fehlgeschlagen für {$order->order_number}: " . $e->getMessage());
        }

        Log::info("Bestellbestätigung für {$user->email}: Bestellung {$order->order_number}, PO: " . ($validated['po_number'] ?? '-') . ", Gesamtbetrag: {$order->total_eur} EUR, Versandoption: {$validated['shipping_option']}, Zahlungsziel: {$paymentDueDate}");

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items', 'customer', 'user')
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
