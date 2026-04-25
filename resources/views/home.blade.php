<x-app-layout>
    <x-slot:title>Startseite</x-slot:title>

    {{-- Hero Banner --}}
    <section class="bg-blue-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-3xl">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight mb-4">
                    Über 120.000 Artikel in 50&nbsp;Werkstoffen
                </h1>
                <p class="text-lg sm:text-xl text-blue-200 mb-8">
                    Schnell, verfügbar, online — Ihr regionaler Stahlpartner seit 1952.
                </p>
                <a href="{{ route('category.show', 'stabstahl') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors">
                    Sortiment entdecken
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- USP Bar --}}
    <section class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <ul class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-6 gap-y-4 text-sm">
                <li class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong class="text-gray-900">Ohne Mindestbestellwert</strong></span>
                </li>
                <li class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong class="text-gray-900">Lieferung 1–3 Werktage</strong> ab Lager</span>
                </li>
                <li class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong class="text-gray-900">Kauf auf Rechnung</strong> · 30 Tage netto</span>
                </li>
                <li class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong class="text-gray-900">Zuschnitt &amp; Anarbeitung</strong> inklusive</span>
                </li>
                <li class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><strong class="text-gray-900">ISO 9001-zertifiziert</strong></span>
                </li>
            </ul>
        </div>
    </section>

    {{-- Category Tiles --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Werkstoffgruppen</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="group bg-white rounded-xl border border-gray-200 p-6 text-center hover:border-blue-900 hover:shadow-md transition-all">
                    <div class="flex justify-center mb-3">
                        <x-category-icon :icon="$cat->icon" class="w-14 h-14 group-hover:scale-110 transition-transform" />
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors">{{ $cat->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $cat->products_count ?? $cat->children->count() }} {{ $cat->children->count() > 0 ? 'Unterkategorien' : 'Artikel' }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Products --}}
    <section class="bg-white border-y border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Empfohlene Produkte</h2>
                <a href="{{ route('search') }}?q=" class="text-sm text-blue-900 hover:underline">Alle Produkte &rarr;</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featured as $product)
                    <a href="{{ route('product.show', $product->id) }}" class="group bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all overflow-hidden">
                        <div class="aspect-square bg-gray-50 p-8 flex items-center justify-center relative">
                            <x-product-svg :form="$product->form->slug" class="w-32 h-32 opacity-80 group-hover:opacity-100 transition-opacity" />
                            @if($product->isBestellware())
                                <span class="absolute top-3 right-3 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded">Bestellware</span>
                            @endif
                        </div>
                        <div class="p-4">
                            <span class="text-xs font-medium text-blue-900 bg-blue-50 px-2 py-0.5 rounded">{{ $product->material->grade }}</span>
                            @if($product->has_restlaengen)
                                <span class="text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded ml-1">Restlängen</span>
                            @endif
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 group-hover:text-blue-900 transition-colors line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $product->short_description }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-900">ab {{ number_format($product->price_per_kg_eur, 2, ',', '.') }}&nbsp;€/kg</span>
                                @if($product->isLagerware())
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $product->stock_quantity_kg > 500 ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                        {{ $product->getStockStatus() }}
                                    </span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">5–10 Werktage</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trust Indicators --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Lieferung ab Lager</p>
                    <p class="text-xs text-gray-500">Versand innerhalb von 24h</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">3.1 Zeugnisse</p>
                    <p class="text-xs text-gray-500">Abnahmeprüfzeugnisse verfügbar</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Anarbeitung</p>
                    <p class="text-xs text-gray-500">Zuschnitt, Entgraten, Verzinken</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Persönlicher Ansprechpartner</p>
                    <p class="text-xs text-gray-500">Fachberatung für Ihr Projekt</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Branchen band --}}
    <section class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Branchen, die uns vertrauen</h2>
                <p class="text-sm text-gray-500 mt-2">Seit über 70 Jahren Werkstoffpartner für Industrie und Handwerk in Süddeutschland.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                {{-- Stahlbau --}}
                <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-xl">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1m-5 8v-4a1 1 0 011-1h2a1 1 0 011 1v4"/></svg>
                    <span class="text-sm font-semibold text-gray-900">Stahlbau</span>
                </div>
                {{-- Maschinenbau --}}
                <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-xl">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-sm font-semibold text-gray-900">Maschinenbau</span>
                </div>
                {{-- Anlagenbau --}}
                <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-xl">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-sm font-semibold text-gray-900">Anlagenbau</span>
                </div>
                {{-- Fahrzeugbau --}}
                <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-xl">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                    <span class="text-sm font-semibold text-gray-900">Fahrzeugbau</span>
                </div>
                {{-- Metallhandwerk --}}
                <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-xl">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span class="text-sm font-semibold text-gray-900">Metallhandwerk</span>
                </div>
                {{-- Hoch- & Tiefbau --}}
                <div class="flex flex-col items-center text-center p-4 bg-gray-50 rounded-xl">
                    <svg class="w-10 h-10 text-blue-900 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="text-sm font-semibold text-gray-900">Hoch- &amp; Tiefbau</span>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
