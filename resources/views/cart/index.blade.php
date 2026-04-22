<x-app-layout>
    <x-slot:title>Warenkorb</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Warenkorb</h1>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @php
            $cartProps = [
                'items' => $items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_sku' => $item->product->sku,
                        'material_grade' => $item->product->material->grade,
                        'form_slug' => $item->product->form->slug,
                        'quantity' => $item->quantity,
                        'length_mm' => $item->length_mm,
                        'is_cut_to_length' => $item->product->is_cut_to_length,
                        'anarbeitung' => $item->anarbeitung ?? [],
                        'certificate_code' => $item->certificate_code,
                        'unit_price_eur' => (float)$item->unit_price_eur,
                        'line_total_eur' => (float)$item->line_total_eur,
                        'is_bestellware' => $item->product->isBestellware(),
                        'delivery_days' => $item->product->getDeliveryDays(),
                    ];
                })->values(),
                'checkoutUrl' => route('checkout.index'),
                'homeUrl' => route('home'),
                'angebotUrl' => route('angebot.generate'),
                'csrfToken' => csrf_token(),
            ];
        @endphp
        <div id="react-cart-table" data-props='@json($cartProps)'></div>
    </div>

    @push('scripts')
        @vite(['resources/js/islands/cart-table.tsx'])
    @endpush
</x-app-layout>
