<x-app-layout>
    <x-slot:title>Bestellung bestätigt</x-slot:title>

    @php
        $netTotal = $order->subtotal_eur + $order->shipping_eur;
        $vat = $order->total_eur - $netTotal;
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Success header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Vielen Dank — Ihre Bestellung ist eingegangen.</h1>
            <p class="text-gray-600 mt-3">
                Bestellnummer <strong class="text-gray-900">{{ $order->order_number }}</strong>
                @if($order->po_number) · Ihre Referenz <strong class="text-gray-900">{{ $order->po_number }}</strong> @endif
            </p>
            <p class="text-sm text-gray-500 mt-1">Eine Auftragsbestätigung wurde an <strong>{{ $order->billing_email ?? Auth::user()->email }}</strong> versandt.</p>
        </div>

        {{-- Timeline --}}
        <div class="mb-8">
            <x-order-timeline :order="$order" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: order details --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Next steps --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">So geht es weiter</h2>
                    <ol class="space-y-3">
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-blue-100 text-blue-900 text-xs font-bold flex items-center justify-center">1</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Auftragsprüfung</p>
                                <p class="text-xs text-gray-500">Wir prüfen Verfügbarkeit und Wunschliefertermin — Sie erhalten eine finale Bestätigung, meist innerhalb eines Werktags.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-blue-100 text-blue-900 text-xs font-bold flex items-center justify-center">2</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Versand & Avisierung</p>
                                <p class="text-xs text-gray-500">Unser Fahrer ruft vor Anlieferung die angegebene Kontaktnummer an.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="w-6 h-6 shrink-0 rounded-full bg-blue-100 text-blue-900 text-xs font-bold flex items-center justify-center">3</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Rechnung per E-Mail</p>
                                <p class="text-xs text-gray-500">Sie erhalten die Rechnung als PDF nach Versand. Zahlungsziel <strong>{{ $order->payment_terms_days }} Tage netto</strong>, fällig am <strong>{{ $order->payment_due_date?->format('d.m.Y') ?? '—' }}</strong>.</p>
                            </div>
                        </li>
                    </ol>
                </div>

                {{-- Payment instructions (key B2B feature) --}}
                <div class="bg-blue-50/60 rounded-xl border-2 border-blue-200 p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-900 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Zahlungsinformationen</h2>
                            <p class="text-xs text-gray-600 mt-0.5">Zahlung per Überweisung auf folgendes Konto — erst nach Rechnungseingang fällig.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 font-semibold">Empfänger</p>
                            <p class="font-medium text-gray-900 mt-1">Müller Stahl & Metall GmbH</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 font-semibold">Bank</p>
                            <p class="font-medium text-gray-900 mt-1">Sparkasse Bodensee</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 font-semibold">IBAN</p>
                            <p class="font-mono font-medium text-gray-900 mt-1 text-[13px]">DE89 6905 0001 0012 3456 78</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <p class="text-[10px] uppercase tracking-wide text-gray-500 font-semibold">BIC</p>
                            <p class="font-mono font-medium text-gray-900 mt-1 text-[13px]">SOLADES1KNZ</p>
                        </div>
                        <div class="bg-amber-50 border border-amber-300 rounded-lg p-4 sm:col-span-2">
                            <p class="text-[10px] uppercase tracking-wide text-amber-800 font-semibold">Verwendungszweck (wichtig)</p>
                            <p class="font-mono font-bold text-gray-900 mt-1 text-base">{{ $order->order_number }}@if($order->po_number) / {{ $order->po_number }}@endif</p>
                            <p class="text-xs text-amber-800 mt-1">Bitte immer angeben, damit Ihre Zahlung automatisch zugeordnet wird.</p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500">Zahlungsziel</p>
                            <p class="font-semibold text-gray-900">{{ $order->payment_terms_days }} Tage netto</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Fällig am</p>
                            <p class="font-semibold text-gray-900">{{ $order->payment_due_date?->format('d.m.Y') ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Addresses --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Lieferadresse</h3>
                        @if($order->delivery_company_name)
                            <p class="text-sm font-medium text-gray-900">{{ $order->delivery_company_name }}</p>
                        @endif
                        <p class="text-sm text-gray-700">{{ $order->delivery_street }}</p>
                        <p class="text-sm text-gray-700">{{ $order->delivery_postal_code }} {{ $order->delivery_city }}</p>
                        @if($order->delivery_contact_name || $order->delivery_contact_phone)
                            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-0.5">
                                @if($order->delivery_contact_name)<p>Ansprechpartner: <span class="text-gray-700">{{ $order->delivery_contact_name }}</span></p>@endif
                                @if($order->delivery_contact_phone)<p>Telefon: <span class="text-gray-700">{{ $order->delivery_contact_phone }}</span></p>@endif
                                @if($order->delivery_window)<p>Zeitfenster: <span class="text-gray-700">{{ $order->delivery_window }}</span></p>@endif
                            </div>
                        @endif
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Rechnungsadresse</h3>
                        <p class="text-sm font-medium text-gray-900">{{ $order->billing_company_name ?? $order->customer->company_name }}</p>
                        <p class="text-sm text-gray-700">{{ $order->billing_street ?? $order->customer->street }}</p>
                        <p class="text-sm text-gray-700">{{ $order->billing_postal_code ?? $order->customer->postal_code }} {{ $order->billing_city ?? $order->customer->city }}</p>
                        <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-0.5">
                            <p>Kd.-Nr.: <span class="text-gray-700">{{ $order->customer->customer_number }}</span></p>
                            @if($order->billing_vat_id)<p>USt-IdNr.: <span class="text-gray-700">{{ $order->billing_vat_id }}</span></p>@endif
                        </div>
                    </div>
                </div>

                {{-- Order items --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Positionen ({{ $order->items->count() }})</h2>
                        <p class="text-xs text-gray-500">
                            Wunschliefertermin: <strong class="text-gray-700">{{ $order->requested_delivery_date->format('d.m.Y') }}</strong>
                            @if($order->shipping_option_label) · {{ $order->shipping_option_label }} @endif
                        </p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="py-3 flex justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $item->product_sku }} · {{ $item->material_grade }} · {{ $item->quantity }}x
                                        @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                                        · {{ number_format($item->weight_kg, 1, ',', '.') }} kg
                                    </p>
                                    @if(!empty($item->anarbeitung))
                                        <p class="text-xs text-blue-700 mt-0.5">Anarbeitung: {{ implode(', ', $item->anarbeitung) }}</p>
                                    @endif
                                    @if($item->certificate_code)
                                        <p class="text-xs text-green-700 mt-0.5">Zeugnis {{ $item->certificate_code }}</p>
                                    @endif
                                </div>
                                <span class="text-sm font-medium whitespace-nowrap">{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4 space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Warenwert</span>
                            <span>{{ number_format($order->subtotal_eur, 2, ',', '.') }}&nbsp;€</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Versand</span>
                            <span>{{ $order->shipping_eur > 0 ? number_format($order->shipping_eur, 2, ',', '.') . ' €' : 'kostenfrei' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">MwSt. 19 %</span>
                            <span>{{ number_format($vat, 2, ',', '.') }}&nbsp;€</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Gesamt (brutto)</span>
                            <span>{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;€</span>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Ihre Bemerkungen</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Right: actions & contact --}}
            <div>
                <div class="sticky top-20 space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-3">
                        <a href="{{ route('orders.pdf', $order->order_number) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-medium text-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Auftragsbestätigung (PDF)
                        </a>
                        <a href="{{ route('orders.show', $order->order_number) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                            Bestellung ansehen
                        </a>
                        <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors">
                            Weiter einkaufen →
                        </a>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Fragen zur Bestellung?</h3>
                        <p class="text-xs text-gray-500 mb-3">Bitte geben Sie bei Rückfragen Bestellnummer <strong class="text-gray-700">{{ $order->order_number }}</strong> an.</p>
                        <a href="tel:+4975136060" class="flex items-center gap-2 text-sm font-semibold text-blue-900 hover:underline mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            +49 751 3606-0
                        </a>
                        <a href="mailto:auftraege@mueller-stahl.de" class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            auftraege@mueller-stahl.de
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
