# Staff & Workflow API

## Staff

### List Staff
**GET** `/api/staff`

**Query Parameters:**
- `type` (optional): employee, agent, driver, picker, manager

**Response:**
Returns list of staff members with linked user accounts.

### Create Staff
**POST** `/api/staff`

**Parameters:**
- `user_id` (optional, if linking to system user)
- `staff_type` (required): employee, agent, driver, picker, manager
- `salary_monthly` (numeric)

---

## Customer Addresses (Location)

### List Addresses
**GET** `/api/customers/{customer_id}/addresses`

### Add Address
**POST** `/api/customers/{customer_id}/addresses`

**Parameters:**
- `title` (Home, Shop...)
- `address_text` (High St, Building 5...)
- `lat` (Latitude)
- `lng` (Longitude)
- `is_default` (boolean)

---

## Architecture Updates (Parties & Workflow)
- **Parties**: A new unified concept `parties` is introduced to handle interactions with any entity (Driver, Walk-in, Customer, Agent).
- **Workflow Logs**: The system is prepared to log Sales Invoice status changes (`pending` -> `preparing` -> `delivered`) in `sales_order_status_logs`.
