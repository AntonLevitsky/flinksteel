<x-admin.layout>
    <x-slot:title>Kunden</x-slot:title>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Aktive Kunden</div>
            <div class="text-xl font-bold text-gray-900">{{ $customers->count() }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Umsatz YTD</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($totalRevenueYtd, 0, ',', '.') }}&nbsp;€</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Ø Auftragswert</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($avgOrderValue, 0, ',', '.') }}&nbsp;€</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Kunde</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Kd.-Nr.</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Kondition</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Umsatz YTD</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Aufträge</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Ø Wert</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Kreditlimit</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Auslastung</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Letzte Best.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customers as $c)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.kunde', $c->id) }}'">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $c->company_name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $c->city }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $c->customer_number }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($c->price_tier === 'vip')
                                    <span class="text-[10px] font-medium text-purple-700 bg-purple-50 border border-purple-200 px-1.5 py-0.5 rounded">VIP</span>
                                @elseif($c->price_tier === 'premium')
                                    <span class="text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Premium</span>
                                @else
                                    <span class="text-[10px] font-medium text-gray-500 bg-gray-50 border border-gray-200 px-1.5 py-0.5 rounded">Standard</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($c->revenue_ytd, 0, ',', '.') }}&nbsp;€</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $c->order_count }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ number_format($c->avg_order, 0, ',', '.') }}&nbsp;€</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ number_format($c->credit_limit_eur, 0, ',', '.') }}&nbsp;€</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center gap-2 justify-center">
                                    <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $c->credit_pct > 80 ? 'bg-red-500' : ($c->credit_pct > 50 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ min(100, $c->credit_pct) }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-500">{{ $c->credit_pct }} %</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $c->last_order_date?->format('d.m.Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
