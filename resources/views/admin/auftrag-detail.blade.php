<x-admin.layout>
    <x-slot:title>Auftrag {{ $order->order_number }}</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.auftraege') }}" class="text-sm text-gray-500 hover:text-blue-900">← Zurück zur Auftragsliste</a>
    </div>

    {{-- Order header --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Auftragsnummer</div>
            <div class="text-lg font-bold text-blue-900">{{ $order->order_number }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Kunde</div>
            <div class="text-sm font-semibold">{{ $order->customer->company_name ?? '—' }}</div>
            <div class="text-[10px] text-gray-400">{{ $order->customer->customer_number ?? '' }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Wert / Gewicht</div>
            <div class="text-sm font-bold">{{ number_format($order->subtotal_eur, 2, ',', '.') }} €</div>
            <div class="text-[10px] text-gray-400">{{ number_format($order->total_weight, 0, ',', '.') }} kg</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Marge</div>
            <div class="text-sm font-bold {{ $totalMarginPct >= 15 ? 'text-green-700' : 'text-amber-600' }}">
                {{ number_format($totalMarginEur, 2, ',', '.') }} € ({{ $totalMarginPct }} %)
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Status</div>
            @php $sc = $order->getStatusColor(); @endphp
            <span class="inline-flex items-center mt-1 text-xs font-medium px-2.5 py-1 rounded-full {{ $sc === 'green' ? 'text-green-700 bg-green-50' : ($sc === 'yellow' ? 'text-amber-700 bg-amber-50' : 'text-blue-700 bg-blue-50') }}">
                {{ $order->getStatusLabel() }}
            </span>
        </div>
    </div>

    {{-- Delivery info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm">
            <div class="text-[10px] text-gray-500 uppercase mb-1">Lieferadresse</div>
            <div>{{ $order->delivery_street }}</div>
            <div>{{ $order->delivery_postal_code }} {{ $order->delivery_city }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm">
            <div class="text-[10px] text-gray-500 uppercase mb-1">Wunschliefertermin</div>
            <div class="font-medium">{{ $order->requested_delivery_date?->format('d.m.Y') ?? '—' }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm">
            <div class="text-[10px] text-gray-500 uppercase mb-1">Bestellt durch</div>
            <div>{{ $order->user->name ?? '—' }}</div>
            <div class="text-gray-400 text-xs">{{ $order->placed_at->format('d.m.Y, H:i') }} Uhr</div>
        </div>
    </div>

    {{-- Line items with margin --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Positionen ({{ $order->items->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Pos.</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Artikel</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Verfügb.</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Menge</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Gewicht</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">VK</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">EK (kalk.)</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Marge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($order->items as $idx => $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $item->product_sku }} · {{ $item->material_grade }}
                                    @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                                    @if(!empty($item->anarbeitung)) · {{ implode(', ', $item->anarbeitung) }} @endif
                                    @if($item->certificate_code) · Zeugnis {{ $item->certificate_code }} @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->is_lagerware)
                                    <span class="text-[10px] font-medium text-green-700 bg-green-50 border border-green-200 px-1.5 py-0.5 rounded">Lager</span>
                                @else
                                    <span class="text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Bestell</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ number_format($item->weight_kg, 1, ',', '.') }} kg</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ number_format($item->ek_price, 2, ',', '.') }}&nbsp;€</td>
                            <td class="px-4 py-3 text-right">
                                <span class="{{ $item->margin_pct >= 15 ? 'text-green-700' : ($item->margin_pct >= 10 ? 'text-amber-600' : 'text-red-600') }} font-medium">
                                    {{ number_format($item->margin_eur, 2, ',', '.') }}&nbsp;€
                                </span>
                                <span class="text-xs text-gray-400 ml-1">({{ $item->margin_pct }} %)</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr class="font-semibold">
                        <td colspan="5" class="px-4 py-3 text-right text-gray-500">Gesamt</td>
                        <td class="px-4 py-3 text-right">{{ number_format($order->subtotal_eur, 2, ',', '.') }}&nbsp;€</td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ number_format($order->subtotal_eur - $totalMarginEur, 2, ',', '.') }}&nbsp;€</td>
                        <td class="px-4 py-3 text-right {{ $totalMarginPct >= 15 ? 'text-green-700' : 'text-amber-600' }}">
                            {{ number_format($totalMarginEur, 2, ',', '.') }}&nbsp;€ ({{ $totalMarginPct }} %)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-admin.layout>
