<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-sm text-gray-500 mt-1">{{ $category->description }}</p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        <span class="text-sm text-gray-500">{{ $products->total() }} Produkte</span>
        <select data-ajax-sort class="text-sm border-gray-300 rounded-lg focus:border-blue-900 focus:ring-blue-900">
            <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name</option>
            <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Preis aufsteigend</option>
            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Preis absteigend</option>
            <option value="stock" {{ $sort === 'stock' ? 'selected' : '' }}>Verfügbarkeit</option>
        </select>
    </div>
</div>

@if($products->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <p class="text-gray-500">Keine Produkte in dieser Kategorie gefunden.</p>
        <a href="{{ route('category.show', $category->slug) }}" class="text-sm text-blue-900 hover:underline mt-2 inline-block">Filter zurücksetzen</a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($products as $product)
            @include('category._product-card', ['product' => $product])
        @endforeach
    </div>

    <div class="mt-8 pagination">
        {{ $products->links() }}
    </div>
@endif
