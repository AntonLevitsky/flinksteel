# Build Notes — flinksteel Prototype

## Architecture Decisions

### Session-based cart over guest-cookie cart
Auth is required for all pages, so a DB-backed cart tied to the user model is simpler and more robust than cookie-based guest carts. Cart items persist across sessions automatically.

### React islands vs full SPA
Only 4 components need client-side interactivity (product configurator, cart table, cart badge, category filter). Everything else is Blade. This avoids the overhead of a full React SPA, keeps initial page loads fast, and lets Blade handle SEO/SSR concerns.

### Communication between islands via CustomEvent
The `CartSummary` header badge and `ProductConfigurator`/`CartTable` islands need to stay in sync. Instead of a shared state library (Redux/Zustand), a simple `window.dispatchEvent(new CustomEvent('cart-updated'))` pattern keeps things minimal. CartSummary also polls `/api/cart/summary` every 30s as a fallback.

### Inline SVGs for product images
Used monochrome SVG illustrations embedded directly in Blade components rather than external image files. Each form factor (Rundstahl, Flachstahl, IPE, Rohr, Blech, etc.) has its own SVG shape. This avoids external dependencies and works offline.

### Tailwind v4 CSS-based config
Laravel 12 ships with `@tailwindcss/vite` (Tailwind v4), which uses CSS-based configuration via `@theme` directives instead of the v3 `tailwind.config.js` approach. Custom colors are defined as CSS custom properties.

### German number formatting
All prices use `number_format($val, 2, ',', '.')` (comma decimal, period thousands) with a non-breaking space before the `€` sign. The React islands use `toLocaleString('de-DE')` for the same formatting.

### Price calculations
- **Weight for cut-to-length:** `(length_mm / 1000) * weight_per_meter_kg * quantity`
- **Weight for fixed-length/sheets:** `weight_per_piece_kg * quantity`
- **Base price:** `weight_kg * price_per_kg_eur`
- **Per-cut services (Sägen, Entgraten):** `price_per_cut * quantity`
- **Per-kg services (Sandstrahlen, Verzinken, Grundieren):** `price_per_kg * weight_kg`
- **Certificate surcharge:** flat per-position from certificates table
- **Shipping:** Weight-based tiers (9,90 EUR Paketversand up to 30 kg, 89/189/289 EUR Spedition). Free for regional customers (PLZ 88xxx Bodensee-Oberschwaben). See `ShippingHelper::calculate()`.
- **VAT:** always 19%

### Order numbers
Format `B-YYYYNNNNN` (e.g., `B-202600001`). Auto-incremented per year via `Order::generateOrderNumber()`.

### Wholesaler multi-tenancy prepared but not active
The `wholesalers` table and `customers.wholesaler_id` FK exist for future multi-tenant support, but only one wholesaler is seeded. All branding is hardcoded in Blade templates for this prototype.

### No email sending
Order confirmations log email content via `Log::info()` instead of actually sending. The confirmation page shows "Eine Bestätigung wurde an {email} gesendet." for the user.

### Alpine.js used for small interactions
Breeze installs Alpine.js, which is used for the account dropdown toggle and product detail tabs. React islands handle the heavier interactive pieces.

## Product Data Notes
- 30 products seeded with realistic German steel catalog naming conventions
- Weight-per-meter values calculated from cross-sectional geometry and material density
- Prices approximate real market ranges: ~1.20 EUR/kg for construction steel, 4-6 EUR/kg for stainless, 6-12 EUR/kg for non-ferrous
- Restlängen (remnant lengths) flagged on 4 products
- 6 products marked as featured for homepage display
- 3 sample orders with 9 total line items for Franz Kowalski's order history

### Lieferbedingungen tab modeled after industry standards
The product detail "Lieferbedingungen" tab was redesigned based on research of 15+ German steel wholesalers (Klöckner, thyssenkrupp Schulte, Salzgitter, Stahl-Shop24, Stahlshop.de, Stahl24.eu, etc.). It includes all sections that are standard in the B2B steel industry:
- Weight-based shipping cost table (Paket/Langpaket/Spedition tiers)
- Differentiated delivery timeframes (Paket vs Spedition vs Anarbeitung)
- Speditionslieferung details (frei Bordsteinkante, Abladehinweise, LKW-Zufahrt)
- Selbstabholung with warehouse address and pickup policy
- Teillieferungen (partial deliveries permitted, no extra cost)
- Verpackung and Palettenpfand (Tausch vs Pfand system)
- Schnitt-/Längentoleranzen and Gewichtstoleranzen (±2mm saw, ±10% weight)
- Gefahrübergang (EXW Weingarten, Incoterms 2020)
- Mängelrüge deadlines (7 days visible, 3 months hidden, per §377 HGB)
- Liefergebiet (DE + neighboring EU countries)

## Known Limitations (Prototype Scope)
- No user registration (accounts created by seed only)
- No real payment processing
- No admin panel
- No email sending
- Inventory is display-only (no reservation or decrement on order)
- Search is simple LIKE query, no full-text indexing
- Responsive design tested at 1440px and iPhone SE widths in mind, not exhaustively
