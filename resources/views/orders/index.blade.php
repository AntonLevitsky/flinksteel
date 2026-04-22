<x-app-layout>
    <x-slot:title>Meine Bestellungen</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Meine Bestellungen</h1>

        @if($orders->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-gray-500">Sie haben noch keine Bestellungen.</p>
                <a href="{{ route('home') }}" class="text-blue-900 hover:underline text-sm mt-2 inline-block">Jetzt einkaufen</a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bestellnr.</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Positionen</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Gesamtbetrag</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aktion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('orders.show', $order->order_number) }}'">
                                    <td class="px-6 py-4 text-sm font-medium text-blue-900">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $order->placed_at->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $order->items->count() }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;€</td>
                                    <td class="px-6 py-4">
                                        @php $color = $order->getStatusColor(); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $color === 'green' ? 'bg-green-50 text-green-700' : ($color === 'yellow' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-blue-700') }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('orders.show', $order->order_number) }}" class="text-sm text-blue-900 hover:underline">Detail</a>
                                        <span class="mx-1 text-gray-300">|</span>
                                        <form method="POST" action="{{ route('orders.reorder', $order->order_number) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-blue-900 hover:underline" onclick="event.stopPropagation()">Nachbestellen</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
