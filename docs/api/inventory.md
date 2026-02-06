# Inventory & Products API

All routes require authentication.

## Products

### List Products
**GET** `/api/products`

**Response (200 OK):** List of products with categories and units.

### Create Product
**POST** `/api/products`

**Parameters:**
- `name`, `category_id`, `base_unit_id` (required)
- `sku` (optional, unique)
- `purchase_price`, `sale_price_retail`, `sale_price_wholesale`
- `has_pack` (boolean) -> if true, `pack_unit_id` & `units_per_pack` required.

### Sync Suppliers
**POST** `/api/products/{id}/suppliers`

**Parameters:**
- `suppliers` (array): `[{ "supplier_id": 1, "last_price": 500, "currency": "IQD" }]`

---

## Inventory

### Get Balances
**GET** `/api/inventory/balances`

**Query Parameters:**
- `warehouse_id` (optional)
- `product_id` (optional)

**Response:** List of balances (Qty on Hand, Avg Cost).

### Get Transactions
**GET** `/api/inventory/transactions`

**Query Parameters:**
- `product_id` (optional)
- `date_from`, `date_to` (optional, YYYY-MM-DD)

### Opening Balance
**POST** `/api/inventory/opening-balance`

Used to initialize stock or make adjustments.

**Parameters:**
- `warehouse_id` (required)
- `items` (array):
    - `product_id`
    - `unit_id`
    - `qty`
    - `cost_iqd` (Cost per THIS unit)
    - `unit_factor` (Conversion to base unit)

**Effect:**
- Creates `InventoryTransaction` (type: `opening_balance`).
- Updates `InventoryBalance`:
    - Adds Qty.
    - Recalculates Weighted Average Cost.
