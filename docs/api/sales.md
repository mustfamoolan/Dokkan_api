# Sales API

## Sales Invoices (Workflow)

### Create Invoice (Draft)
**POST** `/api/sales-invoices`
- `party_id`, `lines`, `payment_type`...

### Workflow Actions
1. **Submit**: `/api/sales-invoices/{id}/submit` (Draft -> Pending)
2. **Approve**: `/api/sales-invoices/{id}/approve` (Pending -> Approved)
3. **Start Preparing**: `/api/sales-invoices/{id}/start-preparing`
4. **Mark Prepared**: `/api/sales-invoices/{id}/mark-prepared`
   - **Effect**: Deducts Inventory, Records Cost Snapshot.
5. **Assign Driver**: `/api/sales-invoices/{id}/assign-driver`
   - Body: `{ "driver_staff_id": 5 }`
6. **Out For Delivery**: `/api/sales-invoices/{id}/out-for-delivery`
7. **Mark Delivered**: `/api/sales-invoices/{id}/mark-delivered`
   - **Effect**: Creates Journal Entry (Revenue/Cash/AR + COGS).

## Sales Returns

### Create Return (Draft)
**POST** `/api/sales-returns`
- `sales_invoice_id`, `lines`...

### Post Return
**POST** `/api/sales-returns/{id}/post`
- **Effect**: Returns Inventory, Reverses Revenue.
