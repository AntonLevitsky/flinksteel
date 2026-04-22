<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q', '');
        $products = collect();

        if (strlen($q) >= 2) {
            $products = Product::where('is_active', true)
                ->where('is_available_for_sale', true)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhereHas('material', fn($mq) => $mq->where('grade', 'like', "%{$q}%"));
                })
                ->with('material', 'category')
                ->paginate(12)
                ->withQueryString();
        }

        return view('search.index', compact('products', 'q'));
    }

    public function suggest(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where('is_available_for_sale', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhereHas('material', fn($mq) => $mq->where('grade', 'like', "%{$q}%"));
            })
            ->with('material', 'category', 'form')
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'material_grade' => $p->material->grade,
                'category_name' => $p->category->name,
                'form_slug' => $p->form->slug,
                'price_per_kg_eur' => (float)$p->price_per_kg_eur,
                'stock_status' => $p->getStockStatus(),
                'url' => route('product.show', $p->id),
            ]);

        return response()->json($products);
    }
}
