<x-admin.layout>
    <x-slot:title>Lagerbestand</x-slot:title>

    {{-- Summary tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Gesamtbestand</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($totalStockKg / 1000, 1, ',', '.') }}&nbsp;t</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Bestandswert (EK)</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($totalStockValue, 0, ',', '.') }}&nbsp;€</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Lagerartikel</div>
            <div class="text-xl font-bold text-green-700">{{ $lagerwareCount }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Bestellware</div>
            <div class="text-xl font-bold text-blue-700">{{ $bestellwareCount }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Unter Meldebestand</div>
            <div class="text-xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $lowStockCount }}</div>
        </div>
    </div>

    {{-- Low stock alerts --}}
    @php $lowStockProducts = $products->where('is_low', true); @endphp
    @if($lowStockProducts->isNotEmpty())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-semibold text-red-800 mb-3">Bestandswarnungen — unter Meldebestand</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($lowStockProducts as $p)
                    <div class="bg-white rounded-lg border border-red-200 p-3">
                        <div class="text-xs font-semibold text-gray-900">{{ $p->name }}</div>
                        <div class="flex justify-between items-end mt-1">
                            <div>
                                <span class="text-sm font-bold text-red-600">{{ number_format($p->stock_quantity_kg, 0, ',', '.') }} kg</span>
                                <span class="text-[10px] text-gray-400"> / Melde: {{ number_format($p->meldebestand, 0, ',', '.') }} kg</span>
                            </div>
                            <span class="text-[10px] text-red-600 font-medium">Nachbestellen</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Full stock table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Artikel</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Güte</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Typ</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Bestand</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">VK €/kg</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">EK €/kg</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Marge</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Best.-Wert</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($products as $p)
                        <tr class="{{ $p->is_low ? 'bg-red-50/50' : '' }}">
                            <td class="px-3 py-2.5">
                                <div class="font-medium text-gray-900 text-xs">{{ $p->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $p->sku }}</div>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-gray-600">{{ $p->material->grade ?? '' }}</td>
                            <td class="px-3 py-2.5 text-center">
                                @if($p->isLagerware())
                                    <span class="text-[10px] font-medium text-green-700 bg-green-50 px-1.5 py-0.5 rounded">Lager</span>
                                @else
                                    <span class="text-[10px] font-medium text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">Bestell</span>
                                @endif
                                @if($p->is_partner_network)
                                    <span class="text-[10px] font-medium text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded ml-0.5">Netzw.</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs {{ $p->is_low ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                {{ $p->stock_quantity_kg > 0 ? number_format($p->stock_quantity_kg, 0, ',', '.') . ' kg' : '—' }}
                            </td>
                            <td class="px-3 py-2.5 text-right text-xs">{{ number_format($p->price_per_kg_eur, 2, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right text-xs text-gray-500">{{ number_format($p->ek_per_kg, 2, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right text-xs {{ $p->margin_pct >= 15 ? 'text-green-700' : 'text-amber-600' }}">{{ $p->margin_pct }} %</td>
                            <td class="px-3 py-2.5 text-right text-xs text-gray-500">{{ $p->stock_value > 0 ? number_format($p->stock_value, 0, ',', '.') . ' €' : '—' }}</td>
                            <td class="px-3 py-2.5 text-center">
                                @if($p->is_low)
                                    <span class="text-[10px] font-medium text-red-600 bg-red-50 border border-red-200 px-1.5 py-0.5 rounded">Kritisch</span>
                                @elseif($p->isBestellware())
                                    <span class="text-[10px] text-gray-400">Auf Abruf</span>
                                @else
                                    <span class="text-[10px] text-green-600">OK</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
