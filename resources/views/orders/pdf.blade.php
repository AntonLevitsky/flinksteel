<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Auftragsbestätigung {{ $order->order_number }} — Müller Stahl & Metall GmbH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; font-size: 11px; color: #1a1a1a; padding: 40px; max-width: 800px; margin: 0 auto; }
        @media print {
            body { padding: 20px; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 20px; font-weight: 700; color: #1e3a8a; }
        .logo-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .company-info { text-align: right; font-size: 9px; color: #666; line-height: 1.6; }
        .addresses { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .address-block { width: 48%; }
        .address-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .address-text { font-size: 11px; line-height: 1.6; }
        .doc-title { font-size: 22px; font-weight: 700; color: #1e3a8a; margin-bottom: 5px; }
        .doc-meta { font-size: 10px; color: #666; margin-bottom: 25px; }
        .doc-meta span { margin-right: 20px; }
        .confirm-banner { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 18px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .confirm-icon { width: 24px; height: 24px; background: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .confirm-icon svg { width: 14px; height: 14px; }
        .confirm-text { font-size: 12px; font-weight: 600; color: #166534; }
        .confirm-sub { font-size: 10px; color: #15803d; margin-top: 1px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #666; }
        thead th:last-child, tbody td:last-child { text-align: right; }
        thead th:nth-child(4), tbody td:nth-child(4) { text-align: right; }
        thead th:nth-child(5), tbody td:nth-child(5) { text-align: right; }
        tbody td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .item-name { font-weight: 600; font-size: 11px; }
        .item-details { font-size: 9px; color: #666; margin-top: 2px; }
        .item-badge { display: inline-block; font-size: 8px; padding: 1px 5px; border-radius: 3px; font-weight: 500; }
        .badge-lager { background: #f0fdf4; color: #166534; }
        .badge-bestell { background: #eff6ff; color: #1e40af; }
        .totals { width: 300px; margin-left: auto; }
        .totals tr td { padding: 4px 10px; font-size: 11px; }
        .totals .total-row td { border-top: 2px solid #1e3a8a; font-weight: 700; font-size: 14px; padding-top: 8px; }
        .delivery-info { display: flex; gap: 30px; margin-bottom: 25px; }
        .delivery-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .delivery-box-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .delivery-box-value { font-size: 11px; font-weight: 600; }
        .footer-notes { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #666; line-height: 1.8; }
        .footer-legal { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #999; text-align: center; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #1e3a8a; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; }
        .print-btn:hover { background: #1e3070; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Als PDF drucken</button>

    <div class="header">
        <div>
            <div class="logo">Müller Stahl & Metall GmbH</div>
            <div class="logo-sub">Ihr regionaler Stahlpartner seit 1952</div>
        </div>
        <div class="company-info">
            Industriestraße 17, 88250 Weingarten<br>
            Tel: +49 751 3606-0<br>
            info@mueller-stahl.de<br>
            USt-IdNr: DE 812 345 678
        </div>
    </div>

    <div class="addresses">
        <div class="address-block">
            <div class="address-label">Rechnungsadresse</div>
            <div class="address-text">
                <strong>{{ $order->billing_company_name ?? $order->customer->company_name }}</strong><br>
                {{ $order->billing_street ?? $order->customer->street }}<br>
                {{ $order->billing_postal_code ?? $order->customer->postal_code }} {{ $order->billing_city ?? $order->customer->city }}<br>
                Kd.-Nr.: {{ $order->customer->customer_number }}<br>
                @if($order->billing_vat_id ?? $order->customer->vat_id) USt-IdNr.: {{ $order->billing_vat_id ?? $order->customer->vat_id }} @endif
            </div>
        </div>
        <div class="address-block">
            <div class="address-label">Lieferadresse</div>
            <div class="address-text">
                <strong>{{ $order->delivery_company_name ?? $order->customer->company_name }}</strong><br>
                {{ $order->delivery_street }}<br>
                {{ $order->delivery_postal_code }} {{ $order->delivery_city }}<br>
                @if($order->delivery_contact_name)Ansprechpartner: {{ $order->delivery_contact_name }}<br>@endif
                @if($order->delivery_contact_phone)Tel.: {{ $order->delivery_contact_phone }}<br>@endif
                @if($order->delivery_window)Zeitfenster: {{ $order->delivery_window }}@endif
            </div>
        </div>
    </div>

    <div class="doc-title">Auftragsbestätigung {{ $order->order_number }}</div>
    <div class="doc-meta">
        <span>Bestelldatum: {{ $order->placed_at->format('d.m.Y, H:i') }} Uhr</span>
        @if($order->po_number)<span>Ihre Referenz: <strong>{{ $order->po_number }}</strong></span>@endif
        <span>Status: {{ $order->getStatusLabel() }}</span>
    </div>

    <div class="confirm-banner">
        <div class="confirm-icon">
            <svg fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <div class="confirm-text">Ihre Bestellung ist eingegangen und wird bearbeitet.</div>
            <div class="confirm-sub">Sie erhalten eine Versandbenachrichtigung, sobald Ihre Ware unser Lager verlässt.</div>
        </div>
    </div>

    <div class="delivery-info">
        <div class="delivery-box">
            <div class="delivery-box-label">Wunschliefertermin</div>
            <div class="delivery-box-value">{{ $order->requested_delivery_date->format('d.m.Y') }}</div>
        </div>
        <div class="delivery-box">
            <div class="delivery-box-label">Versandart</div>
            <div class="delivery-box-value">{{ $order->shipping_option_label ?? ($order->shipping_eur > 0 ? 'Speditionsversand' : 'Frei-Haus-Lieferung') }}</div>
        </div>
        <div class="delivery-box">
            <div class="delivery-box-label">Zahlungsziel</div>
            <div class="delivery-box-value">{{ $order->payment_terms_days ?? $order->customer->payment_terms_days }} Tage netto</div>
            @if($order->payment_due_date)
                <div style="font-size: 9px; color: #666; margin-top: 2px;">fällig am {{ $order->payment_due_date->format('d.m.Y') }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Pos.</th>
                <th>Artikel</th>
                <th style="width: 50px;">Menge</th>
                <th style="width: 70px;">Gewicht</th>
                <th style="width: 90px;">Betrag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        <div class="item-details">
                            {{ $item->product_sku }} · {{ $item->material_grade }}
                            @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                            @if(!empty($item->anarbeitung))
                                · {{ implode(', ', $item->anarbeitung) }}
                            @endif
                            @if($item->certificate_code) · Zeugnis {{ $item->certificate_code }} @endif
                        </div>
                        <div style="margin-top: 3px;">
                            @if($item->product && $item->product->isLagerware())
                                <span class="item-badge badge-lager">Ab Lager — 1–3 WT</span>
                            @else
                                <span class="item-badge badge-bestell">Bestellware — 5–10 WT</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->weight_kg, 1, ',', '.') }} kg</td>
                    <td>{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td style="color: #666;">Warenwert netto</td>
            <td>{{ number_format($order->subtotal_eur, 2, ',', '.') }}&nbsp;€</td>
        </tr>
        @if($order->anarbeitung_total_eur > 0)
            <tr>
                <td style="color: #666;">davon Anarbeitung</td>
                <td style="color: #666;">{{ number_format($order->anarbeitung_total_eur, 2, ',', '.') }}&nbsp;€</td>
            </tr>
        @endif
        @if($order->certificate_total_eur > 0)
            <tr>
                <td style="color: #666;">davon Zeugnisse</td>
                <td style="color: #666;">{{ number_format($order->certificate_total_eur, 2, ',', '.') }}&nbsp;€</td>
            </tr>
        @endif
        <tr>
            <td style="color: #666;">Versandkosten</td>
            <td>{{ $order->shipping_eur > 0 ? number_format($order->shipping_eur, 2, ',', '.') . ' €' : 'kostenfrei' }}</td>
        </tr>
        @php $netTotal = $order->subtotal_eur + $order->shipping_eur; @endphp
        <tr>
            <td style="color: #666;">Nettobetrag</td>
            <td>{{ number_format($netTotal, 2, ',', '.') }}&nbsp;€</td>
        </tr>
        <tr>
            <td style="color: #666;">MwSt. 19 %</td>
            <td>{{ number_format($order->total_eur - $netTotal, 2, ',', '.') }}&nbsp;€</td>
        </tr>
        <tr class="total-row">
            <td>Gesamtbetrag brutto</td>
            <td>{{ number_format($order->total_eur, 2, ',', '.') }}&nbsp;€</td>
        </tr>
    </table>

    <div style="margin-top: 30px; padding: 16px 18px; border: 2px solid #1e3a8a; border-radius: 8px; background: #f0f4ff;">
        <div style="font-size: 12px; font-weight: 700; color: #1e3a8a; margin-bottom: 8px;">Zahlungsinformationen</div>
        <table style="width: 100%; font-size: 10px;">
            <tr>
                <td style="padding: 2px 8px 2px 0; color: #666; width: 28%;">Bank</td>
                <td style="padding: 2px 0; font-weight: 600;">Sparkasse Bodensee</td>
            </tr>
            <tr>
                <td style="padding: 2px 8px 2px 0; color: #666;">IBAN</td>
                <td style="padding: 2px 0; font-family: monospace; font-weight: 600;">DE89 6905 0001 0012 3456 78</td>
            </tr>
            <tr>
                <td style="padding: 2px 8px 2px 0; color: #666;">BIC</td>
                <td style="padding: 2px 0; font-family: monospace; font-weight: 600;">SOLADES1KNZ</td>
            </tr>
            <tr>
                <td style="padding: 2px 8px 2px 0; color: #666;">Verwendungszweck</td>
                <td style="padding: 2px 0; font-family: monospace; font-weight: 700; color: #b45309;">{{ $order->order_number }}@if($order->po_number) / {{ $order->po_number }}@endif</td>
            </tr>
            <tr>
                <td style="padding: 2px 8px 2px 0; color: #666;">Fällig am</td>
                <td style="padding: 2px 0; font-weight: 600;">{{ $order->payment_due_date?->format('d.m.Y') ?? '—' }} ({{ $order->payment_terms_days ?? 30 }} Tage netto)</td>
            </tr>
        </table>
        <div style="font-size: 9px; color: #666; margin-top: 8px; font-style: italic;">
            Bitte stets den Verwendungszweck angeben, damit wir Ihre Zahlung korrekt zuordnen können. Die Rechnung erhalten Sie separat per E-Mail nach Versand der Ware.
        </div>
    </div>

    <div class="footer-notes">
        <strong>Hinweise zur Lieferung:</strong><br>
        · Lagerware wird innerhalb von 1–3 Werktagen versandt. Bestellware: 5–10 Werktage ab Auftragsbestätigung.<br>
        · Bei gemischten Bestellungen (Lager- und Bestellware) kann die Lieferung in Teilsendungen erfolgen. Zusätzliche Kosten entstehen Ihnen dadurch nicht.<br>
        · Lieferung erfolgt frei Bordsteinkante. Bitte stellen Sie ein geeignetes Abladegerät bereit.<br>
        · Gesamtgewicht: ca. {{ number_format($order->items->sum('weight_kg'), 0, ',', '.') }} kg.<br>
        · Es gelten unsere Allgemeinen Liefer- und Zahlungsbedingungen.<br><br>
        <strong>Bei Fragen zu Ihrer Bestellung:</strong><br>
        · Telefon: +49 751 3606-0 (Mo–Fr 7:00–17:00 Uhr)<br>
        · E-Mail: auftraege@mueller-stahl.de<br>
        · Bitte geben Sie Ihre Bestellnummer <strong>{{ $order->order_number }}</strong>@if($order->po_number) bzw. Ihre Referenz <strong>{{ $order->po_number }}</strong>@endif an.
    </div>

    <div class="footer-legal">
        Müller Stahl & Metall GmbH · Industriestraße 17 · 88250 Weingarten · HRB 12345 AG Ravensburg · Geschäftsführer: Thomas Müller<br>
        Sparkasse Bodensee · IBAN: DE89 6905 0001 0012 3456 78 · BIC: SOLADES1KNZ
    </div>
</body>
</html>
