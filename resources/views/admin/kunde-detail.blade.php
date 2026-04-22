<x-admin.layout>
    <x-slot:title>Kunde: {{ $customer->company_name }}</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.kunden') }}" class="text-sm text-gray-500 hover:text-blue-900">← Zurück zur Kundenliste</a>
    </div>

    {{-- Customer header --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Umsatz YTD</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($customer->revenue_ytd, 0, ',', '.') }}&nbsp;€</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Aufträge</div>
            <div class="text-xl font-bold text-gray-900">{{ $customer->order_count }}</div>
            <div class="text-[10px] text-gray-400">Ø {{ number_format($customer->avg_order, 0, ',', '.') }} € pro Auftrag</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Kondition</div>
            <div class="text-sm font-semibold mt-1">{{ $customer->getPriceTierLabel() }}</div>
            <div class="text-[10px] text-gray-400">Multiplikator: {{ number_format($customer->price_multiplier, 2, ',', '.') }}x</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Kreditlimit</div>
            <div class="text-sm font-bold">{{ number_format($customer->credit_limit_eur, 0, ',', '.') }}&nbsp;€</div>
            <div class="text-[10px] text-gray-400">Zahlung: {{ $customer->payment_terms_days }} Tage netto</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Stammdaten + Top products --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Stammdaten</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-500 text-xs">Firma:</span> <span class="font-medium">{{ $customer->company_name }}</span></div>
                    <div><span class="text-gray-500 text-xs">Kd.-Nr.:</span> <span class="font-mono">{{ $customer->customer_number }}</span></div>
                    <div><span class="text-gray-500 text-xs">Adresse:</span> {{ $customer->street }}, {{ $customer->postal_code }} {{ $customer->city }}</div>
                    <div><span class="text-gray-500 text-xs">USt-IdNr.:</span> {{ $customer->vat_id ?? '—' }}</div>
                    @if($customer->users->isNotEmpty())
                        <div class="pt-2 border-t border-gray-100">
                            <span class="text-gray-500 text-xs">Benutzer:</span>
                            @foreach($customer->users as $u)
                                <div class="text-xs mt-1">{{ $u->name }} · {{ $u->email }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($topProducts->isNotEmpty())
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Top-Artikel</h3>
                    <div class="space-y-2">
                        @foreach($topProducts as $tp)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-700 truncate mr-2">{{ $tp->name }}</span>
                                <span class="font-medium whitespace-nowrap">{{ number_format($tp->total_eur, 0, ',', '.') }} €</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Recent orders --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Letzte Bestellungen</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nr.</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Datum</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Pos.</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Betrag</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.auftrag', $order->order_number) }}'">
                                <td class="px-4 py-2.5 font-medium text-blue-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $order->placed_at->format('d.m.Y') }}</td>
                                <td class="px-4 py-2.5 text-right text-gray-500">{{ $order->items->count() }}</td>
                                <td class="px-4 py-2.5 text-right font-medium">{{ number_format($order->total_eur, 0, ',', '.') }}&nbsp;€</td>
                                <td class="px-4 py-2.5 text-center">
                                    @php $sc = $order->getStatusColor(); @endphp
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $sc === 'green' ? 'text-green-700 bg-green-50' : ($sc === 'yellow' ? 'text-amber-700 bg-amber-50' : 'text-blue-700 bg-blue-50') }}">
                                        {{ $order->getStatusLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Keine Bestellungen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin.layout>
