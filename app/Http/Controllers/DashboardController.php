<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = $user->customer;

        $recentOrders = Order::where('customer_id', $customer->id)
            ->orderBy('placed_at', 'desc')
            ->with('items')
            ->limit(5)
            ->get();

        $totalOrders = Order::where('customer_id', $customer->id)->count();
        $lastOrderDate = Order::where('customer_id', $customer->id)->max('placed_at');

        $featured = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('material', 'category', 'form')
            ->limit(4)
            ->get();

        return view('dashboard', compact('customer', 'recentOrders', 'totalOrders', 'lastOrderDate', 'featured'));
    }
}
