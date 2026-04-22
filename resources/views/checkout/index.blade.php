<x-app-layout>
    <x-slot:title>Kasse</x-slot:title>

    @php
        $shippingOptionsJson = collect($shippingOptions)->map(function($opt) {
            return [
                'code' => $opt['code'],
                'cost' => $opt['cost'],
            ];
        })->values()->toJson();
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{
            selectedShipping: '{{ $shippingOptions[0]['code'] ?? 'standard' }}',
            shippingOptions: {{ $shippingOptionsJson }},
            subtotal: {{ $subtotal }},
            get shippingCost() {
                const opt = this.shippingOptions.find(o => o.code === this.selectedShipping);
                return opt ? opt.cost : 0;
            },
            get netTotal() { return this.subtotal + this.shippingCost; },
            get vat() { return Math.round(this.netTotal * 0.19 * 100) / 100; },
            get grossTotal() { return Math.round((this.netTotal + this.vat) * 100) / 100; },
            formatEur(val) {
                return val.toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '\u00A0€';
            }
         }">

        <h1 class="text-2xl font-bold text-gray-900 mb-8">Kasse</h1>

        {{-- Checkout Progress Stepper --}}
        <nav class="mb-8" aria-label="Bestellfortschritt">
            <ol class="flex items-center justify-between max-w-2xl mx-auto">
                {{-- Step 1 --}}
                <li class="flex items-center gap-2">
                    <a href="#address" class="flex items-center gap-2 group">
                        <span class="w-8 h-8 rounded-full bg-blue-900 text-white flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span class="text-sm font-semibold text-blue-900 hidden sm:block group-hover:underline">Lieferadresse</span>
                    </a>
                </li>
                {{-- Connector --}}
                <li class="flex-1 mx-2 sm:mx-4">
                    <div class="h-0.5 bg-blue-900"></div>
                </li>
                {{-- Step 2 --}}
                <li class="flex items-center gap-2">
                    <a href="#shipping" class="flex items-center gap-2 group">
                        <span class="w-8 h-8 rounded-full bg-blue-900 text-white flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span class="text-sm font-semibold text-blue-900 hidden sm:block group-hover:underline">Versandoptionen</span>
                    </a>
                </li>
                {{-- Connector --}}
                <li class="flex-1 mx-2 sm:mx-4">
                    <div class="h-0.5 bg-blue-900"></div>
                </li>
                {{-- Step 3 --}}
                <li class="flex items-center gap-2">
                    <a href="#review" class="flex items-center gap-2 group">
                        <span class="w-8 h-8 rounded-full bg-blue-900 text-white flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span class="text-sm font-semibold text-blue-900 hidden sm:block group-hover:underline">Überprüfen & Bestellen</span>
                    </a>
                </li>
            </ol>
        </nav>

        <form method="POST" action="{{ route('checkout.place') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left column --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Delivery address --}}
                    <div id="address" class="bg-white rounded-xl border border-gray-200 p-6 scroll-mt-20">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Lieferadresse</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Straße und Hausnummer</label>
                                <input type="text" name="delivery_street" value="{{ old('delivery_street', $customer->street) }}" required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                @error('delivery_street') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
                                <input type="text" name="delivery_postal_code" value="{{ old('delivery_postal_code', $customer->postal_code) }}" required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                @error('delivery_postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ort</label>
                                <input type="text" name="delivery_city" value="{{ old('delivery_city', $customer->city) }}" required
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                @error('delivery_city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Shipping options selector --}}
                    <div id="shipping" class="bg-white rounded-xl border border-gray-200 p-6 scroll-mt-20">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Versandart</h2>
                            <span class="text-xs text-gray-400">{{ number_format($totalWeight, 1, ',', '.') }} kg · {{ $itemCount }} {{ $itemCount === 1 ? 'Stück' : 'Stück' }}</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($shippingOptions as $index => $opt)
                                <label class="flex items-start gap-3 p-4 rounded-lg border cursor-pointer transition-all"
                                       :class="selectedShipping === '{{ $opt['code'] }}'
                                           ? 'border-blue-900 bg-blue-50/50 ring-1 ring-blue-900'
                                           : 'border-gray-200 hover:border-gray-300 bg-white'">
                                    <input type="radio" name="shipping_option" value="{{ $opt['code'] }}"
                                           x-model="selectedShipping"
                                           {{ $index === 0 ? 'checked' : '' }}
                                           class="mt-0.5 border-gray-300 text-blue-900 focus:ring-blue-900">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-semibold text-gray-900">{{ $opt['label'] }}</span>
                                            <span class="text-sm font-bold {{ $opt['is_free'] ? 'text-green-700' : 'text-gray-900' }} whitespace-nowrap ml-4">
                                                {{ $opt['is_free'] ? 'kostenfrei' : number_format($opt['cost'], 2, ',', '.') . ' €' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $opt['description'] }}</p>
                                        <div class="flex items-center gap-3 mt-1.5">
                                            <span class="inline-flex items-center gap-1 text-xs {{ $opt['code'] === 'pickup' ? 'text-blue-700' : ($opt['delivery_min'] <= 1 ? 'text-orange-600' : 'text-gray-600') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @if($opt['delivery_min'] === $opt['delivery_max'])
                                                    {{ $opt['delivery_min'] }} {{ $opt['delivery_min'] === 1 ? 'Werktag' : 'Werktage' }}
                                                @else
                                                    {{ $opt['delivery_min'] }}–{{ $opt['delivery_max'] }} Werktage
                                                @endif
                                            </span>
                                            @if($opt['note'])
                                                <span class="text-xs text-gray-400">· {{ $opt['note'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('shipping_option') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Date & Notes --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Liefertermin & Bemerkungen</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Wunschliefertermin</label>
                                <input type="date" name="requested_delivery_date"
                                    value="{{ old('requested_delivery_date', now()->addDays(3)->format('Y-m-d')) }}"
                                    min="{{ now()->addDays(2)->format('Y-m-d') }}" required
                                    class="w-full sm:w-auto rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                @error('requested_delivery_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bemerkungen (optional)</label>
                                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900" placeholder="z.B. Abladestelle Halle 2, Gabelstapler vorhanden...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Teillieferung notice --}}
                    @if($isMixedOrder)
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Voraussichtlich 2 Teillieferungen</p>
                                <p class="text-xs text-amber-700 mt-1">Ihre Bestellung enthält Lagerware (Versand in 1–3 Werktagen) und Bestellware (5–10 Werktage). Die Lieferung erfolgt in zwei separaten Sendungen. Zusätzliche Versandkosten entstehen Ihnen dadurch nicht.</p>
                            </div>
                        </div>
                    @elseif($hasBestellware)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Bestellware — Lieferfrist ca. 5–10 Werktage</p>
                                <p class="text-xs text-blue-700 mt-1">Alle Artikel werden vom Hersteller bezogen und nach Eingang in unserem Lager an Sie versandt.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Order items summary --}}
                    <div id="review" class="bg-white rounded-xl border border-gray-200 p-6 scroll-mt-20">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ihre Bestellung ({{ $items->count() }} Positionen)</h2>
                        <div class="divide-y divide-gray-100">
                            @foreach($items as $item)
                                <div class="py-3 flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                                            @if($item->product->isBestellware())
                                                <span class="text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Bestellware</span>
                                            @else
                                                <span class="text-[10px] font-medium text-green-700 bg-green-50 border border-green-200 px-1.5 py-0.5 rounded">Ab Lager</span>
                                            @endif
                                        </div>
                                        @php $deliveryDays = $item->product->getDeliveryDays(); @endphp
                                        <p class="text-xs text-gray-500">
                                            {{ $item->quantity }}x
                                            @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                                            · {{ number_format($item->product->calculateWeight($item->quantity, $item->length_mm), 1, ',', '.') }} kg
                                            @if(!empty($item->anarbeitung))
                                                @php
                                                    $anarbeitungNames = \App\Models\AnarbeitungOption::whereIn('code', $item->anarbeitung)->pluck('name_de')->toArray();
                                                @endphp
                                                · {{ implode(', ', $anarbeitungNames) }}
                                            @endif
                                            @if($item->certificate_code) · Zeugnis {{ $item->certificate_code }} @endif
                                        </p>
                                    </div>
                                    <span class="text-sm font-medium whitespace-nowrap">{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right: Order totals + CTA --}}
                <div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-20">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Zusammenfassung</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Waren ({{ $items->count() }} Pos.)</span>
                                <span>{{ number_format($subtotal, 2, ',', '.') }}&nbsp;€</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Versand</span>
                                <span x-text="shippingCost === 0 ? 'kostenfrei' : formatEur(shippingCost)"></span>
                            </div>
                            <div class="text-xs text-gray-400">
                                Gesamtgewicht: {{ number_format($totalWeight, 1, ',', '.') }} kg · {{ $itemCount }} Stück
                            </div>
                            <div class="border-t border-gray-200 pt-2 flex justify-between">
                                <span class="text-gray-500">Netto</span>
                                <span x-text="formatEur(netTotal)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">MwSt. (19 %)</span>
                                <span x-text="formatEur(vat)"></span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-bold">
                                <span>Gesamtsumme</span>
                                <span x-text="formatEur(grossTotal)"></span>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-6 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            Bestellung auslösen
                        </button>

                        <p class="text-xs text-gray-400 text-center mt-3">Mit Ihrer Bestellung akzeptieren Sie unsere AGB.</p>

                        {{-- Trust Signals --}}
                        <div class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>SSL-verschlüsselte Datenübertragung</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Kauf auf Rechnung — {{ $customer->payment_terms_days ?? 30 }} Tage Zahlungsziel</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-blue-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Ihr regionaler Stahlpartner seit 1952</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span>ISO 9001 · DIN EN ISO/IEC 17025</span>
                            </div>
                        </div>

                        {{-- Free delivery badge for regional customers --}}
                        @if(str_starts_with($customer->postal_code ?? '', '88'))
                            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm font-medium text-green-800">Kostenfreie Lieferung in Ihre Region</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
