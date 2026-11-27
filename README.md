# SaigonBite – Laravel + MySQL + JavaScript

Starter implementation for the six selected use cases:
- UC3 Place an Order
- UC8 Prepare an Order and Mark It Ready
- UC10 Deliver an Order
- UC13 Manage Hubs and Kitchen Capacity
- UC14 Manage Brands and Menu Items
- UC16 Produce Management Reports

## Requirements
- PHP 8.2+
- Composer 2
- MySQL 8+
- PHP extensions required by Laravel (PDO, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo)

## Setup
1. Extract this project.
2. Run `composer install`.
3. Copy `.env.example` to `.env`.
4. Create a MySQL database named `saigonbite`.
5. Configure DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD in `.env`.
6. Run `php artisan key:generate`.
7. Run `php artisan migrate --seed`. This loads the public TP.HCM branch/menu snapshot documented in [database/REAL_DATA_SOURCES.md](database/REAL_DATA_SOURCES.md). Re-running the seeder updates the same records instead of adding duplicates.
8. Run `php artisan serve`.
9. Open http://127.0.0.1:8000 and sign in.

## Login accounts
The default local accounts are `admin / 123456` and `customer / 123456`. The admin role manages UC14 brands and menu items; the customer role uses UC3 ordering. Passwords are stored as hashes. To create or reset these accounts without re-seeding restaurant data, run `php artisan db:seed --class=DefaultAccountsSeeder`.

## API endpoints
- GET/POST `/api/hubs`; GET/PUT `/api/hubs/{hub}`; PATCH `/api/hubs/{hub}/status`; PUT `/api/hubs/{hub}/brands`
- GET/POST `/api/brands`; GET/PUT/DELETE `/api/brands/{brand}`
- GET `/api/brands/{brand}/items`; GET `/api/menu-items`; POST `/api/menu-items`; PUT/DELETE `/api/menu-items/{menuItem}`
- GET/POST `/api/customers/me/addresses`
- POST `/api/orders/preview`; POST `/api/orders`; GET `/api/orders/{order}`
- GET `/api/kitchen/orders`; PATCH `/api/kitchen/orders/{order}/status`
- GET `/api/deliveries`; PATCH `/api/deliveries/{delivery}/status`
- GET `/api/reports/summary?from=YYYY-MM-DD&to=YYYY-MM-DD` (date filters are optional)

Orders accept COD only and require a UUID `idempotency_key`, a Hub, Brand, delivery details or an owned `address_id`, and at least one distinct menu item. The server reads prices from the database and returns authoritative totals from `POST /api/orders/preview`; posted prices and totals are ignored. A repeated key for the same customer returns the original order. Payment starts as `unpaid`; it changes to `paid` only when a COD delivery is marked delivered with `cash_collected` equal to the order total. Hub brand assignments can be created with `brand_ids` when creating a Hub or replaced through `PUT /api/hubs/{hub}/brands`.

## Architecture
- Models: Eloquent entities and relationships.
- Repositories: data access queries.
- Services: business rules and transaction handling.
- Controllers: HTTP request validation and responses.
- Blade + JavaScript: simple dashboard frontend.

## Important scope notes
- UC3 stores the selected payment method; this starter does not implement a payment gateway.
- Menu item catalog activation (`menu_items.is_active`, UC14) is separate from Hub availability (`hub_menu_items.is_available`, UC9). Existing global availability was copied to Hub assignments by the additive migration. New UC14 items are inactive at each Hub until the UC9 availability flow sets the pivot.
- The approved proposal leaves distance limits and distance-based delivery fee rules unresolved (Assumption A-02 pending). The current local fee of 15,000 VND and surcharge of 0 VND remain configurable through `SAIGONBITE_DELIVERY_FEE` and `SAIGONBITE_SURCHARGE`; they are not a confirmed production pricing policy. No geocoder or maximum distance rule is configured.
- Voucher management is external to UC3/UC14 (Assumption A-13). Voucher codes are never silently accepted: requests containing a voucher return a validation error until an external read-only voucher provider is integrated. The app has no voucher CRUD/table.
- The restaurant names, branches, and menu prices are a public-data snapshot and can change. The app stores orders locally; it does not send them to restaurant POS systems or synchronize live stock, branch availability, or capacity. Confirm current prices and obtain restaurant integration approval before accepting real customer orders.
- Hub capacity is an internal SaigonBite queue setting, initially 20 per branch, not a capacity figure published by the restaurant.
- Login and role checks currently cover only the admin (UC14) and customer (UC3) flows; other staff roles are outside this login scope.
- Default credentials are for local development and should be changed before deployment.
- Confirm all status transitions and report definitions against the final approved use case descriptions.
- This is a learning-project scaffold, not production-ready software.
