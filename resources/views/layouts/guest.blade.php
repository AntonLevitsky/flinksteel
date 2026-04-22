<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Anmelden — Müller Stahl & Metall</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-brand-bg">
            <div class="mb-6 text-center">
                <a href="/" class="flex items-center justify-center gap-3">
                    <div class="w-12 h-12 bg-blue-900 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">MS</span>
                    </div>
                    <div>
                        <span class="font-bold text-blue-900 text-2xl">Müller Stahl & Metall</span>
                    </div>
                </a>
                <p class="mt-2 text-sm text-gray-500">Ihr regionaler Stahlpartner seit 1952</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-lg overflow-hidden sm:rounded-xl border border-gray-100">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-gray-400">&copy; {{ date('Y') }} Müller Stahl & Metall GmbH</p>
        </div>
    </body>
</html>
