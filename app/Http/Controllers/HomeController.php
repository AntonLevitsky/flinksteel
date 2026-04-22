<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->orderBy('sort_order')->get();
        $featured = Product::where('is_featured', true)->where('is_active', true)->where('is_available_for_sale', true)->with('material', 'category')->limit(6)->get();

        return view('home', compact('categories', 'featured'));
    }
}
