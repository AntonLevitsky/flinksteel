<x-admin.layout>
    <x-slot:title>Statistik</x-slot:title>

    {{-- Period summary tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach(['heute' => 'Heute', 'woche' => 'Woche', 'monat' => 'Monat', 'jahr' => 'Jahr'] as $key => $label)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="text-[10px] text-gray-500 uppercase font-medium">{{ $label }}</div>
                <div class="text-xl font-bold text-gray-900 mt-1">{{ number_format($periodStats[$key]['revenue'], 0, ',', '.') }}&nbsp;€</div>
                <div class="text-[10px] text-gray-400">{{ $periodStats[$key]['count'] }} Aufträge · {{ number_format($periodStats[$key]['weight'] / 1000, 1, ',', '.') }} t</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Monthly revenue chart (CSS bars) --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Umsatz nach Monat</h3>
            <div class="space-y-3">
                @foreach($monthlyRevenue as $m)
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-10 shrink-0">{{ $m['short'] }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-5 relative">
                            @php $pct = $maxMonthly > 0 ? ($m['revenue'] / $maxMonthly * 100) : 0; @endphp
                            <div class="h-5 rounded-full bg-blue-900 flex items-center justify-end pr-2 transition-all" style="width: {{ max(2, $pct) }}%">
                                @if($pct > 20)
                                    <span class="text-[10px] text-white font-medium">{{ number_format($m['revenue'], 0, ',', '.') }} €</span>
                                @endif
                            </div>
                            @if($pct <= 20 && $m['revenue'] > 0)
                                <span class="absolute left-[{{ $pct + 2 }}%] top-0.5 text-[10px] text-gray-500 ml-2">{{ number_format($m['revenue'], 0, ',', '.') }} €</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Lagerware vs Bestellware --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Lagerware vs. Bestellware</h3>
            @php
                $lagerPct = $totalItemRevenue > 0 ? round($lagerRevenue / $totalItemRevenue * 100) : 0;
                $bestellPct = 100 - $lagerPct;
            @endphp
            <div class="flex gap-4 mb-6">
                <div class="flex-1 bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-700">{{ $lagerPct }} %</div>
                    <div class="text-xs text-green-600 mt-1">Lagerware</div>
                    <div class="text-xs text-gray-500 mt-1">{{ number_format($lagerRevenue, 0, ',', '.') }} €</div>
                    <div class="text-[10px] text-gray-400">Ø Marge: 17,8 %</div>
                </div>
                <div class="flex-1 bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-700">{{ $bestellPct }} %</div>
                    <div class="text-xs text-blue-600 mt-1">Bestellware</div>
                    <div class="text-xs text-gray-500 mt-1">{{ number_format($bestellRevenue, 0, ',', '.') }} €</div>
                    <div class="text-[10px] text-gray-400">Ø Marge: 12,4 %</div>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 flex overflow-hidden">
                <div class="h-3 bg-green-500" style="width: {{ $lagerPct }}%"></div>
                <div class="h-3 bg-blue-500" style="width: {{ $bestellPct }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top products --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Top 5 Produkte</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Artikel</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Menge</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Umsatz</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($topProducts as $idx => $tp)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-2.5 text-gray-900 text-xs font-medium">{{ $tp->product_name }}</td>
                            <td class="px-4 py-2.5 text-right text-gray-500 text-xs">{{ $tp->qty }}x · {{ number_format($tp->weight, 0, ',', '.') }} kg</td>
                            <td class="px-4 py-2.5 text-right font-medium">{{ number_format($tp->total, 0, ',', '.') }}&nbsp;€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Top customers --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Top 5 Kunden</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Kunde</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Aufträge</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Umsatz</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($topCustomers as $idx => $tc)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.kunde', $tc->customer_id) }}'">
                            <td class="px-4 py-2.5 text-gray-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $tc->customer->company_name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right text-gray-500">{{ $tc->order_count }}</td>
                            <td class="px-4 py-2.5 text-right font-medium">{{ number_format($tc->total, 0, ',', '.') }}&nbsp;€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
