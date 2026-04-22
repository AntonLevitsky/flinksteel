<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $cartCount = 0;
            if (Auth::check()) {
                $cart = Auth::user()->cart;
                if ($cart) {
                    $cartCount = $cart->items->sum('quantity');
                }
            }
            $view->with('cartCount', $cartCount);

            $megaMenuCategories = Category::whereNull('parent_id')
                ->with(['children' => fn($q) => $q->withCount('products')->orderBy('sort_order')])
                ->withCount('products')
                ->orderBy('sort_order')
                ->get();
            $view->with('megaMenuCategories', $megaMenuCategories);
        });
    }
}
