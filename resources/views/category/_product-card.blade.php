@php $cardCustomer = Auth::user()?->customer; @endphp
<a href="{{ route('product.show', $product->id) }}" class="group bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all overflow-hidden flex flex-col">
    <div class="aspect-[4/3] bg-gray-50 p-6 flex items-center justify-center relative">
        <x-product-svg :form="$product->form->slug" class="w-24 h-24 opacity-70 group-hover:opacity-100 transition-opacity" />
        @if($product->isBestellware())
            <span class="absolute top-3 right-3 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Bestellware</span>
        @endif
        @if($product->is_partner_network)
            <span class="absolute top-3 left-3 text-[10px] font-medium text-purple-700 bg-purple-50 border border-purple-200 px-1.5 py-0.5 rounded">Netzwerk</span>
        @endif
    </div>
    <div class="p-4 flex-1 flex flex-col">
        <div class="flex flex-wrap gap-1 mb-2">
            <span class="text-xs font-medium text-blue-900 bg-blue-50 px-2 py-0.5 rounded">{{ $product->material->grade }}</span>
            @if($product->has_restlaengen)
                <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Restlängen</span>
            @endif
        </div>
        <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors line-clamp-2">{{ $product->name }}</h3>
        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $product->short_description }}</p>
        <div class="mt-auto pt-3 flex items-center justify-between">
            @php $cardPrice = $product->getPriceForCustomer($cardCustomer); @endphp
            @if($cardCustomer && $cardCustomer->price_multiplier < 1.0)
                <div>
                    <span class="text-sm font-bold text-gray-900">ab {{ number_format($cardPrice, 2, ',', '.') }}&nbsp;€/kg</span>
                    <span class="text-[10px] text-green-600 ml-1">Ihr Preis</span>
                </div>
            @else
                <span class="text-sm font-bold text-gray-900">ab {{ number_format($cardPrice, 2, ',', '.') }}&nbsp;€/kg</span>
            @endif
            @if($product->isLagerware())
                <span class="text-xs px-2 py-0.5 rounded-full {{ $product->stock_quantity_kg > 500 ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                    {{ $product->getStockStatus() }}
                </span>
            @else
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">5–10 Werktage</span>
            @endif
        </div>
    </div>
</a>
