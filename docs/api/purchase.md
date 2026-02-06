# Purchase API

All routes require authentication.

## Purchase Invoices

### Create Invoice (Draft)
**POST** `/api/purchase-invoices`

**Parameters:**
- `supplier_id` (required)
- `invoice_date` (YYYY-MM-DD)
- `currency` (IQD, USD)
- `exchange_rate` (1 for IQD, or Rate for USD)
- `discount_foreign` (optional)
- `notes` (optional)
- `lines` (array):
    - `product_id`
    - `unit_id`
    - `qty`
    - `price_foreign` (Price per unit in Invoice Currency)
    - `is_free` (boolean)

**Response:** Returns created invoice with status `draft`.

### Approve Invoice
**POST** `/api/purchase-invoices/{id}/approve`

Change status to `approved`.

### Post Invoice (Finalize)
**POST** `/api/purchase-invoices/{id}/post`

**Effect:**
- Changes status to `posted`.
- **Inventory:** Adds stock to warehouse.
- **Accounting:** Creates Journal Entry (Dr Inventory, Cr Supplier).

---

## Purchase Returns

### Create Return (Draft)
**POST** `/api/purchase-returns`

**Parameters:**
- `supplier_id`
- `purchase_invoice_id` (optional reference)
- `return_date`
- `currency`, `exchange_rate`
- `lines` (array): same as invoice but no is_free logical checks usually.

### Post Return
**POST** `/api/purchase-returns/{id}/post`

**Effect:**
- Changes status to `posted`.
- **Inventory:** Removes stock from warehouse.
- **Accounting:** Creates Journal Entry (Dr Supplier, Cr Inventory).
