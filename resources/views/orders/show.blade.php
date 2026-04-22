<x-app-layout>
    <x-slot:title>Bestellung {{ $order->order_number }}</x-slot:title>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-900">Startseite</a>
            <span class="mx-1">/</span>
            <a href="{{ route('orders.index') }}" class="hover:text-blue-900">Meine Bestellungen</a>
            <span class="mx-1">/</span>
            <span class="text-gray-900 font-medium">{{ $order->order_number }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bestellung {{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500">Aufgegeben am {{ $order->placed_at->format('d.m.Y \u\m H:i') }} Uhr</p>
            </div>
            <div class="flex items-center gap-3">
                @php $color = $order->getStatusColor(); @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $color === 'green' ? 'bg-green-50 text-green-700' : ($color === 'yellow' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-700') }}">
                    {{ $order->getStatusLabel() }}
                </span>
                <a href="{{ route('orders.pdf', $order->order_number) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-blue-900 text-blue-900 rounded-lg hover:bg-blue-50 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </a>
                <form method="POST" action="{{ route('orders.reorder', $order->order_number) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Nachbestellen
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-8">
            <x-order-timeline :order="$order" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Lieferadresse</h3>
                <p class="text-sm text-gray-900">{{ $order->customer->company_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->delivery_street }}</p>
                <p class="text-sm text-gray-600">{{ $order->delivery_postal_code }} {{ $order->delivery_city }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Wunschliefertermin</h3>
                <p class="text-sm text-gray-900">{{ $order->requested_delivery_date->format('d.m.Y') }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Bestellt von</h3>
                <p class="text-sm text-gray-900">{{ $order->user->name ?? 'Unbekannt' }}</p>
                <p class="text-sm text-gray-600">{{ $order->customer->customer_number }}</p>
            </div>
        </div>

        {{-- Line items --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Artikel</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Menge</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Gewicht</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Betrag</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $item->product_sku }} · {{ $item->material_grade }}
                                    @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                                </p>
                                @if(!empty($item->anarbeitung))
                                    <p class="text-xs text-blue-700 mt-0.5">Anarbeitung: {{ implode(', ', $item->anarbeitung) }}</p>
                                @endif
                                @if($item->certificate_code)
                                    <p class="text-xs text-green-700 mt-0.5">Zeugnis {{ $item->certificate_code }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-right">{{ number_format($item->weight_kg, 1, ',', '.') }} kg</td>
                            <td class="px-6 py-4 text-sm text-right font-medium">{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="max-w-xs ml-auto space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Zwischensumme</span>
                    <span>{{ number_format($order->subtotal_eur, 2, ',', '.') }}&nbsp;€</span>
                </div>
                @if($order->anarbeitung_total_eur > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Anarbeitung</span>
                        <span>{{ number_format($order->anarbeitung_total_eur, 2, ',', '.') }}&nbsp;€</span>
                    </div>
                @endif
                @if($order->certificate_total_eur > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Zeugnisse</span>
                        <span>{{ number_format($order->certificate_total_eur, 2, ',', '.') }}&nbsp;€</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Versand</span>
                    <span>{{ $order->shipping_eur > 0 ? number_format($order->shipping_eur, 2, ',', '.') . ' €' : 'kostenfrei' }}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-bold">
                    <span>Gesamt (brutto)</span>
                    <span>{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;€</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
