<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Verwaltung' }} — Müller Stahl & Metall</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-56 bg-gray-900 text-gray-300 flex flex-col shrink-0 sticky top-0 h-screen">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-900 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-xs">MS</span>
                    </div>
                    <div>
                        <div class="text-white text-sm font-semibold leading-tight">Müller Stahl</div>
                        <div class="text-[10px] text-gray-500">Verwaltung</div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 py-3 space-y-0.5 px-2">
                @php $current = request()->route()->getName(); @endphp

                <a href="{{ route('admin.cockpit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ $current === 'admin.cockpit' ? 'bg-gray-800 text-white' : 'hover:bg-gray-800/50 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Cockpit
                </a>
                <a href="{{ route('admin.auftraege') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ str_starts_with($current, 'admin.auftrag') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800/50 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Aufträge
                </a>
                <a href="{{ route('admin.lager') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ $current === 'admin.lager' ? 'bg-gray-800 text-white' : 'hover:bg-gray-800/50 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Lagerbestand
                </a>
                <a href="{{ route('admin.produkte') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ str_starts_with($current, 'admin.produkt') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800/50 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Produkte
                </a>
                <a href="{{ route('admin.kunden') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ str_starts_with($current, 'admin.kunde') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800/50 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kunden
                </a>
                <a href="{{ route('admin.statistik') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ $current === 'admin.statistik' ? 'bg-gray-800 text-white' : 'hover:bg-gray-800/50 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Statistik
                </a>
            </nav>

            <div class="p-3 border-t border-gray-800">
                <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs text-gray-500 hover:text-gray-300 hover:bg-gray-800/50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Zum Kundenportal
                </a>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shrink-0">
                <h1 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Verwaltung' }}</h1>
                <div class="text-xs text-gray-400">{{ now()->format('d.m.Y, H:i') }} Uhr · {{ Auth::user()->name }}</div>
            </header>

            <main class="flex-1 p-6 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
