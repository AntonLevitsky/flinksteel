<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Angebot {{ $angebotNr }} — Müller Stahl & Metall GmbH</title>
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
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #666; }
        thead th:last-child, tbody td:last-child { text-align: right; }
        thead th:nth-child(4), tbody td:nth-child(4) { text-align: right; }
        tbody td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .item-name { font-weight: 600; font-size: 11px; }
        .item-details { font-size: 9px; color: #666; margin-top: 2px; }
        .item-badge { display: inline-block; font-size: 8px; padding: 1px 5px; border-radius: 3px; font-weight: 500; }
        .badge-lager { background: #f0fdf4; color: #166534; }
        .badge-bestell { background: #eff6ff; color: #1e40af; }
        .badge-partner { background: #faf5ff; color: #6b21a8; }
        .totals { width: 280px; margin-left: auto; }
        .totals tr td { padding: 4px 10px; font-size: 11px; }
        .totals .total-row td { border-top: 2px solid #1e3a8a; font-weight: 700; font-size: 14px; padding-top: 8px; }
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
            <div class="address-label">Angebot an</div>
            <div class="address-text">
                <strong>{{ $customer->company_name }}</strong><br>
                {{ $customer->street }}<br>
                {{ $customer->postal_code }} {{ $customer->city }}<br>
                Kd.-Nr.: {{ $customer->customer_number }}<br>
                @if($customer->vat_id) USt-IdNr.: {{ $customer->vat_id }} @endif
            </div>
        </div>
        <div class="address-block">
            <div class="address-label">Ansprechpartner</div>
            <div class="address-text">
                {{ $user->name }}<br>
                {{ $user->email }}
            </div>
        </div>
    </div>

    <div class="doc-title">Angebot {{ $angebotNr }}</div>
    <div class="doc-meta">
        <span>Datum: {{ now()->format('d.m.Y') }}</span>
        <span>Gültig bis: {{ $validUntil->format('d.m.Y') }}</span>
        @if($customer->price_multiplier < 1.0)
            <span>Kondition: {{ $customer->getPriceTierLabel() }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Pos.</th>
                <th>Artikel</th>
                <th style="width: 50px;">Menge</th>
                <th style="width: 80px;">Einzelpreis</th>
                <th style="width: 90px;">Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-details">
                            {{ $item->product->sku }} · {{ $item->product->material->grade }}
                            @if($item->length_mm) · {{ number_format($item->length_mm, 0, ',', '.') }} mm @endif
                            · {{ number_format($item->product->calculateWeight($item->quantity, $item->length_mm), 1, ',', '.') }} kg
                            @if(!empty($item->anarbeitung))
                                @php $names = \App\Models\AnarbeitungOption::whereIn('code', $item->anarbeitung)->pluck('name_de')->toArray(); @endphp
                                · {{ implode(', ', $names) }}
                            @endif
                            @if($item->certificate_code) · Zeugnis {{ $item->certificate_code }} @endif
                        </div>
                        <div style="margin-top: 3px;">
                            @if($item->product->isLagerware())
                                <span class="item-badge badge-lager">Ab Lager</span>
                            @else
                                <span class="item-badge badge-bestell">Bestellware 5–10 WT</span>
                            @endif
                            @if($item->product->is_partner_network)
                                <span class="item-badge badge-partner">Partnernetzwerk</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price_eur, 2, ',', '.') }}&nbsp;€</td>
                    <td>{{ number_format($item->line_total_eur, 2, ',', '.') }}&nbsp;€</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td style="color: #666;">Zwischensumme netto</td>
            <td>{{ number_format($subtotal, 2, ',', '.') }}&nbsp;€</td>
        </tr>
        <tr>
            <td style="color: #666;">zzgl. Versandkosten</td>
            <td style="color: #666;">nach Aufwand</td>
        </tr>
        <tr>
            <td style="color: #666;">zzgl. MwSt. 19 %</td>
            <td>{{ number_format($subtotal * 0.19, 2, ',', '.') }}&nbsp;€</td>
        </tr>
        <tr class="total-row">
            <td>Angebotssumme brutto</td>
            <td>{{ number_format($subtotal * 1.19, 2, ',', '.') }}&nbsp;€</td>
        </tr>
    </table>

    <div class="footer-notes">
        <strong>Hinweise:</strong><br>
        · Preise netto ab Lager Weingarten, zzgl. gesetzlicher MwSt. und Versandkosten.<br>
        · Angebot gültig bis {{ $validUntil->format('d.m.Y') }}, vorbehaltlich Zwischenverkauf.<br>
        · Lieferzeit Lagerware: 1–3 Werktage. Bestellware: 5–10 Werktage ab Auftragseingang.<br>
        · Gesamtgewicht ca. {{ number_format($totalWeight, 0, ',', '.') }} kg.<br>
        · Es gelten unsere Allgemeinen Liefer- und Zahlungsbedingungen.
    </div>

    <div class="footer-legal">
        Müller Stahl & Metall GmbH · Industriestraße 17 · 88250 Weingarten · HRB 12345 AG Ravensburg · Geschäftsführer: Thomas Müller
    </div>
</body>
</html>
