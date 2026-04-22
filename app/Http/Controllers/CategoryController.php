<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Form;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $children = $category->children;

        ['products' => $products, 'sort' => $sort] = $this->buildProductQuery($request, $category);

        // Available filter options
        $childIds = $children->pluck('id')->push($category->id);
        $availableMaterials = Material::whereHas('products', fn($q) => $q->whereIn('category_id', $childIds))->get();
        $availableForms = Form::whereHas('products', fn($q) => $q->whereIn('category_id', $childIds))->get();

        $breadcrumbs = [];
        if ($category->parent) {
            $breadcrumbs[] = $category->parent;
        }
        $breadcrumbs[] = $category;

        return view('category.show', compact(
            'category', 'children', 'products', 'availableMaterials', 'availableForms', 'breadcrumbs', 'sort'
        ));
    }

    public function showPartial(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        ['products' => $products, 'sort' => $sort] = $this->buildProductQuery($request, $category);

        return view('category._product-grid', compact('products', 'category', 'sort'));
    }

    private function buildProductQuery(Request $request, Category $category): array
    {
        $query = $category->allProducts()->where('is_active', true)->where('is_available_for_sale', true)->with('material', 'form', 'category');

        // Filters
        if ($request->filled('materials')) {
            $query->whereIn('material_id', $request->input('materials'));
        }
        if ($request->filled('forms')) {
            $query->whereIn('form_id', $request->input('forms'));
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity_kg', '>', 0);
        }
        if ($request->boolean('restlaengen')) {
            $query->where('has_restlaengen', true);
        }

        // Sort
        $sort = $request->input('sort', 'name');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price_per_kg_eur', 'asc'),
            'price_desc' => $query->orderBy('price_per_kg_eur', 'desc'),
            'stock' => $query->orderBy('stock_quantity_kg', 'desc'),
            default => $query->orderBy('name'),
        };

        $products = $query->paginate(12)->withQueryString();

        return ['products' => $products, 'sort' => $sort];
    }
}
