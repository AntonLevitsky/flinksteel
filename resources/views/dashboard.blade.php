<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Welcome section --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Willkommen zurück, {{ Auth::user()->name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $customer->company_name }} &middot; Kundennr. {{ $customer->customer_number }}
                    </p>
                </div>
                <div>
                    @php
                        $tierLabel = $customer->getPriceTierLabel();
                        $tierColor = match($customer->price_tier) {
                            'vip' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'premium' => 'bg-blue-50 text-blue-700 border-blue-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $tierColor }}">
                        {{ $tierLabel }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Total Bestellungen</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Letzte Bestellung</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                    @if($lastOrderDate)
                        {{ \Carbon\Carbon::parse($lastOrderDate)->format('d.m.Y') }}
                    @else
                        &mdash;
                    @endif
                </p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Ihr Kundenstatus</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $customer->getPriceTierLabel() }}</p>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('home') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-blue-300 hover:shadow-lg transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors">Sortiment durchstöbern</span>
                </div>
            </a>
            <a href="{{ route('cart.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-blue-300 hover:shadow-lg transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors">Warenkorb</span>
                </div>
            </a>
            <a href="{{ route('orders.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 hover:border-blue-300 hover:shadow-lg transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors">Meine Bestellungen</span>
                </div>
            </a>
        </div>

        {{-- Recent orders --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Letzte Bestellungen</h2>
                <a href="{{ route('orders.index') }}" class="text-sm text-blue-900 hover:underline">Alle anzeigen</a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    Noch keine Bestellungen vorhanden.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bestellnr.</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Datum</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Positionen</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Gesamt</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('orders.show', $order->order_number) }}" class="text-blue-900 hover:underline font-medium">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $order->placed_at->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $order->items->count() }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;&euro;</td>
                                    <td class="px-6 py-4">
                                        @php $color = $order->getStatusColor(); @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $color }}-50 text-{{ $color }}-700">{{ $order->getStatusLabel() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('orders.show', $order->order_number) }}" class="text-sm text-blue-900 hover:underline">Detail</a>
                                            <form method="POST" action="{{ route('orders.reorder', $order->order_number) }}">
                                                @csrf
                                                <button type="submit" class="text-sm text-blue-900 hover:underline">Nachbestellen</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Featured products --}}
        @if($featured->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Empfohlene Produkte</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featured as $p)
                        @include('category._product-card', ['product' => $p])
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
