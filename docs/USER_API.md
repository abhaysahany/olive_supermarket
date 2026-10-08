# 👤 Customer & User API Documentation

**Base URL:** `https://webtesting.bond` (or local `http://127.0.0.1:8000`)  
**Prefix:** `/api/v1/user`

All user endpoints require an authenticated user Sanctum token:
```http
Authorization: Bearer <your_access_token>
```

---

## Endpoints

### 1. Customer Profile

Returns the logged-in customer's profile, recent order stats, and contact details.

- **Endpoint:** `GET /api/v1/user/profile`
- **Access:** Authenticated User
- **Response (`200 OK`):**
```json
{
    "user": {
        "id": 1,
        "name": "Michael O.",
        "email": "michael@example.com",
        "phone": "+15125550142",
        "role": "user",
        "created_at": "2026-10-08T06:30:00.000000Z"
    },
    "recent_orders_count": 5
}
```

---

### 2. Place Order / Checkout

Submits an order from the user's cart or from a direct items array. Automatically calculates totals, applies delivery fees/discounts, reserves product stock, creates an order record with tracking number (`TRK-US-...`), and clears the active cart.

- **Endpoint:** `POST /api/v1/user/orders`
- **Access:** Authenticated User

#### Request Body
```json
{
    "order_type": "delivery",
    "delivery_time_slot": "asap",
    "delivery_time_slot_label": "ASAP (In ~45 min)",
    "payment_method": "credit_card",
    "shipping_name": "Michael O.",
    "shipping_phone": "+15125550142",
    "shipping_address_line1": "123 Market St",
    "shipping_address_line2": "Suite 500",
    "shipping_city": "San Francisco",
    "shipping_state": "CA",
    "shipping_zip_code": "94103",
    "delivery_instructions": "Leave package at reception desk",
    "tip_amount": 4.00,
    "coupon_code": "FRESH10",
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ]
}
```
*(Note: If the `items` array is omitted, the user's active backend Cart items will automatically be used and cleared).*

#### Supported Field Values
- **`order_type`**: `delivery`, `pickup`
- **`payment_method`**: `credit_card`, `cash_on_delivery`, `apple_pay`, `google_pay`, `ebt_snap`

#### Response (`201 Created`)
```json
{
    "message": "Order placed successfully",
    "data": {
        "id": 12,
        "order_number": "US-ORD-20261008-XK92P1",
        "user_id": 1,
        "subtotal": "28.50",
        "tax_amount": "1.85",
        "delivery_fee": "0.00",
        "tip_amount": "4.00",
        "discount_amount": "2.85",
        "total_price": "31.50",
        "order_type": "delivery",
        "order_status": "pending",
        "payment_status": "paid",
        "payment_method": "credit_card",
        "delivery_time_slot_label": "ASAP (In ~45 min)",
        "created_at": "2026-10-08T07:15:00.000000Z",
        "delivery": {
            "id": 8,
            "tracking_number": "TRK-US-88219034",
            "delivery_status": "pending",
            "estimated_delivery_time": "2026-10-08T08:00:00.000000Z"
        },
        "items": [
            {
                "id": 24,
                "product_id": 1,
                "product_name": "Organic Honeycrisp Apples",
                "unit_price": "3.99",
                "quantity": 2,
                "total_price": "7.98"
            }
        ]
    }
}
```

---

### 3. Customer Order History

Retrieves paginated past and active orders placed by the authenticated customer.

- **Endpoint:** `GET /api/v1/user/orders`
- **Access:** Authenticated User
- **Query Parameters:**
    - `page`: Page number (Default: `1`)
    - `per_page`: Number of orders per page (Default: `10`)
- **Response (`200 OK`):**
```json
{
    "data": [
        {
            "id": 12,
            "order_number": "US-ORD-20261008-XK92P1",
            "total_price": "31.50",
            "order_status": "pending",
            "payment_status": "paid",
            "created_at": "2026-10-08T07:15:00.000000Z",
            "delivery": {
                "tracking_number": "TRK-US-88219034",
                "delivery_status": "pending"
            },
            "items_count": 2
        }
    ],
    "current_page": 1,
    "last_page": 2,
    "total": 15
}
```

---

### 4. Single Order Details

Fetches full breakdown of items, pricing, delivery window, and courier assignment for a specific customer order.

- **Endpoint:** `GET /api/v1/user/orders/{order_id}`
- **Access:** Authenticated User (Must own the order)
- **Response (`200 OK`):**
```json
{
    "data": {
        "id": 12,
        "order_number": "US-ORD-20261008-XK92P1",
        "subtotal": "28.50",
        "tax_amount": "1.85",
        "delivery_fee": "0.00",
        "tip_amount": "4.00",
        "discount_amount": "2.85",
        "total_price": "31.50",
        "order_type": "delivery",
        "order_status": "processing",
        "payment_status": "paid",
        "shipping_address": {
            "name": "Michael O.",
            "phone": "+15125550142",
            "line1": "123 Market St",
            "line2": "Suite 500",
            "city": "San Francisco",
            "state": "CA",
            "zip_code": "94103"
        },
        "delivery": {
            "tracking_number": "TRK-US-88219034",
            "delivery_status": "in_transit",
            "driver_name": "Marcus Vance",
            "driver_phone": "+14155550199",
            "current_location": {
                "latitude": 37.774929,
                "longitude": -122.419416
            }
        },
        "items": [
            {
                "id": 24,
                "product_id": 1,
                "name": "Organic Honeycrisp Apples",
                "brand": "Oliva Farms",
                "unit_size": "2 lbs bag",
                "price": "3.99",
                "quantity": 2,
                "line_total": "7.98"
            }
        ]
    }
}
```

---

### 5. Track Live Order Delivery

Fetches live courier GPS coordinates, estimated arrival time, and step progression for an order.

- **Endpoint:** `GET /api/v1/user/orders/{order_id}/track-delivery`
- **Access:** Authenticated User (Must own the order)
- **Response (`200 OK`):**
```json
{
    "order_id": 12,
    "order_number": "US-ORD-20261008-XK92P1",
    "delivery": {
        "tracking_number": "TRK-US-88219034",
        "delivery_status": "out_for_delivery",
        "driver_name": "Marcus Vance",
        "driver_phone": "+14155550199",
        "vehicle_info": "Silver Toyota Prius (CA 7XYZ89)",
        "current_location": {
            "latitude": 37.774929,
            "longitude": -122.419416
        },
        "estimated_delivery_time": "2026-10-08T08:00:00.000000Z",
        "proof_of_delivery_image": null
    }
}
```
