<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function cockpit()
    {
        // KPI tiles
        $todayOrders = Order::whereDate('placed_at', today())->get();
        $todayRevenue = $todayOrders->sum('total_eur');
        $todayCount = $todayOrders->count();
        $todayWeight = OrderItem::whereIn('order_id', $todayOrders->pluck('id'))->sum('weight_kg');

        $openOrders = Order::whereIn('status', ['bestaetigt', 'in_bearbeitung'])->count();

        $totalStockKg = Product::where('stock_quantity_kg', '>', 0)->sum('stock_quantity_kg');
        $stockValueEur = Product::where('stock_quantity_kg', '>', 0)
            ->selectRaw('SUM(stock_quantity_kg * price_per_kg_eur * 0.82) as val')->value('val') ?? 0;

        $monthOrders = Order::whereMonth('placed_at', now()->month)->whereYear('placed_at', now()->year)->get();
        $monthRevenue = $monthOrders->sum('total_eur');
        $monthSubtotal = $monthOrders->sum('subtotal_eur');
        // Simulated cost basis: ~82% of subtotal for Lagerware, ~88% for Bestellware
        $monthCost = $monthSubtotal * 0.83;
        $grossMarginPct = $monthSubtotal > 0 ? round(($monthSubtotal - $monthCost) / $monthSubtotal * 100, 1) : 0;

        // Trend KPIs (simulated month-over-month)
        $kpis = [
            'lagerumschlag' => ['value' => '4,2x', 'delta' => '+0,3', 'up' => true],
            'liefertreue' => ['value' => '94,7 %', 'delta' => '+1,2 %', 'up' => true],
            'avg_order' => ['value' => number_format($monthOrders->count() > 0 ? $monthRevenue / $monthOrders->count() : 0, 0, ',', '.') . ' €', 'delta' => '+8 %', 'up' => true],
            'bestandsreichweite' => ['value' => '32 Tage', 'delta' => '-3 Tage', 'up' => false],
        ];

        // Low stock alerts (simulated Meldebestand = 20% of max stock across products)
        $lowStock = Product::where('is_active', true)
            ->where('stock_quantity_kg', '>', 0)
            ->where('stock_quantity_kg', '<', 2000)
            ->orderBy('stock_quantity_kg')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with('customer', 'items.product')
            ->orderBy('placed_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($order) {
                $hasLager = $order->items->contains(fn($i) => $i->product && $i->product->isLagerware());
                $hasBestell = $order->items->contains(fn($i) => $i->product && $i->product->isBestellware());
                $order->fulfillment_type = $hasLager && $hasBestell ? 'misch' : ($hasBestell ? 'bestell' : 'lager');
                $order->total_weight = $order->items->sum('weight_kg');
                return $order;
            });

        // Pending supplier orders (simulated from Bestellware in recent orders)
        $supplierPOs = Order::whereIn('status', ['bestaetigt', 'in_bearbeitung'])
            ->with('items.product')
            ->get()
            ->flatMap(function ($order) {
                return $order->items->filter(fn($i) => $i->product && $i->product->isBestellware())->map(function ($item) use ($order) {
                    return (object)[
                        'po_number' => 'LB-' . str_pad($item->id + 380, 4, '0', STR_PAD_LEFT),
                        'supplier' => $item->product->supplier_name ?? 'Unbekannt',
                        'product_name' => $item->product_name,
                        'weight_kg' => $item->weight_kg,
                        'customer_order' => $order->order_number,
                        'expected_date' => $order->placed_at->addDays(rand(4, 8)),
                        'is_overdue' => $order->placed_at->addDays(6)->lt(now()),
                    ];
                });
            })->take(5);

        return view('admin.cockpit', compact(
            'todayRevenue', 'todayCount', 'todayWeight', 'openOrders',
            'totalStockKg', 'stockValueEur', 'monthRevenue', 'grossMarginPct',
            'kpis', 'lowStock', 'recentOrders', 'supplierPOs'
        ));
    }

    public function auftraege()
    {
        $statusFilter = request('status');

        $query = Order::with('customer', 'items.product')->orderBy('placed_at', 'desc');

        if ($statusFilter && $statusFilter !== 'alle') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->get()->map(function ($order) {
            $hasLager = $order->items->contains(fn($i) => $i->product && $i->product->isLagerware());
            $hasBestell = $order->items->contains(fn($i) => $i->product && $i->product->isBestellware());
            $order->fulfillment_type = $hasLager && $hasBestell ? 'misch' : ($hasBestell ? 'bestell' : 'lager');
            $order->total_weight = $order->items->sum('weight_kg');
            // Simulated margin: Lagerware ~18%, Bestellware ~12%
            $order->margin_pct = $order->fulfillment_type === 'bestell' ? 12.4 : ($order->fulfillment_type === 'misch' ? 15.1 : 17.8);
            $order->margin_eur = round($order->subtotal_eur * $order->margin_pct / 100, 2);
            return $order;
        });

        $counts = [
            'alle' => Order::count(),
            'bestaetigt' => Order::where('status', 'bestaetigt')->count(),
            'in_bearbeitung' => Order::where('status', 'in_bearbeitung')->count(),
            'versandt' => Order::where('status', 'versandt')->count(),
        ];

        return view('admin.auftraege', compact('orders', 'counts', 'statusFilter'));
    }

    public function auftragDetail(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('customer', 'user', 'items.product.material')
            ->firstOrFail();

        $order->total_weight = $order->items->sum('weight_kg');

        // Per-item margin calculation
        $order->items->each(function ($item) {
            $ekMultiplier = ($item->product && $item->product->isBestellware()) ? 0.88 : 0.82;
            $item->ek_price = round($item->line_total_eur * $ekMultiplier, 2);
            $item->margin_eur = round($item->line_total_eur - $item->ek_price, 2);
            $item->margin_pct = $item->line_total_eur > 0 ? round($item->margin_eur / $item->line_total_eur * 100, 1) : 0;
            $item->is_lagerware = $item->product ? $item->product->isLagerware() : true;
        });

        $totalMarginEur = $order->items->sum('margin_eur');
        $totalMarginPct = $order->subtotal_eur > 0 ? round($totalMarginEur / $order->subtotal_eur * 100, 1) : 0;

        return view('admin.auftrag-detail', compact('order', 'totalMarginEur', 'totalMarginPct'));
    }

    public function lager()
    {
        $products = Product::with('material', 'form', 'category')
            ->where('is_active', true)
            ->orderByDesc('stock_quantity_kg')
            ->get()
            ->map(function ($p) {
                $p->ek_per_kg = round($p->price_per_kg_eur * 0.82, 2);
                $p->margin_pct = round(($p->price_per_kg_eur - $p->ek_per_kg) / $p->price_per_kg_eur * 100, 1);
                $p->stock_value = round($p->stock_quantity_kg * $p->ek_per_kg, 2);
                // Simulated Meldebestand: roughly proportional to typical order sizes
                $p->meldebestand = $p->isLagerware() ? max(500, round($p->stock_quantity_kg * 0.25, -2)) : 0;
                $p->is_low = $p->isLagerware() && $p->stock_quantity_kg < $p->meldebestand;
                return $p;
            });

        $totalStockKg = $products->where('stock_quantity_kg', '>', 0)->sum('stock_quantity_kg');
        $totalStockValue = $products->sum('stock_value');
        $lowStockCount = $products->where('is_low', true)->count();
        $lagerwareCount = $products->where('stock_quantity_kg', '>', 0)->count();
        $bestellwareCount = $products->where('stock_quantity_kg', '<=', 0)->count();

        return view('admin.lager', compact('products', 'totalStockKg', 'totalStockValue', 'lowStockCount', 'lagerwareCount', 'bestellwareCount'));
    }

    public function produkte()
    {
        $categoryFilter = request('kategorie');
        $stockFilter = request('bestand'); // lager, bestell, alle
        $saleFilter = request('verkauf'); // aktiv, inaktiv, alle

        $query = Product::with('material', 'form', 'category')
            ->where('is_active', true)
            ->orderBy('name');

        if ($categoryFilter) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categoryFilter));
        }
        if ($stockFilter === 'lager') {
            $query->where('stock_quantity_kg', '>', 0);
        } elseif ($stockFilter === 'bestell') {
            $query->where('stock_quantity_kg', '<=', 0);
        }
        if ($saleFilter === 'aktiv') {
            $query->where('is_available_for_sale', true);
        } elseif ($saleFilter === 'inaktiv') {
            $query->where('is_available_for_sale', false);
        }

        $products = $query->get()->map(function ($p) {
            $erpPrice = $p->erp_price_per_kg ?? round($p->price_per_kg_eur * 0.82, 4);
            $p->current_margin = $p->price_per_kg_eur > 0
                ? round(($p->price_per_kg_eur - $erpPrice) / $p->price_per_kg_eur * 100, 1)
                : 0;
            $p->erp_source_label = $p->getErpSourceLabel();
            return $p;
        });

        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        $counts = [
            'total' => Product::where('is_active', true)->count(),
            'lager' => Product::where('is_active', true)->where('stock_quantity_kg', '>', 0)->count(),
            'bestell' => Product::where('is_active', true)->where('stock_quantity_kg', '<=', 0)->count(),
            'for_sale' => Product::where('is_active', true)->where('is_available_for_sale', true)->count(),
            'not_for_sale' => Product::where('is_active', true)->where('is_available_for_sale', false)->count(),
            'partner' => Product::where('is_active', true)->where('is_partner_network', true)->count(),
        ];

        return view('admin.produkte', compact('products', 'categories', 'counts', 'categoryFilter', 'stockFilter', 'saleFilter'));
    }

    public function produktDetail(int $id)
    {
        $product = Product::with('material', 'form', 'category', 'anarbeitungOptions')->findOrFail($id);

        $aiSuggestions = $product->getAiPriceSuggestions();

        // Order history for this product
        $orderHistory = OrderItem::where('product_id', $product->id)
            ->with('order.customer')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $totalSold = OrderItem::where('product_id', $product->id)->sum('weight_kg');
        $totalRevenue = OrderItem::where('product_id', $product->id)->sum('line_total_eur');

        return view('admin.produkt-detail', compact('product', 'aiSuggestions', 'orderHistory', 'totalSold', 'totalRevenue'));
    }

    public function produktUpdate(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'price_per_kg_eur' => 'required|numeric|min:0.01',
            'is_available_for_sale' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string|max:5000',
        ]);

        $product->update([
            'price_per_kg_eur' => $validated['price_per_kg_eur'],
            'is_available_for_sale' => $request->boolean('is_available_for_sale'),
            'is_featured' => $request->boolean('is_featured'),
            'short_description' => $validated['short_description'] ?? $product->short_description,
            'long_description' => $validated['long_description'] ?? $product->long_description,
        ]);

        return redirect()->route('admin.produkt', $product->id)->with('success', 'Produkt wurde aktualisiert.');
    }

    public function kunden()
    {
        $customers = Customer::with(['orders.items', 'users'])->get()->map(function ($c) {
            $c->order_count = $c->orders->count();
            $c->revenue_ytd = $c->orders->where('placed_at', '>=', now()->startOfYear())->sum('total_eur');
            $c->avg_order = $c->order_count > 0 ? round($c->revenue_ytd / max($c->order_count, 1), 2) : 0;
            $c->last_order_date = $c->orders->sortByDesc('placed_at')->first()?->placed_at;
            $c->credit_used = $c->orders->whereIn('status', ['bestaetigt', 'in_bearbeitung'])->sum('total_eur');
            $c->credit_pct = $c->credit_limit_eur > 0 ? round($c->credit_used / $c->credit_limit_eur * 100, 1) : 0;
            return $c;
        })->sortByDesc('revenue_ytd');

        $totalRevenueYtd = $customers->sum('revenue_ytd');
        $totalOrders = $customers->sum('order_count');
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenueYtd / $totalOrders, 2) : 0;

        return view('admin.kunden', compact('customers', 'totalRevenueYtd', 'totalOrders', 'avgOrderValue'));
    }

    public function kundeDetail(int $id)
    {
        $customer = Customer::with(['orders.items.product', 'users'])->findOrFail($id);

        $customer->revenue_ytd = $customer->orders->where('placed_at', '>=', now()->startOfYear())->sum('total_eur');
        $customer->order_count = $customer->orders->count();
        $customer->avg_order = $customer->order_count > 0 ? round($customer->revenue_ytd / $customer->order_count, 2) : 0;

        // Top products for this customer
        $topProducts = $customer->orders->flatMap->items
            ->groupBy('product_name')
            ->map(fn($items) => (object)[
                'name' => $items->first()->product_name,
                'total_qty' => $items->sum('quantity'),
                'total_eur' => $items->sum('line_total_eur'),
                'total_kg' => $items->sum('weight_kg'),
            ])
            ->sortByDesc('total_eur')
            ->take(5);

        $recentOrders = $customer->orders->sortByDesc('placed_at')->take(10);

        return view('admin.kunde-detail', compact('customer', 'topProducts', 'recentOrders'));
    }

    public function statistik()
    {
        $allOrders = Order::with('items.product')->get();

        // Period summaries
        $periods = [
            'heute' => $allOrders->where('placed_at', '>=', today()),
            'woche' => $allOrders->where('placed_at', '>=', now()->startOfWeek()),
            'monat' => $allOrders->where('placed_at', '>=', now()->startOfMonth()),
            'jahr' => $allOrders->where('placed_at', '>=', now()->startOfYear()),
        ];

        $periodStats = [];
        foreach ($periods as $key => $orders) {
            $periodStats[$key] = [
                'revenue' => $orders->sum('total_eur'),
                'count' => $orders->count(),
                'weight' => $orders->flatMap->items->sum('weight_kg'),
            ];
        }

        // Monthly revenue (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $rev = $allOrders
                ->where('placed_at', '>=', $month->copy()->startOfMonth())
                ->where('placed_at', '<', $month->copy()->endOfMonth())
                ->sum('total_eur');
            $monthlyRevenue[] = [
                'label' => $month->translatedFormat('M Y'),
                'short' => $month->translatedFormat('M'),
                'revenue' => $rev,
            ];
        }
        $maxMonthly = max(array_column($monthlyRevenue, 'revenue') ?: [1]);

        // Top products
        $topProducts = OrderItem::selectRaw('product_name, SUM(line_total_eur) as total, SUM(weight_kg) as weight, SUM(quantity) as qty')
            ->groupBy('product_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Top customers
        $topCustomers = Order::with('customer')
            ->selectRaw('customer_id, SUM(total_eur) as total, COUNT(*) as order_count')
            ->groupBy('customer_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Lagerware vs Bestellware split
        $lagerItems = $allOrders->flatMap->items->filter(fn($i) => $i->product && $i->product->isLagerware());
        $bestellItems = $allOrders->flatMap->items->filter(fn($i) => $i->product && $i->product->isBestellware());
        $lagerRevenue = $lagerItems->sum('line_total_eur');
        $bestellRevenue = $bestellItems->sum('line_total_eur');
        $totalItemRevenue = $lagerRevenue + $bestellRevenue;

        return view('admin.statistik', compact(
            'periodStats', 'monthlyRevenue', 'maxMonthly',
            'topProducts', 'topCustomers',
            'lagerRevenue', 'bestellRevenue', 'totalItemRevenue'
        ));
    }
}
