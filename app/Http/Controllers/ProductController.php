<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show(int $id)
    {
        $product = Product::with(['material', 'form', 'category.parent', 'anarbeitungOptions'])->findOrFail($id);
        $certificates = Certificate::whereIn('code', $product->certifications_available ?? ['2.2'])->get();

        $customer = Auth::user()->customer;
        $customerPrice = $product->getPriceForCustomer($customer);

        $relatedProducts = $product->getRelatedProducts(4);
        $supplierSourcing = $product->getSupplierSourcing();

        $breadcrumbs = [];
        if ($product->category->parent) {
            $breadcrumbs[] = $product->category->parent;
        }
        $breadcrumbs[] = $product->category;

        return view('product.show', compact(
            'product', 'certificates', 'breadcrumbs',
            'customer', 'customerPrice', 'relatedProducts', 'supplierSourcing'
        ));
    }
}
