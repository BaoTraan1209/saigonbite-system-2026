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
7. Run `php artisan migrate --seed`.
8. Run `php artisan serve`.
9. Open http://127.0.0.1:8000

## API endpoints
- GET/POST `/api/hubs`; GET/PUT `/api/hubs/{hub}`; PATCH `/api/hubs/{hub}/status`
- GET/POST `/api/brands`; GET/PUT/DELETE `/api/brands/{brand}`
- GET `/api/brands/{brand}/items`; POST `/api/menu-items`; PUT/DELETE `/api/menu-items/{menuItem}`
- GET/POST `/api/orders`; GET `/api/orders/{order}`
- GET `/api/kitchen/orders`; PATCH `/api/kitchen/orders/{order}/status`
- GET `/api/deliveries`; PATCH `/api/deliveries/{delivery}/status`
- GET `/api/reports/summary`

## Architecture
- Models: Eloquent entities and relationships.
- Repositories: data access queries.
- Services: business rules and transaction handling.
- Controllers: HTTP request validation and responses.
- Blade + JavaScript: simple dashboard frontend.

## Important scope notes
- UC3 stores the selected payment method; this starter does not implement a payment gateway.
- The starter uses simplified role labels and does not yet include production authentication/authorization.
- Add authorization policies/middleware before exposing administrative endpoints.
- Confirm all status transitions and report definitions against the final approved use case descriptions.
- This is a learning-project scaffold, not production-ready software.
