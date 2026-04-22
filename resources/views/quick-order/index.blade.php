<x-app-layout>
    <x-slot name="title">Schnellbestellung</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-900">Startseite</a>
            <span class="mx-2">&#8250;</span>
            <span class="text-gray-900">Schnellbestellung</span>
        </nav>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Schnellbestellung</h1>
            <p class="text-gray-500 mt-1">Geben Sie Artikelnummern und Mengen ein, um schnell zu bestellen.</p>
        </div>

        <div id="react-quick-order-pad" data-props='@json([
            "lookupUrl" => route("products.lookup"),
            "addToCartUrl" => route("cart.add"),
            "cartUrl" => route("cart.index"),
            "csrfToken" => csrf_token(),
        ])'></div>
    </div>

    @push('scripts')
        @vite(['resources/js/islands/quick-order-pad.tsx'])
    @endpush
</x-app-layout>
