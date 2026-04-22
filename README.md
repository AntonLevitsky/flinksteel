# flinksteel — Regionales Kundenportal für Stahlhandel

B2B e-commerce customer portal prototype for **Müller Stahl & Metall GmbH**, a regional German steel wholesaler in Weingarten, Baden-Württemberg.

## Tech Stack

- **Laravel 12** (PHP 8.5) with SQLite
- **Blade** templates with **Tailwind CSS** v4
- **React 18 + TypeScript** islands for interactive components (product configurator, cart, filters)
- **Laravel Breeze** (Blade stack) for authentication
- **Vite** for asset bundling

## Quick Start

```bash
composer install
npm install
npm run build
php artisan migrate --seed
php artisan serve
```

Then visit `http://127.0.0.1:8000` and log in.

## Test Logins

| Name             | Email                                    | Password   | Company                      |
|------------------|------------------------------------------|------------|------------------------------|
| Franz Kowalski   | f.kowalski@schlosserei-bergmann.de        | `password` | Schlosserei Bergmann GmbH    |
| Maria Bruckner   | m.bruckner@metallbau-bruckner.de          | `password` | Metallbau Bruckner KG        |
| Hans Dietrich    | h.dietrich@konstruktion-dietrich.de       | `password` | Konstruktion Dietrich GmbH   |
| Andreas Riedl    | a.riedl@anlagenbau-riedl.de               | `password` | Anlagenbau Riedl GmbH        |
| Klaus Schlosser  | k.schlosser@schlosser-partner.de          | `password` | Schlosser & Partner AG       |

**Default test user:** Franz Kowalski (has 3 historical orders seeded).

## Architecture

### Blade Pages (server-rendered)
- Homepage, category listings, product detail pages, checkout, order history
- Global layout with header (logo, search, cart, account dropdown) and footer

### React Islands (`resources/js/islands/`)
Each island is a standalone Vite entry point mounted into a specific `<div id="react-...">` in Blade:

| Island                 | Mount Point                      | Purpose                                      |
|------------------------|----------------------------------|----------------------------------------------|
| `ProductConfigurator`  | Product detail page              | Quantity, length, Anarbeitung, certificate, live pricing |
| `CartTable`            | Cart page                        | Line item editing, removal, totals            |
| `CartSummary`          | Header (all pages)               | Cart item count badge                         |
| `CategoryFilter`       | Category listing sidebar         | Material, form, stock filters                 |

Props are passed via `data-props` JSON attributes. Islands communicate via `CustomEvent('cart-updated')`.

### Cart API (`/api/cart/*`)
Session-authenticated JSON endpoints for cart operations. Cart persists in the database, tied to the user.

## Catalog

30 products across 6 top-level categories (Stabstahl, Profilstahl, Bleche, Rohre, Edelstahl, NE-Metalle) with realistic German steel naming, dimensions, weights, and pricing.
