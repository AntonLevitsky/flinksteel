<x-app-layout>
    <x-slot:title>Suche: {{ $q }}</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Suchergebnisse</h1>
        @if($q)
            <p class="text-gray-500 mb-8">{{ $products instanceof \Illuminate\Pagination\LengthAwarePaginator ? $products->total() : $products->count() }} Ergebnisse für "<strong>{{ $q }}</strong>"</p>
        @else
            <p class="text-gray-500 mb-8">Geben Sie einen Suchbegriff ein.</p>
        @endif

        @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                    @include('category._product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @elseif($q && ($products->isEmpty()))
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-gray-500">Keine Ergebnisse für "<strong>{{ $q }}</strong>" gefunden.</p>
                <p class="text-sm text-gray-400 mt-2">Versuchen Sie es mit einem anderen Suchbegriff oder durchsuchen Sie unsere Kategorien.</p>
            </div>
        @endif
    </div>
</x-app-layout>
