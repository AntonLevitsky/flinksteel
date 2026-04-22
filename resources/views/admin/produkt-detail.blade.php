<x-admin.layout>
    <x-slot:title>{{ $product->name }}</x-slot:title>

    {{-- Back link --}}
    <div class="mb-4">
        <a href="{{ route('admin.produkte') }}" class="text-xs text-blue-900 hover:underline inline-flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Zurück zur Produktliste
        </a>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-6">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Product header --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $product->name }}</h2>
                <div class="flex items-center gap-3 mt-1.5">
                    <span class="text-xs font-mono text-gray-500">{{ $product->sku }}</span>
                    <span class="text-xs text-gray-400">|</span>
                    <span class="text-xs text-gray-500">{{ $product->material->grade ?? '' }}</span>
                    <span class="text-xs text-gray-400">|</span>
                    <span class="text-xs text-gray-500">{{ $product->category->name ?? '' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($product->isLagerware())
                    <span class="text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded">Auf Lager</span>
                @else
                    <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded">Kernsortiment</span>
                @endif
                @if($product->is_available_for_sale)
                    <span class="text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-2 py-1 rounded">Im Verkauf</span>
                @else
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 border border-gray-200 px-2 py-1 rounded">Nicht im Verkauf</span>
                @endif
                @if($product->is_partner_network)
                    <span class="text-xs font-medium text-purple-700 bg-purple-50 px-2 py-1 rounded">Partnernetzwerk</span>
                @endif
            </div>
        </div>

        {{-- ERP source info --}}
        <div class="mt-4 bg-gray-50 rounded-lg p-3 flex items-center gap-3">
            <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"/></svg>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-700">{{ $product->getErpSourceLabel() }}</div>
                <div class="text-[10px] text-gray-400">
                    Letzte ERP-Synchronisation: {{ $product->erp_synced_at ? $product->erp_synced_at->format('d.m.Y H:i') : 'Initial-Import' }}
                    · Bestand, SKU und EK-Preise werden vom ERP gesteuert
                </div>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-4">
            <div>
                <div class="text-[10px] text-gray-500 uppercase">Bestand</div>
                <div class="text-sm font-bold {{ $product->isLagerware() ? 'text-green-700' : 'text-gray-400' }}">
                    {{ $product->isLagerware() ? number_format($product->stock_quantity_kg, 0, ',', '.') . ' kg' : 'Bestellware' }}
                </div>
            </div>
            <div>
                <div class="text-[10px] text-gray-500 uppercase">VK-Preis</div>
                <div class="text-sm font-bold text-gray-900">{{ number_format($product->price_per_kg_eur, 2, ',', '.') }} €/kg</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-500 uppercase">EK-Preis (ERP)</div>
                <div class="text-sm font-bold text-gray-600">{{ number_format($aiSuggestions['erp_cost'], 2, ',', '.') }} €/kg</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-500 uppercase">Aktuelle Marge</div>
                <div class="text-sm font-bold {{ $aiSuggestions['current_margin'] >= 15 ? 'text-green-700' : 'text-amber-600' }}">{{ $aiSuggestions['current_margin'] }} %</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-500 uppercase">Verkauft (gesamt)</div>
                <div class="text-sm font-bold text-gray-900">{{ number_format($totalSold, 0, ',', '.') }} kg</div>
                <div class="text-[10px] text-gray-400">{{ number_format($totalRevenue, 0, ',', '.') }} €</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left column: Edit form --}}
        <div class="lg:col-span-1 space-y-6">
            <form method="POST" action="{{ route('admin.produkt.update', $product->id) }}">
                @csrf

                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Produkt bearbeiten</h3>

                    {{-- Price --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">VK-Preis (€/kg)</label>
                        <input type="number" name="price_per_kg_eur" step="0.01" min="0.01"
                               value="{{ old('price_per_kg_eur', $product->price_per_kg_eur) }}"
                               class="w-full text-sm border-gray-300 rounded-lg py-2 px-3 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        @error('price_per_kg_eur')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Available for sale --}}
                    <div class="mb-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_available_for_sale" value="0">
                            <input type="checkbox" name="is_available_for_sale" value="1"
                                   {{ $product->is_available_for_sale ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-900 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Im Verkauf</span>
                                <p class="text-[10px] text-gray-500">Produkt ist im Kundenportal sichtbar und bestellbar</p>
                            </div>
                        </label>
                    </div>

                    {{-- Featured --}}
                    <div class="mb-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ $product->is_featured ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-900 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Hervorgehoben</span>
                                <p class="text-[10px] text-gray-500">Auf der Startseite prominent angezeigt</p>
                            </div>
                        </label>
                    </div>

                    {{-- Short description --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kurzbeschreibung</label>
                        <input type="text" name="short_description"
                               value="{{ old('short_description', $product->short_description) }}"
                               class="w-full text-sm border-gray-300 rounded-lg py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Long description --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Langbeschreibung</label>
                        <textarea name="long_description" rows="4"
                                  class="w-full text-sm border-gray-300 rounded-lg py-2 px-3 focus:ring-blue-500 focus:border-blue-500">{{ old('long_description', $product->long_description) }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-gray-800 transition-colors">
                        Änderungen speichern
                    </button>
                </div>
            </form>

            {{-- Read-only ERP fields --}}
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">ERP-Stammdaten <span class="text-[10px] text-gray-400 font-normal">(nur lesen)</span></h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">SKU</span>
                        <span class="font-mono text-gray-900">{{ $product->sku }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Material</span>
                        <span class="text-gray-900">{{ $product->material->name ?? '' }} ({{ $product->material->grade ?? '' }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Form</span>
                        <span class="text-gray-900">{{ $product->form->name ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Gewicht/m</span>
                        <span class="font-mono text-gray-900">{{ $product->weight_per_meter_kg ? number_format($product->weight_per_meter_kg, 3, ',', '.') . ' kg' : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Gewicht/Stk</span>
                        <span class="font-mono text-gray-900">{{ $product->weight_per_piece_kg ? number_format($product->weight_per_piece_kg, 2, ',', '.') . ' kg' : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Standardlänge</span>
                        <span class="text-gray-900">{{ $product->standard_length_mm ? number_format($product->standard_length_mm, 0, ',', '.') . ' mm' : 'Festmaß' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Zuschnitt</span>
                        <span class="text-gray-900">{{ $product->is_cut_to_length ? 'Ja' : 'Nein' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Restlängen</span>
                        <span class="text-gray-900">{{ $product->has_restlaengen ? 'Verfügbar' : 'Nein' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Zertifikate</span>
                        <span class="text-gray-900">{{ is_array($product->certifications_available) ? implode(', ', $product->certifications_available) : '—' }}</span>
                    </div>
                    @if($product->supplier_name)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Lieferant</span>
                            <span class="text-gray-900">{{ $product->supplier_name }}</span>
                        </div>
                    @endif
                    @if($product->is_partner_network)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Partner</span>
                            <span class="text-gray-900">{{ $product->partner_source }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column: AI Price Suggestions + Order History --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- AI Price Suggestions --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">KI-Preisvorschläge</h3>
                            <p class="text-[10px] text-gray-500">Basierend auf Marktdaten, Wettbewerb und Nachfrage</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-400">Konfidenz:</span>
                        <div class="flex items-center gap-1">
                            <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $aiSuggestions['confidence'] >= 80 ? 'bg-green-500' : ($aiSuggestions['confidence'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                                     style="width: {{ $aiSuggestions['confidence'] }}%"></div>
                            </div>
                            <span class="text-[10px] font-medium text-gray-600">{{ $aiSuggestions['confidence'] }}%</span>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    {{-- Market context --}}
                    <div class="grid grid-cols-3 gap-4 mb-5">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-[10px] text-gray-500 uppercase">{{ $aiSuggestions['market_index']['label'] }}</div>
                            <div class="flex items-center gap-1 mt-1">
                                <span class="text-sm font-bold text-gray-900">{{ number_format($aiSuggestions['market_index']['factor'], 2) }}</span>
                                @if($aiSuggestions['market_index']['trend'] === 'steigend')
                                    <span class="text-[10px] text-green-600 font-medium">steigend</span>
                                @elseif($aiSuggestions['market_index']['trend'] === 'fallend')
                                    <span class="text-[10px] text-red-600 font-medium">fallend</span>
                                @else
                                    <span class="text-[10px] text-gray-500 font-medium">stabil</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-[10px] text-gray-500 uppercase">Wettbewerb</div>
                            <div class="text-sm font-bold text-gray-900 mt-1">{{ number_format($aiSuggestions['competitor']['avg'], 2, ',', '.') }} €/kg</div>
                            <div class="text-[10px] text-gray-400">{{ number_format($aiSuggestions['competitor']['range'][0], 2, ',', '.') }}–{{ number_format($aiSuggestions['competitor']['range'][1], 2, ',', '.') }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-[10px] text-gray-500 uppercase">Nachfrage-Signal</div>
                            <div class="text-xs font-medium text-gray-700 mt-1">{{ $aiSuggestions['demand']['signal'] }}</div>
                        </div>
                    </div>

                    {{-- Price suggestions --}}
                    <div class="space-y-3">
                        @foreach($aiSuggestions['suggestions'] as $key => $suggestion)
                            <div class="border rounded-lg p-4 {{ $suggestion['is_recommended'] ? 'border-indigo-300 bg-indigo-50/50' : 'border-gray-200' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        @if($suggestion['is_recommended'])
                                            <span class="text-[10px] font-semibold text-indigo-700 bg-indigo-100 px-1.5 py-0.5 rounded uppercase">Empfohlen</span>
                                        @endif
                                        <span class="text-xs font-medium text-gray-700">{{ $suggestion['label'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg font-bold text-gray-900">{{ number_format($suggestion['price'], 2, ',', '.') }} €/kg</span>
                                        <span class="text-xs {{ $suggestion['diff_pct'] > 0 ? 'text-green-600' : ($suggestion['diff_pct'] < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                            {{ $suggestion['diff_pct'] > 0 ? '+' : '' }}{{ $suggestion['diff_pct'] }} %
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 mt-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $suggestion['actual_margin'] >= 20 ? 'bg-green-500' : ($suggestion['actual_margin'] >= 15 ? 'bg-green-400' : ($suggestion['actual_margin'] >= 10 ? 'bg-amber-400' : 'bg-red-400')) }}"
                                             style="width: {{ min(100, $suggestion['actual_margin'] * 3) }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-500 w-20 text-right">Marge: {{ $suggestion['actual_margin'] }} %</span>
                                </div>
                                <div class="mt-3">
                                    <form method="POST" action="{{ route('admin.produkt.update', $product->id) }}">
                                        @csrf
                                        <input type="hidden" name="price_per_kg_eur" value="{{ $suggestion['price'] }}">
                                        <input type="hidden" name="is_available_for_sale" value="{{ $product->is_available_for_sale ? '1' : '0' }}">
                                        <input type="hidden" name="is_featured" value="{{ $product->is_featured ? '1' : '0' }}">
                                        <input type="hidden" name="short_description" value="{{ $product->short_description }}">
                                        <input type="hidden" name="long_description" value="{{ $product->long_description }}">
                                        @if($suggestion['is_recommended'])
                                            <button type="submit" class="w-full text-center bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 text-sm font-semibold rounded-lg transition-colors">
                                                Preis auf {{ number_format($suggestion['price'], 2, ',', '.') }} €/kg übernehmen
                                            </button>
                                        @else
                                            <button type="submit" class="bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 px-4 py-2 text-xs font-semibold rounded-lg transition-colors">
                                                Übernehmen
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Analysis timestamp --}}
                    <div class="mt-4 flex items-center justify-between text-[10px] text-gray-400">
                        <span>Letzte Analyse: {{ $aiSuggestions['last_analysis'] }}</span>
                        <span>EK-Basis: {{ number_format($aiSuggestions['erp_cost'], 2, ',', '.') }} €/kg (ERP)</span>
                    </div>
                </div>
            </div>

            {{-- Anarbeitung options --}}
            @if($product->anarbeitungOptions->isNotEmpty())
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Verfügbare Anarbeitung</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->anarbeitungOptions as $opt)
                            <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg">{{ $opt->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Order history --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Bestellhistorie</h3>
                </div>
                @if($orderHistory->isEmpty())
                    <div class="p-5 text-center">
                        <p class="text-xs text-gray-400">Noch keine Bestellungen für dieses Produkt.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Auftrag</th>
                                    <th class="px-4 py-2 text-left">Kunde</th>
                                    <th class="px-4 py-2 text-right">Menge</th>
                                    <th class="px-4 py-2 text-right">Gewicht</th>
                                    <th class="px-4 py-2 text-right">Umsatz</th>
                                    <th class="px-4 py-2 text-left">Datum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($orderHistory as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5">
                                            <a href="{{ route('admin.auftrag', $item->order->order_number) }}" class="text-blue-900 hover:underline text-xs font-medium">{{ $item->order->order_number }}</a>
                                        </td>
                                        <td class="px-4 py-2.5 text-xs text-gray-600">{{ $item->order->customer->company_name ?? '—' }}</td>
                                        <td class="px-4 py-2.5 text-right text-xs font-mono">{{ $item->quantity }}x</td>
                                        <td class="px-4 py-2.5 text-right text-xs font-mono">{{ number_format($item->weight_kg, 1, ',', '.') }} kg</td>
                                        <td class="px-4 py-2.5 text-right text-xs font-medium">{{ number_format($item->line_total_eur, 2, ',', '.') }} €</td>
                                        <td class="px-4 py-2.5 text-xs text-gray-500">{{ $item->order->placed_at->format('d.m.Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.layout>
