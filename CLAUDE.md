# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel 12 + Filament 4 inventory management system ("Toko Berkah Jaya" / stokku.my.id) for tracking product stock, stock-in/stock-out transactions, and stock reports. Backend is PHP 8.2, admin UI is entirely a Filament panel (no separate frontend SPA framework beyond Filament's own Livewire/Tailwind stack).

Two in-depth reference docs already exist and should be consulted before making business-logic or config changes:
- `docs/DOKUMENTASI_SISTEM_INVENTORY.md` — functional spec: menus, roles/permissions matrix, workflows, business rules (in Indonesian).
- `docs/DOKUMENTASI_TEKNIS_CONFIG.md` — config reference: `.env` → `config/*.php` mapping, deploy checklist, troubleshooting (in Indonesian).

## Commands

```bash
composer dev              # Runs serve + queue:listen + pail (logs) + vite concurrently — the standard local dev command
php artisan serve          # App only, no asset watching
npm run dev                 # Vite dev server (Tailwind 4)
npm run build                # Production asset build

composer test               # config:clear then artisan test (runs PHPUnit)
php artisan test --filter=TestName   # Run a single test
php artisan test tests/Feature/SomeTest.php

vendor/bin/pint              # Laravel Pint code style fixer (no custom pint.json, uses defaults)

php artisan migrate
php artisan db:seed --class=RoleSeeder   # Seeds Super Admin / Admin / Kasir roles + permissions
php artisan shield:generate --all         # Regenerate Filament Shield policies/permissions after adding a new resource
```

Test suite currently only has the framework-default example tests (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`) — no real coverage of stock logic exists yet. Tests run against in-memory SQLite (see `phpunit.xml`), not the MySQL dev database.

Default local login: `admin@mail.com` / `admin1234` (see docs for the Admin/Kasir seeded accounts).

## Architecture

### Domain model & stock mutation flow

This is the core mechanic of the app and the thing most likely to break if touched carelessly:

- **Product** (`app/Models/Product.php`) has a denormalized `stock` integer column that is the running total. It is never edited directly by the UI for transactions — it's only mutated by observers.
- **StockIn** / **StockOut** are transaction headers (invoice, date, note); each `hasMany` **StockInItem** / **StockOutItem** line items (product, quantity, unit price, subtotal).
- **StockMutation** is an append-only, auto-generated audit ledger — one row per line item, created/updated/deleted only by observers, never directly editable through the UI (`StockMutationResource` forms are effectively read-only per the policy).
- All the stock arithmetic and mutation-ledger bookkeeping lives in `app/Observers/`:
  - `StockInItemObserver` — on create: increments `product.stock` and inserts a `StockMutation` (`type=in`, `reference_id = stockInItem->id`). On update: diffs old vs new quantity/product and adjusts both stock and the mutation row (throws if the change would drive stock negative). On delete: decrements stock back down (throws if that would go negative) and deletes the mutation row.
  - `StockOutItemObserver` — mirror image of the above for outbound stock (`type=out`, `reference_id = -stockOutItem->id`, note the negative sign to keep in/out reference IDs from colliding). Throws if outbound quantity would exceed available stock.
  - `StockObserver` — attached to `StockIn`/`StockOut` (the header, not the items). On `updated`, propagates header-level `transaction_date`/`note` changes down to the associated `StockMutation` rows. On `deleting`, cascades delete to child items (which in turn triggers the item observers above to reverse the stock and remove mutations).
  - Observers are registered in `AppServiceProvider::boot()` — there's no auto-discovery, so a new stock-affecting model needs to be wired up there explicitly.
  - Because reversal logic depends on `getOriginal()`, always modify these transactions through Eloquent (not raw DB queries) or the stock/ledger will drift out of sync.
- **StockOut** invoice numbers are auto-generated (not user-entered) in `StockOutForm` with format `INV/OUT/{Ymd}/{4-digit sequence}`, computed by finding the max existing invoice for that day's prefix. **StockIn** invoice numbers, by contrast, are manually entered (supplier's own invoice number).
- Business rule enforced in forms: selling_price must be >= purchase_price; a product can't appear twice as a line item within the same transaction (already-picked products are disabled in subsequent item dropdowns).

### Filament structure

- Panel is configured in `app/Providers/Filament/AdminPanelProvider.php`: single `admin` panel at `/admin`, SPA mode enabled, resources/pages/widgets are auto-discovered from `app/Filament/{Resources,Pages,Widgets}`.
- Resources follow Filament 4's split-class convention: each resource (`Products`, `StockIns`, `StockOuts`, `StockMutations`, `Categories`, `Suppliers`, `Users`) has its `Schemas/` (form/infolist definitions) and `Tables/` (table column/filter definitions) separated from the top-level `*Resource.php`, with CRUD pages under `Pages/`.
- Authorization is via **Filament Shield** (`bezhansalleh/filament-shield`) + **spatie/laravel-permission**, policy classes in `app/Policies/`. Permissions follow `{method}:{Model}` naming (pascal case, e.g. `create:StockIn`) and are auto-generated per-model with 12 standard methods (viewAny, view, create, update, delete, deleteAny, restore, forceDelete, forceDeleteAny, restoreAny, replicate, reorder). Adding a new Filament resource requires running `php artisan shield:generate --all` (or reseeding roles) so the corresponding permissions/policy exist, otherwise every action 403s.
- Three seeded roles: `super_admin` and `admin` (full access, including user & role management) and `kasir` (view everything, can only *create* StockIn/StockOut transactions — cannot edit/delete master data or transactions). See the permission matrix in `docs/DOKUMENTASI_SISTEM_INVENTORY.md` §5 before changing policies.
- Dashboard widgets (`app/Filament/Widgets/`) aggregate stats/charts (stock movement over 30 days, stock-by-category, top outbound products, low-stock table, recent mutations) — most pull from `StockMutation`/`Product` directly with query aggregation, see `app/Filament/Support/InventoryDashboard.php` for shared query logic.
- Non-Filament routes in `routes/web.php` handle print/export views outside the panel: `/admin/stock-reports/print` (stock report print view) and `/admin/stock-outs/{stockOut}/print` (single transaction print view), both gated by `web`+`auth` middleware (not Filament's own auth guard flow). Excel export is via `app/Exports/StockReportExport.php` (Laravel Excel / maatwebsite).

### Config

- `.env` is the only place to change environment values; `config/*.php` should not be edited for environment-specific overrides. Notably `timezone` (`Asia/Jakarta`) is hardcoded in `config/app.php`, not env-driven.
- DB/cache/session/queue all default to MySQL/`database` driver in this project (not Redis) — see `docs/DOKUMENTASI_TEKNIS_CONFIG.md` for the full `.env`→config key mapping and deploy checklist if provisioning a new environment.
