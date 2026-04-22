<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickOrderController extends Controller
{
    public function index()
    {
        return view('quick-order.index');
    }

    public function lookupProduct(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $customer = Auth::user()->customer;

        $products = Product::where('is_active', true)
            ->where('is_available_for_sale', true)
            ->where(function ($query) use ($q) {
                $query->where('sku', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->with('material', 'form')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'material_grade' => $p->material->grade,
                'price_per_kg' => $customer ? $p->getPriceForCustomer($customer) : $p->price_per_kg_eur,
                'stock_status' => $p->getStockStatus(),
                'stock_quantity_kg' => (float)$p->stock_quantity_kg,
                'is_cut_to_length' => (bool)$p->is_cut_to_length,
                'weight_per_piece_kg' => (float)$p->weight_per_piece_kg,
                'weight_per_meter_kg' => (float)$p->weight_per_meter_kg,
                'standard_length_mm' => $p->standard_length_mm,
            ]);

        return response()->json($products);
    }
}
