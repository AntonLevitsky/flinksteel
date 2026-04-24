<x-app-layout>
    <x-slot:title>Kasse</x-slot:title>

    @php
        $shippingOptionsJson = collect($shippingOptions)->map(function($opt) {
            return ['code' => $opt['code'], 'cost' => $opt['cost']];
        })->values()->toJson();
        $paymentTermsDays = $customer->payment_terms_days ?? 30;
        $paymentDueEstimate = now()->addDays($paymentTermsDays)->format('d.m.Y');
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{
            openStep: 1,
            maxStepReached: 1,
            billingSame: true,
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
            formatEur(val) { return val.toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' €'; },
            openIfAllowed(step) {
                if (step <= this.maxStepReached) this.openStep = step;
            },
            advance(step) {
                this.openStep = step;
                if (step > this.maxStepReached) this.maxStepReached = step;
                window.scrollTo({ top: document.getElementById('step-' + step).offsetTop - 80, behavior: 'smooth' });
            }
         }">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kasse</h1>
            <a href="{{ route('cart.index') }}" class="text-sm text-gray-500 hover:text-blue-900 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Zurück zum Warenkorb
            </a>
        </div>

        {{-- Checkout Progress Stepper --}}
        <nav class="mb-8" aria-label="Bestellfortschritt">
            <ol class="flex items-center justify-between max-w-2xl mx-auto">
                <li class="flex items-center gap-2">
                    <button type="button" @click="openIfAllowed(1)" class="flex items-center gap-2 group">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                              :class="maxStepReached > 1 ? 'bg-green-500 text-white' : (openStep === 1 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500')">
                            <template x-if="maxStepReached > 1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
                            <template x-if="maxStepReached <= 1"><span>1</span></template>
                        </span>
                        <span class="text-sm font-semibold hidden sm:block" :class="openStep === 1 ? 'text-blue-900' : 'text-gray-600'">Lieferung</span>
                    </button>
                </li>
                <li class="flex-1 mx-2 sm:mx-4">
                    <div class="h-0.5" :class="maxStepReached >= 2 ? 'bg-blue-900' : 'bg-gray-200'"></div>
                </li>
                <li class="flex items-center gap-2">
                    <button type="button" @click="openIfAllowed(2)" class="flex items-center gap-2 group">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                              :class="maxStepReached > 2 ? 'bg-green-500 text-white' : (openStep === 2 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500')">
                            <template x-if="maxStepReached > 2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
                            <template x-if="maxStepReached <= 2"><span>2</span></template>
                        </span>
                        <span class="text-sm font-semibold hidden sm:block" :class="openStep === 2 ? 'text-blue-900' : 'text-gray-600'">Rechnung & Zahlung</span>
                    </button>
                </li>
                <li class="flex-1 mx-2 sm:mx-4">
                    <div class="h-0.5" :class="maxStepReached >= 3 ? 'bg-blue-900' : 'bg-gray-200'"></div>
                </li>
                <li class="flex items-center gap-2">
                    <button type="button" @click="openIfAllowed(3)" class="flex items-center gap-2 group">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                              :class="openStep === 3 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'">
                            <span>3</span>
                        </span>
                        <span class="text-sm font-semibold hidden sm:block" :class="openStep === 3 ? 'text-blue-900' : 'text-gray-600'">Prüfen & Bestellen</span>
                    </button>
                </li>
            </ol>
        </nav>

        <form method="POST" action="{{ route('checkout.place') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left column — accordion steps --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- ============ STEP 1: Lieferung ============ --}}
                    <div id="step-1" class="bg-white rounded-xl border border-gray-200 overflow-hidden scroll-mt-20" :class="openStep === 1 ? 'ring-1 ring-blue-900' : ''">
                        <button type="button" @click="openIfAllowed(1)" class="w-full flex items-center justify-between p-6 text-left">
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                                      :class="maxStepReached > 1 ? 'bg-green-500 text-white' : 'bg-blue-900 text-white'">
                                    <template x-if="maxStepReached > 1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
                                    <template x-if="maxStepReached <= 1"><span>1</span></template>
                                </span>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Lieferung</h2>
                                    <p class="text-xs text-gray-500 mt-0.5">Lieferadresse, Ansprechpartner, Versandart und Wunschtermin</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="openStep === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openStep === 1" x-collapse>
                            <div class="px-6 pb-6 space-y-6 border-t border-gray-100 pt-6">

                                {{-- Lieferadresse --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Lieferadresse</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Firma / Standortbezeichnung</label>
                                            <input type="text" name="delivery_company_name" value="{{ old('delivery_company_name', $customer->company_name) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                            <p class="text-xs text-gray-400 mt-1">z.B. „Werk Nord — Halle 3" falls abweichend vom Firmensitz</p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Straße und Hausnummer <span class="text-red-500">*</span></label>
                                            <input type="text" name="delivery_street" value="{{ old('delivery_street', $customer->street) }}" required
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                            @error('delivery_street') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">PLZ <span class="text-red-500">*</span></label>
                                            <input type="text" name="delivery_postal_code" value="{{ old('delivery_postal_code', $customer->postal_code) }}" required
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                            @error('delivery_postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Ort <span class="text-red-500">*</span></label>
                                            <input type="text" name="delivery_city" value="{{ old('delivery_city', $customer->city) }}" required
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                            @error('delivery_city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Ansprechpartner vor Ort --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-1">Ansprechpartner vor Ort</h3>
                                    <p class="text-xs text-gray-500 mb-3">Unser Fahrer ruft vor Anlieferung an — bitte Handynummer angeben, unter der die Ware angenommen wird.</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                            <input type="text" name="delivery_contact_name" value="{{ old('delivery_contact_name', Auth::user()->name) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                            <input type="tel" name="delivery_contact_phone" value="{{ old('delivery_contact_phone') }}" placeholder="+49 ..."
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Anlieferzeitfenster (optional)</label>
                                            <input type="text" name="delivery_window" value="{{ old('delivery_window') }}" placeholder="z.B. Mo–Fr 7:00–15:00 Uhr"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                    </div>
                                </div>

                                {{-- Versandart --}}
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-sm font-semibold text-gray-900">Versandart</h3>
                                        <span class="text-xs text-gray-400">{{ number_format($totalWeight, 1, ',', '.') }} kg · {{ $itemCount }} Stück</span>
                                    </div>
                                    <div class="space-y-2">
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

                                {{-- Wunschliefertermin --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Wunschliefertermin <span class="text-red-500">*</span></label>
                                    <input type="date" name="requested_delivery_date"
                                        value="{{ old('requested_delivery_date', now()->addDays(3)->format('Y-m-d')) }}"
                                        min="{{ now()->addDays(2)->format('Y-m-d') }}" required
                                        class="w-full sm:w-auto rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                    <p class="text-xs text-gray-400 mt-1">Frühester Termin: {{ now()->addDays(2)->format('d.m.Y') }} · Bindend nach Bestätigung durch uns.</p>
                                    @error('requested_delivery_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Teillieferung notice --}}
                                @if($isMixedOrder)
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3">
                                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div>
                                            <p class="text-sm font-medium text-amber-800">Voraussichtlich 2 Teillieferungen</p>
                                            <p class="text-xs text-amber-700 mt-1">Ihre Bestellung enthält Lagerware (1–3 WT) und Bestellware (5–10 WT). Keine zusätzlichen Versandkosten.</p>
                                        </div>
                                    </div>
                                @elseif($hasBestellware)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
                                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-800">Bestellware — Lieferfrist ca. 5–10 Werktage</p>
                                            <p class="text-xs text-blue-700 mt-1">Alle Artikel werden vom Hersteller bezogen und nach Eingang in unserem Lager an Sie versandt.</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex justify-end pt-2">
                                    <button type="button" @click="advance(2)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-900 hover:bg-blue-800 text-white font-medium text-sm rounded-lg transition-colors">
                                        Weiter zu Rechnung & Zahlung
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============ STEP 2: Rechnung & Zahlung ============ --}}
                    <div id="step-2" class="bg-white rounded-xl border border-gray-200 overflow-hidden scroll-mt-20" :class="openStep === 2 ? 'ring-1 ring-blue-900' : ''">
                        <button type="button" @click="openIfAllowed(2)" class="w-full flex items-center justify-between p-6 text-left" :disabled="maxStepReached < 2">
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                                      :class="maxStepReached > 2 ? 'bg-green-500 text-white' : (maxStepReached >= 2 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500')">
                                    <template x-if="maxStepReached > 2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></template>
                                    <template x-if="maxStepReached <= 2"><span>2</span></template>
                                </span>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Rechnung & Zahlung</h2>
                                    <p class="text-xs text-gray-500 mt-0.5">Rechnungsadresse, Bestellreferenz, Zahlungsziel</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="openStep === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openStep === 2" x-collapse>
                            <div class="px-6 pb-6 space-y-6 border-t border-gray-100 pt-6">

                                {{-- PO Number (prominent) --}}
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Ihre Bestellnummer / Referenz (PO)</label>
                                    <p class="text-xs text-gray-500 mb-2">Wird auf Auftragsbestätigung und Rechnung gedruckt. Viele Einkaufsabteilungen benötigen dies.</p>
                                    <input type="text" name="po_number" value="{{ old('po_number') }}" placeholder="z.B. PO-2026-4711 (optional)"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                </div>

                                {{-- Rechnungsadresse --}}
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-sm font-semibold text-gray-900">Rechnungsadresse</h3>
                                    </div>
                                    <label class="inline-flex items-center gap-2 mb-3 cursor-pointer">
                                        <input type="checkbox" x-model="billingSame" value="1" name="billing_same_as_delivery" class="rounded border-gray-300 text-blue-900 focus:ring-blue-900">
                                        <span class="text-sm text-gray-700">Rechnungsadresse entspricht dem Firmensitz (Stammdaten)</span>
                                    </label>

                                    <div x-show="billingSame" class="bg-gray-50 rounded-lg p-4 text-sm">
                                        <p class="font-medium text-gray-900">{{ $customer->company_name }}</p>
                                        <p class="text-gray-600">{{ $customer->street }}</p>
                                        <p class="text-gray-600">{{ $customer->postal_code }} {{ $customer->city }}</p>
                                        <p class="text-gray-500 mt-2 text-xs">Kd.-Nr.: {{ $customer->customer_number }}
                                            @if($customer->vat_id) · USt-IdNr.: {{ $customer->vat_id }} @endif
                                        </p>
                                    </div>

                                    <div x-show="!billingSame" x-collapse class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Firma</label>
                                            <input type="text" name="billing_company_name" value="{{ old('billing_company_name', $customer->company_name) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Straße und Hausnummer</label>
                                            <input type="text" name="billing_street" value="{{ old('billing_street', $customer->street) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
                                            <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $customer->postal_code) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Ort</label>
                                            <input type="text" name="billing_city" value="{{ old('billing_city', $customer->city) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">USt-IdNr.</label>
                                            <input type="text" name="billing_vat_id" value="{{ old('billing_vat_id', $customer->vat_id) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail für Rechnung</label>
                                            <input type="email" name="billing_email" value="{{ old('billing_email', Auth::user()->email) }}"
                                                class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900">
                                        </div>
                                    </div>
                                </div>

                                {{-- Zahlungsart (fixed: Rechnung) --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Zahlungsart</h3>
                                    <div class="border border-blue-900 bg-blue-50/50 ring-1 ring-blue-900 rounded-lg p-4 flex items-start gap-3">
                                        <div class="w-10 h-10 bg-blue-900 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">Kauf auf Rechnung</p>
                                            <p class="text-xs text-gray-600 mt-0.5">Zahlung per Überweisung nach Erhalt der Ware</p>
                                            <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                                <div>
                                                    <span class="text-gray-500">Zahlungsziel</span>
                                                    <p class="font-semibold text-gray-900">{{ $paymentTermsDays }} Tage netto</p>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Fällig bis ca.</span>
                                                    <p class="font-semibold text-gray-900">{{ $paymentDueEstimate }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Bankverbindung und Verwendungszweck erscheinen nach Bestelleingang auf Ihrer Auftragsbestätigung.</p>
                                </div>

                                {{-- Notes --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bemerkungen (optional)</label>
                                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-900 focus:ring-blue-900" placeholder="z.B. Abladestelle Halle 2, Gabelstapler vorhanden, Kran auf Werksgelände...">{{ old('notes') }}</textarea>
                                </div>

                                <div class="flex justify-between pt-2">
                                    <button type="button" @click="openIfAllowed(1)" class="text-sm text-gray-500 hover:text-gray-700">← Zurück</button>
                                    <button type="button" @click="advance(3)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-900 hover:bg-blue-800 text-white font-medium text-sm rounded-lg transition-colors">
                                        Weiter zur Prüfung
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============ STEP 3: Prüfen & Bestellen ============ --}}
                    <div id="step-3" class="bg-white rounded-xl border border-gray-200 overflow-hidden scroll-mt-20" :class="openStep === 3 ? 'ring-1 ring-blue-900' : ''">
                        <button type="button" @click="openIfAllowed(3)" class="w-full flex items-center justify-between p-6 text-left" :disabled="maxStepReached < 3">
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                                      :class="maxStepReached >= 3 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'">
                                    <span>3</span>
                                </span>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">Prüfen & Bestellen</h2>
                                    <p class="text-xs text-gray-500 mt-0.5">Alle Angaben im Überblick — kostenpflichtig bestellen</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="openStep === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openStep === 3" x-collapse>
                            <div class="px-6 pb-6 space-y-5 border-t border-gray-100 pt-6">

                                {{-- Positionen --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Positionen ({{ $items->count() }})</h3>
                                    <div class="divide-y divide-gray-100 border-t border-b border-gray-100">
                                        @foreach($items as $item)
                                            <div class="py-3 flex justify-between items-start gap-4">
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <p class="text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                                                        @if($item->product->isBestellware())
                                                            <span class="text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Bestellware</span>
                                                        @else
                                                            <span class="text-[10px] font-medium text-green-700 bg-green-50 border border-green-200 px-1.5 py-0.5 rounded">Ab Lager</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-0.5">
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

                                {{-- Terms --}}
                                <div>
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="terms_accepted" value="1" required class="mt-0.5 rounded border-gray-300 text-blue-900 focus:ring-blue-900">
                                        <span class="text-sm text-gray-700">
                                            Ich bestätige, dass die Angaben korrekt sind und akzeptiere die
                                            <a href="#" class="text-blue-900 hover:underline">Allgemeinen Geschäftsbedingungen</a>
                                            sowie die
                                            <a href="#" class="text-blue-900 hover:underline">Datenschutzerklärung</a>.
                                            Mit Absenden der Bestellung kommt ein kostenpflichtiger Kaufvertrag zustande.
                                        </span>
                                    </label>
                                    @error('terms_accepted') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex justify-between items-center pt-2">
                                    <button type="button" @click="openIfAllowed(2)" class="text-sm text-gray-500 hover:text-gray-700">← Zurück</button>
                                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Kostenpflichtig bestellen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============ Right: Sidebar ============ --}}
                <div>
                    <div class="sticky top-20 space-y-4">
                        {{-- Summary --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
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

                            {{-- Payment summary --}}
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <svg class="w-4 h-4 text-blue-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Rechnung — {{ $paymentTermsDays }} Tage netto</span>
                                </div>
                            </div>

                            {{-- Free delivery badge for regional customers --}}
                            @if(str_starts_with($customer->postal_code ?? '', '88'))
                                <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm font-medium text-green-800">Kostenfreie Lieferung in Ihre Region</span>
                                </div>
                            @endif
                        </div>

                        {{-- Sales contact --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">Fragen zur Bestellung?</h3>
                            <p class="text-xs text-gray-500 mb-3">Unser Innendienst hilft persönlich weiter — Mo–Fr 7:00–17:00 Uhr.</p>
                            <a href="tel:+4975136060" class="flex items-center gap-2 text-sm font-semibold text-blue-900 hover:underline mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                +49 751 3606-0
                            </a>
                            <a href="mailto:auftraege@mueller-stahl.de" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                auftraege@mueller-stahl.de
                            </a>
                        </div>

                        {{-- Trust Signals --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-3">
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>SSL-verschlüsselte Datenübertragung</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 text-blue-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Ihr regionaler Stahlpartner seit 1952</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>ISO 9001 · DIN EN ISO/IEC 17025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
