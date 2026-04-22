<x-admin.layout>
    <x-slot:title>Aufträge</x-slot:title>

    {{-- Status tabs --}}
    <div class="flex gap-1 mb-6 bg-white rounded-lg border border-gray-200 p-1 w-fit">
        @foreach(['alle' => 'Alle', 'bestaetigt' => 'Bestätigt', 'in_bearbeitung' => 'In Bearbeitung', 'versandt' => 'Versandt'] as $key => $label)
            <a href="{{ route('admin.auftraege', ['status' => $key]) }}"
               class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ ($statusFilter ?? 'alle') === $key ? 'bg-blue-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $label }} <span class="text-xs opacity-70">({{ $counts[$key] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Auftrag</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Datum</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Kunde</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Pos.</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Gewicht</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Wert netto</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Marge</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Typ</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Liefertermin</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.auftrag', $order->order_number) }}'">
                            <td class="px-4 py-3 font-medium text-blue-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $order->placed_at->format('d.m.Y H:i') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $order->customer->company_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $order->items->count() }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ number_format($order->total_weight, 0, ',', '.') }} kg</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($order->subtotal_eur, 2, ',', '.') }}&nbsp;€</td>
                            <td class="px-4 py-3 text-right">
                                <span class="{{ $order->margin_pct >= 15 ? 'text-green-700' : ($order->margin_pct >= 12 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ number_format($order->margin_pct, 1, ',', '.') }} %
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($order->fulfillment_type === 'lager')
                                    <span class="text-[10px] font-medium text-green-700 bg-green-50 border border-green-200 px-1.5 py-0.5 rounded">Lager</span>
                                @elseif($order->fulfillment_type === 'misch')
                                    <span class="text-[10px] font-medium text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">Misch</span>
                                @else
                                    <span class="text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Bestell</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $order->requested_delivery_date?->format('d.m.Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @php $sc = $order->getStatusColor(); @endphp
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $sc === 'green' ? 'text-green-700 bg-green-50' : ($sc === 'yellow' ? 'text-amber-700 bg-amber-50' : 'text-blue-700 bg-blue-50') }}">
                                    {{ $order->getStatusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">Keine Aufträge gefunden.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
