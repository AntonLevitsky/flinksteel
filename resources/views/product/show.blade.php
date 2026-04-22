<x-app-layout>
    <x-slot:title>{{ $product->name }}</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20 lg:pb-0">
        {{-- Breadcrumbs --}}
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-900">Startseite</a>
            @foreach($breadcrumbs as $bc)
                <span class="mx-1">/</span>
                <a href="{{ route('category.show', $bc->slug) }}" class="hover:text-blue-900">{{ $bc->name }}</a>
            @endforeach
            <span class="mx-1">/</span>
            <span class="text-gray-900 font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            {{-- Left: SVG + badges --}}
            <div>
                <div class="bg-white rounded-xl border border-gray-200 p-12 flex items-center justify-center aspect-square">
                    <x-product-svg :form="$product->form->slug" class="w-64 h-64" />
                </div>
                <div class="flex flex-wrap gap-2 mt-4">
                    @if($product->has_restlaengen)
                        <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">Restlängen verfügbar</span>
                    @endif
                    <span class="text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">{{ $product->material->standard }}</span>
                    @foreach($product->certifications_available ?? [] as $cert)
                        <span class="text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1 rounded-full">Zeugnis {{ $cert }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Right: Product info + Configurator --}}
            <div>
                <div class="mb-6">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400 font-mono">{{ $product->sku }}</span>
                        @if($product->is_partner_network)
                            <span class="text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-full">Partnernetzwerk</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>

                    {{-- Customer-specific pricing --}}
                    @if($customer->price_multiplier < 1.0)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-lg font-bold text-blue-900">{{ number_format($customerPrice, 2, ',', '.') }}&nbsp;€/kg</span>
                            <span class="text-sm text-gray-400 line-through">{{ number_format($product->price_per_kg_eur, 2, ',', '.') }}&nbsp;€/kg</span>
                            <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full">{{ $customer->getPriceTierLabel() }}</span>
                        </div>
                    @else
                        <div class="mt-2">
                            <span class="text-lg font-bold text-blue-900">{{ number_format($customerPrice, 2, ',', '.') }}&nbsp;€/kg</span>
                        </div>
                    @endif

                    <p class="text-sm text-gray-600 mt-2">{{ $product->long_description }}</p>

                    {{-- Stock / availability --}}
                    @if($product->isLagerware())
                        <div class="mt-3 flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 text-sm px-3 py-1 rounded-full {{ $product->stock_quantity_kg > 500 ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $product->stock_quantity_kg > 500 ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                {{ $product->getStockStatus() }}
                            </span>
                            <span class="text-sm text-gray-500">{{ number_format($product->stock_quantity_kg, 0, ',', '.') }} kg auf Lager</span>
                        </div>
                    @else
                        <div class="mt-3">
                            <span class="inline-flex items-center gap-1.5 text-sm px-3 py-1 rounded-full bg-blue-50 text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Bestellware
                            </span>
                            <p class="text-sm text-gray-500 mt-1.5">Nicht auf Lager — wird bei Bestellung vom Hersteller bezogen. Lieferfrist ca. 5–10 Werktage.</p>
                        </div>
                    @endif

                    {{-- Supplier sourcing panel for Bestellware --}}
                    @if($supplierSourcing)
                        <div class="mt-4 bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-semibold text-gray-900">Automatisch bester Lieferant ausgewählt</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $supplierSourcing['selected']['name'] }}</span>
                                    <span class="text-gray-500 ml-2">{{ $supplierSourcing['selected']['delivery_days'] }} Werktage</span>
                                </div>
                                <span class="text-green-700 font-medium">{{ number_format($supplierSourcing['selected']['price_per_kg'], 2, ',', '.') }}&nbsp;€/kg EK</span>
                            </div>
                            <details class="mt-2">
                                <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">{{ count($supplierSourcing['alternatives']) }} weitere Angebote verglichen</summary>
                                <div class="mt-1 space-y-1">
                                    @foreach($supplierSourcing['alternatives'] as $alt)
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ $alt['name'] }} · {{ $alt['delivery_days'] }} WT</span>
                                            <span>{{ number_format($alt['price_per_kg'], 2, ',', '.') }}&nbsp;€/kg</span>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @endif

                    {{-- Partner network info --}}
                    @if($product->is_partner_network && $product->partner_source)
                        <div class="mt-3 flex items-center gap-2 text-xs text-purple-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Bereitgestellt über {{ $product->partner_source }}</span>
                        </div>
                    @endif
                </div>

                {{-- React Configurator Island --}}
                @php
                    $configuratorProps = [
                        'product' => [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sku' => $product->sku,
                            'is_cut_to_length' => $product->is_cut_to_length,
                            'standard_length_mm' => $product->standard_length_mm,
                            'weight_per_meter_kg' => (float)$product->weight_per_meter_kg,
                            'weight_per_piece_kg' => (float)$product->weight_per_piece_kg,
                            'price_per_kg_eur' => (float)$customerPrice,
                            'dimensions' => $product->dimensions,
                            'is_bestellware' => $product->isBestellware(),
                            'delivery_label' => $product->getDeliveryLabel(),
                        ],
                        'anarbeitungOptions' => $product->anarbeitungOptions->map(function($o) {
                            return [
                                'code' => $o->code,
                                'name_de' => $o->name_de,
                                'price_per_cut_eur' => $o->price_per_cut_eur ? (float)$o->price_per_cut_eur : null,
                                'price_per_kg_eur' => $o->price_per_kg_eur ? (float)$o->price_per_kg_eur : null,
                            ];
                        })->values(),
                        'certificates' => $certificates->map(function($c) {
                            return [
                                'code' => $c->code,
                                'name_de' => $c->name_de,
                                'surcharge_eur' => (float)$c->surcharge_eur,
                            ];
                        })->values(),
                        'csrfToken' => csrf_token(),
                    ];
                @endphp
                <div id="react-product-configurator" data-props='@json($configuratorProps)'></div>

                <div class="mt-4 text-sm text-gray-500 flex items-center gap-2">
                    @if($product->isLagerware())
                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $product->getDeliveryLabel() }}
                    @else
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $product->getDeliveryLabel() }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Cross-sell: Auch interessant --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mb-12">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <span class="text-orange-500">KI-Empfehlung</span> — Auch interessant für Sie
                </h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($relatedProducts as $related)
                        <a href="{{ route('product.show', $related->id) }}" class="group bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all overflow-hidden">
                            <div class="aspect-[4/3] bg-gray-50 p-4 flex items-center justify-center relative">
                                <x-product-svg :form="$related->form->slug" class="w-16 h-16 opacity-60 group-hover:opacity-100 transition-opacity" />
                                @if($related->is_partner_network)
                                    <span class="absolute top-2 right-2 text-[9px] font-medium text-purple-700 bg-purple-50 border border-purple-200 px-1 py-0.5 rounded">Netzwerk</span>
                                @endif
                            </div>
                            <div class="p-3">
                                <span class="text-[10px] font-medium text-blue-900 bg-blue-50 px-1.5 py-0.5 rounded">{{ $related->material->grade }}</span>
                                <h3 class="mt-1 text-xs font-semibold text-gray-900 group-hover:text-blue-900 line-clamp-2">{{ $related->name }}</h3>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-900">ab {{ number_format($related->getPriceForCustomer($customer), 2, ',', '.') }}&nbsp;€/kg</span>
                                    @if($related->isLagerware())
                                        <span class="text-[10px] text-green-700">Ab Lager</span>
                                    @else
                                        <span class="text-[10px] text-blue-700">Bestellware</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Tabs --}}
        <div class="bg-white rounded-xl border border-gray-200" x-data="{ tab: 'tech' }">
            <nav class="flex border-b border-gray-200 overflow-x-auto">
                @foreach(['tech' => 'Technische Daten', 'certs' => 'Zeugnisse', 'processing' => 'Anarbeitung', 'delivery' => 'Lieferbedingungen'] as $key => $label)
                    <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-6 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>

            <div class="p-6">
                <div x-show="tab === 'tech'">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="py-2 text-gray-500 w-48">Werkstoff</td><td class="py-2 font-medium">{{ $product->material->grade }}</td></tr>
                            <tr><td class="py-2 text-gray-500">Norm</td><td class="py-2">{{ $product->material->standard }}</td></tr>
                            <tr><td class="py-2 text-gray-500">Form</td><td class="py-2">{{ $product->form->name }}</td></tr>
                            @if($product->dimensions)
                                @foreach($product->dimensions as $key => $val)
                                    <tr><td class="py-2 text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</td><td class="py-2">{{ is_numeric($val) ? number_format($val, $val == (int)$val ? 0 : 1, ',', '.') : $val }} {{ str_contains($key, 'mm') ? 'mm' : '' }}</td></tr>
                                @endforeach
                            @endif
                            <tr><td class="py-2 text-gray-500">Dichte</td><td class="py-2">{{ number_format($product->material->density_kg_per_m3, 0, ',', '.') }} kg/m³</td></tr>
                            @if($product->weight_per_meter_kg)
                                <tr><td class="py-2 text-gray-500">Gewicht pro Meter</td><td class="py-2">{{ number_format($product->weight_per_meter_kg, 3, ',', '.') }} kg/m</td></tr>
                            @endif
                            @if($product->weight_per_piece_kg)
                                <tr><td class="py-2 text-gray-500">Stückgewicht</td><td class="py-2">{{ number_format($product->weight_per_piece_kg, 2, ',', '.') }} kg</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div x-show="tab === 'certs'" x-cloak>
                    <p class="text-gray-600 mb-4">Für dieses Produkt sind folgende Zeugnisse verfügbar:</p>
                    <ul class="space-y-3">
                        @foreach($certificates as $cert)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <span class="font-medium">{{ $cert->name_de }}</span>
                                    @if($cert->surcharge_eur > 0)
                                        <span class="text-sm text-gray-500">(+{{ number_format($cert->surcharge_eur, 2, ',', '.') }}&nbsp;€ pro Position)</span>
                                    @else
                                        <span class="text-sm text-green-600">(kostenlos)</span>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $cert->description_de }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div x-show="tab === 'processing'" x-cloak>
                    <p class="text-gray-600 mb-4">Folgende Anarbeitungen sind für dieses Produkt möglich:</p>
                    <ul class="space-y-3">
                        @foreach($product->anarbeitungOptions as $opt)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-900 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <span class="font-medium">{{ $opt->name_de }}</span>
                                    @if($opt->price_per_cut_eur)
                                        <span class="text-sm text-gray-500">({{ number_format($opt->price_per_cut_eur, 2, ',', '.') }}&nbsp;€/Schnitt)</span>
                                    @elseif($opt->price_per_kg_eur)
                                        <span class="text-sm text-gray-500">({{ number_format($opt->price_per_kg_eur, 2, ',', '.') }}&nbsp;€/kg)</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div x-show="tab === 'delivery'" x-cloak>
                    <div class="space-y-8 text-sm text-gray-600">

                        {{-- Versandkosten --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Versandkosten</h4>
                            <p class="mb-3">Die Versandkosten richten sich nach dem Gesamtgewicht Ihrer Bestellung. Alle Preise verstehen sich netto zzgl. MwSt.</p>
                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Versandart</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Gewicht</th>
                                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Kosten</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr><td class="px-4 py-2">Paketversand (DHL/GLS)</td><td class="px-4 py-2">bis 30 kg</td><td class="px-4 py-2 text-right font-medium">9,90&nbsp;€</td></tr>
                                        <tr><td class="px-4 py-2">Langpaket (bis 3.000 mm)</td><td class="px-4 py-2">bis 30 kg</td><td class="px-4 py-2 text-right font-medium">29,90&nbsp;€</td></tr>
                                        <tr><td class="px-4 py-2">Spedition</td><td class="px-4 py-2">bis 500 kg</td><td class="px-4 py-2 text-right font-medium">89,00&nbsp;€</td></tr>
                                        <tr><td class="px-4 py-2">Spedition</td><td class="px-4 py-2">bis 2.500 kg</td><td class="px-4 py-2 text-right font-medium">189,00&nbsp;€</td></tr>
                                        <tr><td class="px-4 py-2">Spedition</td><td class="px-4 py-2">bis 5.000 kg</td><td class="px-4 py-2 text-right font-medium">289,00&nbsp;€</td></tr>
                                        <tr class="bg-green-50"><td class="px-4 py-2 font-medium text-green-800" colspan="2">Frei-Haus-Lieferung Region Bodensee-Oberschwaben</td><td class="px-4 py-2 text-right font-medium text-green-800">kostenfrei</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-2 text-xs text-gray-400">Bei Lieferungen auf Inseln wird ein Zuschlag von 25,00 € pro Sendung berechnet. Für Privatanschriften fällt ein Zuschlag von 15,00 € an.</p>
                        </div>

                        {{-- Lieferzeiten --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Lieferzeiten</h4>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-green-500 shrink-0"></span>
                                    <div><strong>Paketversand (bis 30 kg):</strong> 1–3 Werktage ab Auftragsbestätigung.</div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                    <div><strong>Speditionsversand:</strong> 3–5 Werktage ab Auftragsbestätigung. Der Spediteur avisiert die Lieferung telefonisch.</div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-2 h-2 mt-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                    <div><strong>Bei Anarbeitung (Zuschnitt, Entgraten etc.):</strong> zzgl. 1–3 Werktage Bearbeitungszeit.</div>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-gray-400">Alle Lieferfristen gelten annähernd und beginnen mit dem Datum der Auftragsbestätigung, frühestens jedoch nach Klärung aller Auftragseinzelheiten. Verbindliche Liefertermine bedürfen unserer ausdrücklichen schriftlichen Bestätigung.</p>
                        </div>

                        {{-- Speditionslieferung --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Hinweise zur Speditionslieferung</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Die Lieferung erfolgt <strong>frei Bordsteinkante</strong>. Der Spediteur öffnet das Fahrzeug — die Entladung erfolgt durch den Empfänger.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Bei schwerem Material (> 100 kg) muss ein geeignetes Abladegerät (Gabelstapler, Radlader) vor Ort vorhanden sein.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Die Lieferadresse muss für LKW über 7,5 t befahrbar sein. Die maximale Abladezeit beträgt 30 Minuten.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Bitte prüfen Sie die Ware <strong>sofort bei Anlieferung</strong> auf äußere Beschädigung und vermerken Sie etwaige Transportschäden auf dem Frachtbrief des Fahrers.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 mt-0.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Anlieferungen an Samstagen, Sonn- und Feiertagen sind nicht möglich. Stundengenau Zeitfenster können nicht garantiert werden.</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Selbstabholung --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Selbstabholung</h4>
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                <p>Selbstabholung ist nach vorheriger Terminabsprache an unserem Lagerstandort möglich:</p>
                                <p class="font-medium text-gray-900 mt-2">Müller Stahl & Metall GmbH<br>Industriestraße 17, 88250 Weingarten</p>
                                <p class="mt-2">Öffnungszeiten Lager: Mo–Fr 7:00–16:30 Uhr</p>
                                <p class="mt-1">Bei Selbstabholung entfallen die Versandkosten. Bitte vereinbaren Sie einen Abholtermin unter <strong>+49 751 3606-0</strong>. Versandfertig gemeldete Ware muss innerhalb von 3 Werktagen abgeholt werden.</p>
                            </div>
                        </div>

                        {{-- Teillieferungen --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Teillieferungen</h4>
                            <p>Wir sind zu Teillieferungen in zumutbarem Umfang berechtigt. Bei logistisch bedingten Teillieferungen entstehen für Sie keine zusätzlichen Versandkosten.</p>
                        </div>

                        {{-- Verpackung --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Verpackung</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span>Stabstahl und Profile werden unverpackt und nicht gegen Rost geschützt geliefert — sofern nicht anders vereinbart.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span>Langmaterial wird auf Europaletten gebündelt. Bleche werden liegend auf Paletten versendet.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span>Europaletten werden im Tauschverfahren (1:1) gehandhabt. Alternativ wird ein Palettenpfand von 15,00 € pro Palette berechnet, das bei Rückgabe innerhalb von 3 Monaten erstattet wird.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span>Sonderverpackung (z. B. Einölfolie, VCI-Korrosionsschutz) ist auf Anfrage gegen Aufpreis möglich.</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Toleranzen --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Toleranzen</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="font-medium text-gray-900 mb-2">Schnitt- und Längentoleranzen</h5>
                                    <ul class="space-y-1 text-xs">
                                        <li>Sägeschnitt Stabstahl/Rohre: <strong>+/- 2 mm</strong> (DIN ISO 2768-1m)</li>
                                        <li>Sägeschnitt Formstahl/U-Stahl: <strong>+/- 3 mm</strong></li>
                                        <li>CNC-Präzisionssäge: <strong>+/- 1 mm</strong></li>
                                        <li>Brennschnitt Bleche: nach <strong>ISO 9013</strong></li>
                                    </ul>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="font-medium text-gray-900 mb-2">Mengen- und Gewichtstoleranzen</h5>
                                    <ul class="space-y-1 text-xs">
                                        <li>Mehr- oder Minderlieferungen bis <strong>10 % des bestellten Gewichts</strong> gelten als vertragsgemäß.</li>
                                        <li>Gewichtsermittlung erfolgt per Werkswaage oder rechnerisch nach DIN-Normen zzgl. 2,5 % Handelsgewicht.</li>
                                    </ul>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-400">Abweichungen von Maß, Gewicht und Güte sind im Rahmen der jeweils geltenden DIN-/EN-Normen für Stahl und Eisen zulässig.</p>
                        </div>

                        {{-- Gefahrübergang --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Gefahrübergang</h4>
                            <p>Mit der Übergabe der Ware an den Spediteur oder Frachtführer — spätestens mit Verlassen unseres Lagers — geht die Gefahr auf den Käufer über. Dies gilt auch bei Frei-Haus-Lieferungen. Die Lieferung erfolgt EXW Weingarten (Incoterms 2020), sofern nicht anders vereinbart.</p>
                        </div>

                        {{-- Mängelrüge --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Mängelrüge und Reklamation</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span><strong>Erkennbare Mängel</strong> sind unverzüglich, spätestens innerhalb von <strong>7 Tagen</strong> nach Anlieferung, schriftlich anzuzeigen (§ 377 HGB).</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span><strong>Verdeckte Mängel</strong> sind unverzüglich nach Entdeckung, spätestens innerhalb von <strong>3 Monaten</strong> nach Lieferung, anzuzeigen.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span>Transportschäden sind sofort beim Fahrer zu vermerken und innerhalb von 24 Stunden an uns zu melden.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-gray-400 mt-0.5">&#8226;</span>
                                    <span>Beanstandete Ware darf nicht weiterverarbeitet werden und ist zur Begutachtung bereitzuhalten.</span>
                                </li>
                            </ul>
                            <p class="mt-2 text-xs text-gray-400">Unterlässt der Käufer die rechtzeitige Untersuchung und Rüge, gilt die Ware als genehmigt. Gewährleistungsfrist: 12 Monate ab Lieferung.</p>
                        </div>

                        {{-- Liefergebiet --}}
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Liefergebiet</h4>
                            <p>Wir liefern deutschlandweit sowie in angrenzende EU-Länder (AT, CH, NL, BE, LU, FR, PL, CZ). Für Lieferungen außerhalb Deutschlands werden die Versandkosten individuell kalkuliert. Bei Fragen zu internationalen Lieferungen kontaktieren Sie uns bitte direkt.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky mobile Add-to-Cart bar --}}
    <div class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 px-4 py-3 lg:hidden"
         x-data="{ formattedTotal: null }"
         x-init="window.addEventListener('configurator-price-update', (e) => { formattedTotal = e.detail.formattedTotal })">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</p>
                <p class="text-xs text-gray-500">
                    <template x-if="formattedTotal">
                        <span x-text="formattedTotal + ' €'"></span>
                    </template>
                    <template x-if="!formattedTotal">
                        <span>ab {{ number_format($customerPrice, 2, ',', '.') }}&nbsp;€/kg</span>
                    </template>
                </p>
            </div>
            <button @click="window.dispatchEvent(new CustomEvent('trigger-add-to-cart'))"
                    class="shrink-0 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 px-6 rounded-lg text-sm transition-colors whitespace-nowrap">
                In den Warenkorb
            </button>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/islands/product-configurator.tsx'])
    @endpush
</x-app-layout>
