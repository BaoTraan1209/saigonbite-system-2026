# UC3 and UC14 API test plan

Base URL: `http://127.0.0.1:8000`. These API routes use the Laravel web session. In Postman, first request `GET /login`, keep the cookies, then `POST /login` with `login` and `password`; keep the authenticated session cookie for subsequent requests. Laravel validation failures return HTTP 422 with `message` and `errors`; unauthenticated requests return 401 and role violations return 403.

Use the seeded local accounts `customer / 123456` and `admin / 123456`. Replace `{{hubId}}`, `{{brandId}}`, `{{itemId}}`, `{{addressId}}`, and `{{orderId}}` with IDs from responses. Run customer and admin cases under the corresponding session.

## Shared request bodies

Order preview:

```json
{
  "hub_id": "{{hubId}}",
  "brand_id": "{{brandId}}",
  "customer_name": "Test Customer",
  "customer_phone": "0900000000",
  "delivery_address": "1 Nguyen Hue, District 1, Ho Chi Minh City",
  "payment_method": "COD",
  "items": [{ "menu_item_id": "{{itemId}}", "quantity": 2 }]
}
```

Order confirmation uses the same body plus a new UUID field:

```json
{ "idempotency_key": "620c9a0e-a45f-4a03-9292-b2c9fc008f83" }
```

Brand create:

```json
{ "name": "Postman Brand", "description": "UC14 check", "status": "active" }
```

Menu item create (Admin chooses whether it can be ordered):

```json
{ "brand_id": "{{brandId}}", "name": "Postman Item", "description": "UC14 check", "price": 42000, "is_active": true, "is_available": true }
```

## UC3 – Place an Order

| # | Method and endpoint | Scenario / request change | Expected response | Expected database result |
|---|---|---|---|---|
| 1 | GET `/api/hubs?available=1` | Customer requests available hubs | 200; each result is active and below capacity | Read only |
| 2 | GET `/api/brands?hub_id={{hubId}}` | List brands at selected Hub | 200; active brands with at least one active/available item | Read only |
| 3 | GET `/api/menu-items?hub_id={{hubId}}&brand_id={{brandId}}` | Browse a brand's Hub menu | 200; only active items available at that Hub | Read only |
| 4 | GET `/api/customers/me/addresses` | Read saved addresses | 200; only current customer's rows | Read only |
| 5 | POST `/api/customers/me/addresses` | Valid recipient, phone, street, district and city | 201 | One customer_addresses row; first address is default |
| 6 | POST `/api/orders/preview` | Valid order preview body | 200 with server subtotal, delivery_fee, surcharge, discount_amount and total_amount | No order or order_item rows |
| 7 | POST `/api/orders` | Valid preview body plus new `idempotency_key` | 201; status `submitted`, payment `unpaid`, method `COD` | One order and one row per submitted item; item name and price snapshots saved |
| 8 | POST `/api/orders` | Select a Brand not assigned to `hub_id` | 422 `brand_id` | No order rows |
| 9 | POST `/api/orders` | Item belongs to another Brand or is unavailable at selected Hub | 422 `items` | No order rows |
| 10 | POST `/api/orders` | Set item quantity to 0, negative, or >50 | 422 `items.*.quantity` | No order rows |
| 11 | POST `/api/orders` | Add forged `unit_price`, `subtotal` and `total_amount` | 201; returned totals use database prices and server fee config | Order line price equals current menu_items.price |
| 12 | POST `/api/orders/preview` | Add `voucher_code` | 422 `voucher_code` while external provider is unconfigured | No rows; blocker documented below |
| 13 | POST `/api/orders/preview` | Invalid/expired/minimum-spend voucher | Cannot be tested until external read-only Voucher provider is supplied | No voucher table or CRUD is created in this scope |
| 14 | POST `/api/orders/preview` | Send voucher_code as an array / attempt multiple vouchers | 422 `voucher_code` | No rows; at most one scalar code accepted |
| 15 | POST `/api/orders` | Omit street/address or send empty delivery address | 422 address fields | No order rows |
| 16 | POST `/api/orders` | Address beyond maximum delivery distance | Not executable: proposal A-02 does not provide distance threshold or geocoding provider | No distance is fabricated or inferred from user input |
| 17 | POST `/api/orders` | Set Hub's in-progress order count to capacity, then preview/create | 422 `hub_id` | No additional order; locking serializes capacity checks |
| 18 | POST `/api/orders` | Call without authenticated session | 401 | No rows |
| 19 | POST `/api/orders` | Make item unavailable after preview, then confirm | 422 `items` | No order rows |
| 20 | POST `/api/orders` | Repeat confirmation with the same customer and idempotency UUID | 201 with the original order ID | Exactly one order and its original order_items |
| 21 | GET `/api/orders/{{orderId}}` | Customer reads own order | 200 with `submitted` and `unpaid` | Read only; payment is not marked paid at order creation |
| 22 | GET `/api/orders/{{otherCustomerOrderId}}` | Customer reads another customer's order | 404 | Read only |
| 23 | PATCH `/api/deliveries/{{deliveryId}}/status` | Mark COD delivery as delivered with exact `cash_collected` | 200; order is completed/paid | Delivery cash recorded; order_status and payment_status updated independently |

