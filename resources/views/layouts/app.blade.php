<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Müller Stahl & Metall GmbH' }} — flinksteel</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col">
            {{-- Header --}}
            <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        {{-- Logo --}}
                        <a href="/" class="flex items-center gap-2 shrink-0">
                            <div class="w-8 h-8 bg-blue-900 rounded flex items-center justify-center">
                                <span class="text-white font-bold text-sm">MS</span>
                            </div>
                            <div class="hidden sm:block">
                                <span class="font-bold text-blue-900 text-lg leading-tight">Müller Stahl & Metall</span>
                            </div>
                        </a>

                        @include('layouts._mega-menu')

                        {{-- Search --}}
                        @php($searchProps = ['searchUrl' => route('search'), 'suggestUrl' => route('search.suggest'), 'initialQuery' => request('q', '')])
                        <div class="flex-1 max-w-xl mx-4 sm:mx-8">
                            <div id="react-search-autocomplete" data-props='@json($searchProps)'></div>
                        </div>

                        {{-- Right: Cart + Account --}}
                        <div class="flex items-center gap-4">
                            {{-- Cart icon with React island --}}
                            <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-blue-900 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <div id="react-cart-summary" data-props='@json(["initialCount" => $cartCount ?? 0])'></div>
                            </a>

                            {{-- Account dropdown --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-900 transition-colors">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->customer->company_name ?? '' }}</p>
                                        <p class="text-xs text-gray-400">{{ Auth::user()->customer->customer_number ?? '' }}</p>
                                    </div>
                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Meine Bestellungen</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Abmelden</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-gray-900 text-gray-400 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-blue-900 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">MS</span>
                                </div>
                                <span class="font-bold text-white">Müller Stahl & Metall</span>
                            </div>
                            <p class="text-sm">Ihr regionaler Stahlpartner seit 1952</p>
                            <p class="text-sm mt-2">Industriestraße 17<br>88250 Weingarten</p>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold mb-3">Sortiment</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('category.show', 'stabstahl') }}" class="hover:text-white transition-colors">Stabstahl</a></li>
                                <li><a href="{{ route('category.show', 'profilstahl') }}" class="hover:text-white transition-colors">Profilstahl</a></li>
                                <li><a href="{{ route('category.show', 'bleche') }}" class="hover:text-white transition-colors">Bleche</a></li>
                                <li><a href="{{ route('category.show', 'rohre') }}" class="hover:text-white transition-colors">Rohre</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold mb-3">Service</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="#" class="hover:text-white transition-colors">Anarbeitung</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Zeugnisse</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Lieferbedingungen</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Kontakt</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold mb-3">Kontakt</h3>
                            <ul class="space-y-2 text-sm">
                                <li>Tel: +49 751 3606-0</li>
                                <li>info@mueller-stahl.de</li>
                                <li class="pt-2">
                                    <span class="text-xs">Mo–Fr: 7:00–17:00 Uhr</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    {{-- Trust row: payment + shipping --}}
                    <div class="border-t border-gray-800 mt-8 pt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Zahlungsarten</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Rechnung · 30 Tage
                                </span>
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    SEPA-Überweisung
                                </span>
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Skonto auf Anfrage
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-3">Versand &amp; Logistik</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Eigene Spedition
                                </span>
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
                                    DHL · DPD
                                </span>
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Frei Haus PLZ&nbsp;88xxx
                                </span>
                                <span class="inline-flex items-center gap-1.5 bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-[11px] font-semibold text-gray-200">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5"/></svg>
                                    Selbstabholung Weingarten
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-800 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <p class="text-xs">&copy; {{ date('Y') }} Müller Stahl & Metall GmbH. Alle Rechte vorbehalten.</p>
                        <div class="flex gap-6 text-xs">
                            <a href="#" class="hover:text-white transition-colors">Impressum</a>
                            <a href="#" class="hover:text-white transition-colors">AGB</a>
                            <a href="#" class="hover:text-white transition-colors">Datenschutz</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <div id="react-toast-notifier"></div>
        @vite(['resources/js/islands/cart-summary.tsx', 'resources/js/islands/search-autocomplete.tsx', 'resources/js/islands/toast-notifier.tsx'])
        @stack('scripts')
    </body>
</html>
