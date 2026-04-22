<x-admin.layout>
    <x-slot:title>Cockpit</x-slot:title>

    {{-- Primary KPI tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Auftragseingang heute</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($todayRevenue, 0, ',', '.') }}&nbsp;€</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $todayCount }} Aufträge · {{ number_format($todayWeight, 0, ',', '.') }} kg</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Offene Aufträge</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $openOrders }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Bestätigt & in Bearbeitung</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Lagerbestand</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalStockKg / 1000, 1, ',', '.') }}&nbsp;t</div>
            <div class="text-xs text-gray-400 mt-0.5">Bestandswert ca. {{ number_format($stockValueEur, 0, ',', '.') }} €</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Rohertrag MTD</div>
            <div class="text-2xl font-bold {{ $grossMarginPct >= 15 ? 'text-green-700' : 'text-amber-600' }} mt-1">{{ $grossMarginPct }}&nbsp;%</div>
            <div class="text-xs text-gray-400 mt-0.5">Umsatz Monat: {{ number_format($monthRevenue, 0, ',', '.') }} €</div>
        </div>
    </div>

    {{-- Trend KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            'lagerumschlag' => ['label' => 'Lagerumschlag', 'icon' => '↻'],
            'liefertreue' => ['label' => 'Liefertreue', 'icon' => '✓'],
            'avg_order' => ['label' => 'Ø Auftragswert', 'icon' => '€'],
            'bestandsreichweite' => ['label' => 'Bestandsreichweite', 'icon' => '📦'],
        ] as $key => $meta)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="text-xs text-gray-500 font-medium">{{ $meta['label'] }}</div>
                <div class="flex items-end gap-2 mt-1">
                    <span class="text-lg font-bold text-gray-900">{{ $kpis[$key]['value'] }}</span>
                    <span class="text-xs font-medium {{ $kpis[$key]['up'] ? 'text-green-600' : 'text-red-500' }} mb-0.5">
                        {{ $kpis[$key]['up'] ? '▲' : '▼' }} {{ $kpis[$key]['delta'] }}
                    </span>
                </div>
                <div class="text-[10px] text-gray-400">vs. Vormonat</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent orders (2/3 width) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Letzte Bestellungen</h2>
                    <a href="{{ route('admin.auftraege') }}" class="text-xs text-blue-900 hover:underline">Alle anzeigen →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">Nr.</th>
                                <th class="px-4 py-2 text-left">Kunde</th>
                                <th class="px-4 py-2 text-right">Wert</th>
                                <th class="px-4 py-2 text-right">Gewicht</th>
                                <th class="px-4 py-2 text-center">Typ</th>
                                <th class="px-4 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.auftrag', $order->order_number) }}'">
                                    <td class="px-4 py-2.5 font-medium text-blue-900">{{ $order->order_number }}</td>
                                    <td class="px-4 py-2.5 text-gray-700">{{ $order->customer->company_name ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-right font-medium">{{ number_format($order->total_eur, 0, ',', '.') }}&nbsp;€</td>
                                    <td class="px-4 py-2.5 text-right text-gray-500">{{ number_format($order->total_weight, 0, ',', '.') }} kg</td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if($order->fulfillment_type === 'lager')
                                            <span class="text-[10px] font-medium text-green-700 bg-green-50 px-1.5 py-0.5 rounded">Lager</span>
                                        @elseif($order->fulfillment_type === 'misch')
                                            <span class="text-[10px] font-medium text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">Misch</span>
                                        @else
                                            <span class="text-[10px] font-medium text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">Bestell</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        @php $sc = $order->getStatusColor(); @endphp
                                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded {{ $sc === 'green' ? 'text-green-700 bg-green-50' : ($sc === 'yellow' ? 'text-amber-700 bg-amber-50' : 'text-blue-700 bg-blue-50') }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Supplier POs --}}
            @if($supplierPOs->isNotEmpty())
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900">Offene Lieferantenbestellungen</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Best.-Nr.</th>
                                    <th class="px-4 py-2 text-left">Lieferant</th>
                                    <th class="px-4 py-2 text-left">Artikel</th>
                                    <th class="px-4 py-2 text-right">Gewicht</th>
                                    <th class="px-4 py-2 text-left">Kundenauftrag</th>
                                    <th class="px-4 py-2 text-center">Liefertermin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($supplierPOs as $po)
                                    <tr>
                                        <td class="px-4 py-2.5 font-mono text-xs">{{ $po->po_number }}</td>
                                        <td class="px-4 py-2.5 text-gray-700">{{ $po->supplier }}</td>
                                        <td class="px-4 py-2.5 text-gray-500 text-xs">{{ \Illuminate\Support\Str::limit($po->product_name, 30) }}</td>
                                        <td class="px-4 py-2.5 text-right text-gray-500">{{ number_format($po->weight_kg, 0, ',', '.') }} kg</td>
                                        <td class="px-4 py-2.5 text-blue-900 text-xs">{{ $po->customer_order }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <span class="text-xs {{ $po->is_overdue ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                                {{ $po->expected_date->format('d.m.') }}
                                                @if($po->is_overdue) ⚠ @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right column: alerts --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">Bestandswarnungen</h2>
                </div>
                @if($lowStock->isEmpty())
                    <div class="p-4 text-sm text-gray-400 text-center">Keine Warnungen</div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($lowStock as $product)
                            <div class="px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-900 truncate">{{ $product->name }}</span>
                                    <span class="text-xs font-bold {{ $product->stock_quantity_kg < 1000 ? 'text-red-600' : 'text-amber-600' }}">
                                        {{ number_format($product->stock_quantity_kg, 0, ',', '.') }} kg
                                    </span>
                                </div>
                                <div class="mt-1 w-full bg-gray-100 rounded-full h-1.5">
                                    @php $pct = min(100, ($product->stock_quantity_kg / max($product->stock_quantity_kg * 3, 1)) * 100); @endphp
                                    <div class="h-1.5 rounded-full {{ $product->stock_quantity_kg < 1000 ? 'bg-red-500' : 'bg-amber-500' }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $product->material->grade ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="px-4 py-2 border-t border-gray-100">
                    <a href="{{ route('admin.lager') }}" class="text-xs text-blue-900 hover:underline">Lagerbestand anzeigen →</a>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout>
