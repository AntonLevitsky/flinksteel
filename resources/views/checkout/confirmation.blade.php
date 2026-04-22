<x-app-layout>
    <x-slot:title>Bestellbestätigung</x-slot:title>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Vielen Dank für Ihre Bestellung!</h1>
            <p class="text-gray-600 mt-2">Ihre Bestellung <strong>{{ $order->order_number }}</strong> ist eingegangen.</p>
            <p class="text-sm text-gray-500 mt-1">Eine Bestätigung wurde an <strong>{{ Auth::user()->email }}</strong> gesendet.</p>
        </div>

        <div class="mb-8">
            <x-order-timeline :order="$order" />
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Bestelldetails</h2>
            <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                <div>
                    <span class="text-gray-500">Bestellnummer</span>
                    <p class="font-medium">{{ $order->order_number }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Datum</span>
                    <p class="font-medium">{{ $order->placed_at->format('d.m.Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Wunschliefertermin</span>
                    <p class="font-medium">{{ $order->requested_delivery_date->format('d.m.Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Lieferadresse</span>
                    <p class="font-medium">{{ $order->delivery_street }}, {{ $order->delivery_postal_code }} {{ $order->delivery_city }}</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex justify-between">
                        <div>
                            <p class="text-sm font-medium">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $item->quantity }}x
                                @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                                · {{ number_format($item->weight_kg, 1, ',', '.') }} kg
                            </p>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-200 mt-4 pt-4 space-y-1 text-sm">
                <div class="flex justify-between font-bold text-lg">
                    <span>Gesamtbetrag (brutto)</span>
                    <span>{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;€</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('orders.pdf', $order->order_number) }}" target="_blank" class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-900 text-white rounded-lg hover:bg-blue-800 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Auftragsbestätigung als PDF
            </a>
            <a href="{{ route('orders.show', $order->order_number) }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                Bestellung ansehen
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                Zur Startseite
            </a>
        </div>
    </div>
</x-app-layout>
