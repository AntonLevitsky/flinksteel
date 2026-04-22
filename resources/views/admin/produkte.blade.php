<x-admin.layout>
    <x-slot:title>Produktverwaltung</x-slot:title>

    {{-- Info banner: ERP source --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-blue-900">Daten aus ERP-System</h3>
                <p class="text-xs text-blue-700 mt-0.5">Produkte, Bestände und EK-Preise werden automatisch aus dem ERP-System synchronisiert. Hier steuern Sie, welche Produkte im Kundenportal zum Verkauf stehen und passen VK-Preise an.</p>
            </div>
        </div>
    </div>

    {{-- Summary tiles --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Gesamt (ERP)</div>
            <div class="text-xl font-bold text-gray-900">{{ $counts['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Auf Lager</div>
            <div class="text-xl font-bold text-green-700">{{ $counts['lager'] }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Kernsortiment</div>
            <div class="text-xl font-bold text-blue-700">{{ $counts['bestell'] }}</div>
            <div class="text-[10px] text-gray-400">nicht auf Lager</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Im Verkauf</div>
            <div class="text-xl font-bold text-green-700">{{ $counts['for_sale'] }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Nicht im Verkauf</div>
            <div class="text-xl font-bold {{ $counts['not_for_sale'] > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ $counts['not_for_sale'] }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-[10px] text-gray-500 uppercase">Partnernetzwerk</div>
            <div class="text-xl font-bold text-purple-700">{{ $counts['partner'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.produkte') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 uppercase">Bestand:</label>
                <select name="bestand" class="text-sm border-gray-300 rounded-lg py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Alle</option>
                    <option value="lager" {{ $stockFilter === 'lager' ? 'selected' : '' }}>Auf Lager</option>
                    <option value="bestell" {{ $stockFilter === 'bestell' ? 'selected' : '' }}>Kernsortiment (Bestellware)</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 uppercase">Verkauf:</label>
                <select name="verkauf" class="text-sm border-gray-300 rounded-lg py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Alle</option>
                    <option value="aktiv" {{ $saleFilter === 'aktiv' ? 'selected' : '' }}>Im Verkauf</option>
                    <option value="inaktiv" {{ $saleFilter === 'inaktiv' ? 'selected' : '' }}>Nicht im Verkauf</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 uppercase">Kategorie:</label>
                <select name="kategorie" class="text-sm border-gray-300 rounded-lg py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Alle</option>
                    @foreach($categories as $cat)
                        <optgroup label="{{ $cat->name }}">
                            @foreach($cat->children as $child)
                                <option value="{{ $child->slug }}" {{ $categoryFilter === $child->slug ? 'selected' : '' }}>{{ $child->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-gray-800 transition-colors">Filtern</button>
            @if($stockFilter || $saleFilter || $categoryFilter)
                <a href="{{ route('admin.produkte') }}" class="text-xs text-gray-500 hover:text-gray-700">Filter zurücksetzen</a>
            @endif
        </form>
    </div>

    {{-- Product table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Artikel</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Quelle</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Bestand</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">VK €/kg</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Marge</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Verkauf</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Typ</th>
                        <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($products as $p)
                        <tr class="hover:bg-gray-50 {{ !$p->is_available_for_sale ? 'opacity-60' : '' }}">
                            <td class="px-3 py-2.5">
                                <div class="font-medium text-gray-900 text-xs">{{ $p->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $p->sku }} · {{ $p->material->grade ?? '' }}</div>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="text-[10px] text-gray-500 max-w-[180px]">
                                    @if($p->isLagerware())
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            ERP — Eigenlager
                                        </span>
                                    @elseif($p->is_partner_network)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full"></span>
                                            Partnernetzwerk
                                        </span>
                                        <div class="text-[9px] text-gray-400 truncate">{{ $p->partner_source }}</div>
                                    @else
                                        <span class="inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                            Kernsortiment
                                        </span>
                                        @if($p->supplier_name)
                                            <div class="text-[9px] text-gray-400 truncate">{{ $p->supplier_name }}</div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                @if($p->isLagerware())
                                    <span class="text-xs font-medium text-green-700">{{ number_format($p->stock_quantity_kg, 0, ',', '.') }} kg</span>
                                @else
                                    <span class="text-[10px] text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-right text-xs font-mono">{{ number_format($p->price_per_kg_eur, 2, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right text-xs {{ $p->current_margin >= 15 ? 'text-green-700' : 'text-amber-600' }}">{{ $p->current_margin }} %</td>
                            <td class="px-3 py-2.5 text-center">
                                @if($p->is_available_for_sale)
                                    <span class="text-[10px] font-medium text-green-700 bg-green-50 px-1.5 py-0.5 rounded">Aktiv</span>
                                @else
                                    <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">Inaktiv</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                @if($p->isLagerware())
                                    <span class="text-[10px] font-medium text-green-700 bg-green-50 px-1.5 py-0.5 rounded">Lager</span>
                                @else
                                    <span class="text-[10px] font-medium text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">Bestell</span>
                                @endif
                                @if($p->is_partner_network)
                                    <span class="text-[10px] font-medium text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded ml-0.5">Netzw.</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <a href="{{ route('admin.produkt', $p->id) }}" class="text-xs text-blue-900 hover:underline font-medium">Bearbeiten</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
            <p class="text-sm text-gray-500">Keine Produkte gefunden für die ausgewählten Filter.</p>
        </div>
    @endif
</x-admin.layout>