## UC14 – Manage Brands and Menu Items

| # | Method and endpoint | Scenario / request change | Expected response | Expected database result |
|---|---|---|---|---|
| 1 | POST `/api/brands` | Valid brand body | 201 | New brands row |
| 2 | GET `/api/brands` | Admin lists brands | 200 | Read only |
| 3 | GET `/api/brands/{{brandId}}` | Admin views brand detail | 200 with related menu_items | Read only |
| 4 | PUT `/api/brands/{{brandId}}` | Change name/description/status | 200 | Same brand row updated |
| 5 | DELETE `/api/brands/{{brandId}}` | Deactivate a brand used by orders | 200; status `inactive` | Brand row remains; order history is retained |
| 6 | POST `/api/menu-items` | Valid menu item body with `is_available: true` or `false` | 201 | New menu_items row; selected Availability is copied to all existing Hubs assigned to the Brand |
| 7 | GET `/api/menu-items` | Admin lists menu items | 200 with brand and `is_active` | Read only |
| 8 | GET `/api/brands/{{brandId}}/items` | Admin lists items for a brand | 200 | Read only |
| 9 | PUT `/api/menu-items/{{itemId}}` | Change name, description, price or `is_active` | 200 | Same item row updated; order item snapshots unchanged |
| 10 | DELETE `/api/menu-items/{{itemId}}` | Deactivate even when referenced by an order | 200; `is_active=false` | Item row remains and historical order_items remain |
| 11 | POST `/api/brands` | Reuse an existing brand name | 422 `name` | No duplicate row |
| 12 | POST `/api/menu-items` | Reuse item name within same Brand | 422 `name` | No duplicate row |
| 13 | POST `/api/menu-items` | Use missing `brand_id` | 422 `brand_id` | No item row |
| 14 | POST `/api/brands` or `/api/menu-items` | Call as Customer | 403 | No rows |
| 15 | PUT `/api/menu-items/{{itemId}}` | Set `is_available` to `true` or `false` | 200; returned status reflects Admin choice | `menu_items.is_available` and all existing `hub_menu_items` rows for this item are updated |
| 16 | PUT `/api/menu-items/{{itemId}}` | Change menu item price after an order exists | 200 | Menu current price changes; order_items.unit_price/line_total retain prior snapshot |

## Known dependencies / limits

- Voucher validation is read-only and external per Assumption A-13, but the repository has no provider URL, interface contract, credentials, or test fixture. Voucher acceptance/expiry tests need that integration.
- Proposal Assumption A-02 leaves maximum delivery distance and delivery-fee rules pending. `SAIGONBITE_DELIVERY_FEE` and `SAIGONBITE_SURCHARGE` preserve the current local behavior as configurable values; they are not a confirmed distance policy. No coordinates or user-supplied distance are trusted.
- Admin can set item availability in UC14. The chosen status is copied to every Hub currently assigned to the item's Brand; customer catalog and order validation use that Hub-specific row. New Hub assignments inherit the item's Admin-selected status.
- Payment is COD-only in the current implementation, although Proposal FR5 also mentions online payment. No gateway or card fields are added.
