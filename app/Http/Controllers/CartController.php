<?php

namespace App\Http\Controllers;

use App\Models\AnarbeitungOption;
use App\Models\CartItem;
use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->getOrCreateCart();
        $items = $cart->items()->with('product.material', 'product.form', 'product.anarbeitungOptions')->get();

        return view('cart.index', compact('cart', 'items'));
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'length_mm' => 'nullable|integer|min:1',
            'anarbeitung' => 'nullable|array',
            'certificate_code' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = Auth::user()->getOrCreateCart();

        $item = $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'length_mm' => $request->length_mm,
            'anarbeitung' => $request->anarbeitung ?? [],
            'certificate_code' => $request->certificate_code,
            'unit_price_eur' => 0,
            'line_total_eur' => 0,
        ]);

        $item->recalculate();

        return response()->json([
            'success' => true,
            'item' => $item->load('product'),
            'cart_count' => $cart->fresh()->items->sum('quantity'),
            'cart_total' => $cart->fresh()->getSubtotal(),
        ]);
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $cart = Auth::user()->getOrCreateCart();
        $item = $cart->items()->findOrFail($id);

        if ($request->has('quantity')) {
            $item->quantity = max(1, (int) $request->quantity);
        }
        if ($request->has('length_mm')) {
            $item->length_mm = $request->length_mm;
        }
        if ($request->has('anarbeitung')) {
            $item->anarbeitung = $request->anarbeitung;
        }
        if ($request->has('certificate_code')) {
            $item->certificate_code = $request->certificate_code;
        }

        $item->save();
        $item->recalculate();

        return response()->json([
            'success' => true,
            'item' => $item->fresh()->load('product'),
            'cart_count' => $cart->fresh()->items->sum('quantity'),
            'cart_total' => $cart->fresh()->getSubtotal(),
        ]);
    }

    public function removeItem(int $id): JsonResponse
    {
        $cart = Auth::user()->getOrCreateCart();
        $cart->items()->findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'cart_count' => $cart->fresh()->items->sum('quantity'),
            'cart_total' => $cart->fresh()->getSubtotal(),
        ]);
    }

    public function summary(): JsonResponse
    {
        $cart = Auth::user()->cart;
        $count = $cart ? $cart->items->sum('quantity') : 0;
        $total = $cart ? $cart->getSubtotal() : 0;

        return response()->json([
            'count' => $count,
            'total' => $total,
        ]);
    }
}
