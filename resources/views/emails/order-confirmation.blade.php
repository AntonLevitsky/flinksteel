<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auftragsbestätigung {{ $order->order_number }}</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; color:#1a1a1a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; max-width:620px;">
                    <tr>
                        <td style="padding:28px 32px; background:#1e3a8a; color:#ffffff;">
                            <div style="font-size:18px; font-weight:700;">Müller Stahl & Metall GmbH</div>
                            <div style="font-size:11px; opacity:0.85; margin-top:2px;">Ihr regionaler Stahlpartner seit 1952</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            <div style="width:48px; height:48px; background:#dcfce7; border-radius:50%; display:inline-block; text-align:center; line-height:48px; margin-bottom:16px;">
                                <span style="color:#16a34a; font-size:24px; font-weight:700;">✓</span>
                            </div>
                            <h1 style="font-size:22px; font-weight:700; color:#1a1a1a; margin:0 0 8px 0;">Vielen Dank für Ihre Bestellung</h1>
                            <p style="font-size:14px; color:#4b5563; margin:0 0 4px 0;">Bestellnummer: <strong>{{ $order->order_number }}</strong></p>
                            @if($order->po_number)
                                <p style="font-size:14px; color:#4b5563; margin:0 0 4px 0;">Ihre Referenz: <strong>{{ $order->po_number }}</strong></p>
                            @endif
                            <p style="font-size:13px; color:#6b7280; margin:12px 0 0 0;">Wir haben Ihre Bestellung erhalten und prüfen sie umgehend. Eine Versandbenachrichtigung erhalten Sie, sobald die Ware unser Lager verlässt.</p>
                        </td>
                    </tr>

                    {{-- Delivery / Payment info --}}
                    <tr>
                        <td style="padding:0 32px 24px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding:16px 0; border-right:1px solid #e5e7eb; width:50%; vertical-align:top;">
                                        <div style="font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px;">Lieferadresse</div>
                                        <div style="font-size:13px; color:#1a1a1a; margin-top:6px; line-height:1.5;">
                                            <strong>{{ $order->delivery_company_name ?? $order->customer->company_name }}</strong><br>
                                            {{ $order->delivery_street }}<br>
                                            {{ $order->delivery_postal_code }} {{ $order->delivery_city }}
                                            @if($order->delivery_contact_phone)<br><span style="color:#6b7280; font-size:11px;">Tel.: {{ $order->delivery_contact_phone }}</span>@endif
                                        </div>
                                    </td>
                                    <td style="padding:16px 0 16px 16px; width:50%; vertical-align:top;">
                                        <div style="font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px;">Wunschliefertermin</div>
                                        <div style="font-size:13px; color:#1a1a1a; margin-top:6px;">{{ $order->requested_delivery_date->format('d.m.Y') }}</div>
                                        @if($order->shipping_option_label)
                                            <div style="font-size:11px; color:#6b7280; margin-top:2px;">{{ $order->shipping_option_label }}</div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Items --}}
                    <tr>
                        <td style="padding:0 32px 24px;">
                            <div style="font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">Positionen ({{ $order->items->count() }})</div>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td style="padding:10px 0; border-bottom:1px solid #f1f5f9;">
                                            <div style="font-size:13px; font-weight:600; color:#1a1a1a;">{{ $item->product_name }}</div>
                                            <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                                                {{ $item->product_sku }} · {{ $item->material_grade }} · {{ $item->quantity }}x
                                                @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                                                · {{ number_format($item->weight_kg, 1, ',', '.') }} kg
                                            </div>
                                        </td>
                                        <td style="padding:10px 0; border-bottom:1px solid #f1f5f9; text-align:right; font-size:13px; font-weight:600; white-space:nowrap; vertical-align:top;">
                                            {{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
                                <tr><td style="font-size:12px; color:#6b7280; padding:3px 0;">Warenwert</td><td style="font-size:12px; text-align:right; padding:3px 0;">{{ number_format($order->subtotal_eur, 2, ',', '.') }}&nbsp;€</td></tr>
                                <tr><td style="font-size:12px; color:#6b7280; padding:3px 0;">Versand</td><td style="font-size:12px; text-align:right; padding:3px 0;">{{ $order->shipping_eur > 0 ? number_format($order->shipping_eur, 2, ',', '.') . ' €' : 'kostenfrei' }}</td></tr>
                                @php $net = $order->subtotal_eur + $order->shipping_eur; @endphp
                                <tr><td style="font-size:12px; color:#6b7280; padding:3px 0;">MwSt. 19 %</td><td style="font-size:12px; text-align:right; padding:3px 0;">{{ number_format($order->total_eur - $net, 2, ',', '.') }}&nbsp;€</td></tr>
                                <tr><td style="font-size:15px; font-weight:700; padding:8px 0 0 0; border-top:2px solid #1e3a8a;">Gesamtsumme</td><td style="font-size:15px; font-weight:700; text-align:right; padding:8px 0 0 0; border-top:2px solid #1e3a8a;">{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;€</td></tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Payment Instructions --}}
                    <tr>
                        <td style="padding:0 32px 24px;">
                            <div style="background:#eff6ff; border:2px solid #bfdbfe; border-radius:8px; padding:18px;">
                                <div style="font-size:14px; font-weight:700; color:#1e3a8a; margin-bottom:10px;">Zahlungsinformationen</div>
                                <table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
                                    <tr><td style="padding:3px 0; color:#6b7280; width:130px;">Empfänger</td><td style="padding:3px 0; font-weight:600;">Müller Stahl & Metall GmbH</td></tr>
                                    <tr><td style="padding:3px 0; color:#6b7280;">Bank</td><td style="padding:3px 0; font-weight:600;">Sparkasse Bodensee</td></tr>
                                    <tr><td style="padding:3px 0; color:#6b7280;">IBAN</td><td style="padding:3px 0; font-family:monospace; font-weight:600;">DE89 6905 0001 0012 3456 78</td></tr>
                                    <tr><td style="padding:3px 0; color:#6b7280;">BIC</td><td style="padding:3px 0; font-family:monospace; font-weight:600;">SOLADES1KNZ</td></tr>
                                    <tr><td style="padding:3px 0; color:#6b7280;">Verwendungszweck</td><td style="padding:3px 0; font-family:monospace; font-weight:700; color:#b45309;">{{ $order->order_number }}@if($order->po_number) / {{ $order->po_number }}@endif</td></tr>
                                    <tr><td style="padding:3px 0; color:#6b7280;">Fällig am</td><td style="padding:3px 0; font-weight:600;">{{ $order->payment_due_date?->format('d.m.Y') ?? '—' }} ({{ $order->payment_terms_days }} Tage netto)</td></tr>
                                </table>
                                <div style="font-size:11px; color:#6b7280; margin-top:10px; font-style:italic;">Bitte stets den Verwendungszweck angeben. Die Rechnung erhalten Sie separat nach Warenversand.</div>
                            </div>
                        </td>
                    </tr>

                    {{-- CTA --}}
                    <tr>
                        <td style="padding:0 32px 32px; text-align:center;">
                            <a href="{{ route('orders.show', $order->order_number) }}" style="display:inline-block; background:#1e3a8a; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:8px; font-size:14px; font-weight:600;">
                                Bestellung online ansehen
                            </a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px; background:#f8fafc; border-top:1px solid #e5e7eb; font-size:11px; color:#6b7280; line-height:1.6;">
                            <strong style="color:#374151;">Fragen zur Bestellung?</strong><br>
                            Telefon: <a href="tel:+4975136060" style="color:#1e3a8a; text-decoration:none;">+49 751 3606-0</a> (Mo–Fr 7:00–17:00 Uhr)<br>
                            E-Mail: <a href="mailto:auftraege@mueller-stahl.de" style="color:#1e3a8a; text-decoration:none;">auftraege@mueller-stahl.de</a><br>
                            Bitte geben Sie Ihre Bestellnummer <strong>{{ $order->order_number }}</strong> an.

                            <div style="margin-top:16px; padding-top:12px; border-top:1px solid #e5e7eb; font-size:10px; color:#9ca3af;">
                                Müller Stahl & Metall GmbH · Industriestraße 17 · 88250 Weingarten<br>
                                HRB 12345 AG Ravensburg · USt-IdNr.: DE 812 345 678 · Geschäftsführer: Thomas Müller
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
