# 🛡️ Admin Portal API Documentation

**Base URL:** `https://webtesting.bond` (or local `http://127.0.0.1:8000`)  
**Prefix:** `/api/v1/admin`

All admin endpoints require an authenticated user with role `admin`:
```http
Authorization: Bearer <admin_token>
```
*(If a non-admin attempts access, `403 Forbidden` is returned).*

---

## 📊 1. Dashboard & User Management

### Admin Dashboard Stats
Provides high-level KPI metrics (revenue, total orders, low-stock items, active deliveries).

- **Endpoint:** `GET /api/v1/admin/dashboard`
- **Access:** Admin
- **Response (`200 OK`):**
```json
{
    "status": "success",
    "metrics": {
        "total_revenue": "18450.75",
        "total_orders": 420,
        "pending_orders": 14,
        "active_deliveries": 6,
        "total_products": 240,
        "low_stock_products": 8,
        "total_users": 185
    }
}
```

---

### List Users
Returns registered users with roles and contact numbers.

- **Endpoint:** `GET /api/v1/admin/users`
- **Access:** Admin
- **Query Parameters:** `?role=user` or `?role=admin`
- **Response (`200 OK`):**
```json
{
    "status": "success",
    "users": [
        {
            "id": 1,
            "name": "Michael O.",
            "email": "michael@example.com",
            "phone": "+15125550142",
            "role": "user",
            "created_at": "2026-10-08T06:30:00.000000Z"
        }
    ],
    "count": 185
}
```

---

## 🏷️ 2. Category Management

### Create Category
- **Endpoint:** `POST /api/v1/admin/categories`
- **Request Body:**
```json
{
    "name": "Bakery",
    "description": "Fresh artisan breads & baked pastries",
    "image": "https://images.unsplash.com/photo-1509440159596-0249088772ff"
}
```
- **Response (`201 Created`)**

---

### Update Category
- **Endpoint:** `PUT /api/v1/admin/categories/{id}`
- **Request Body:**
```json
{
    "name": "Artisan Bakery & Pastries",
    "description": "Updated description",
    "image": "https://images.unsplash.com/..."
}
```
- **Response (`200 OK`)**

---

### Delete Category
- **Endpoint:** `DELETE /api/v1/admin/categories/{id}`
- **Response (`200 OK`)**

---

## 📂 3. Subcategory Management

### Create Subcategory
- **Endpoint:** `POST /api/v1/admin/subcategories`
- **Request Body:**
```json
{
    "category_id": 1,
    "name": "Artisan Sourdough & Loaves",
    "slug": "artisan-sourdough-loaves",
    "description": "Freshly baked sourdough and baguettes",
    "image": "https://..."
}
```
- **Response (`201 Created`)**

---

### Update Subcategory
- **Endpoint:** `PUT /api/v1/admin/subcategories/{id}`
- **Request Body:**
```json
{
    "name": "Artisan & Organic Breads",
    "description": "Updated sourdough varieties"
}
```
- **Response (`200 OK`)**

---

### Delete Subcategory
- **Endpoint:** `DELETE /api/v1/admin/subcategories/{id}`
- **Response (`200 OK`)**

---

## 📦 4. Product Catalog Management

### Create Product
- **Endpoint:** `POST /api/v1/admin/products`
- **Request Body:**
```json
{
    "subcategory_id": 1,
    "sku": "US-PROD-201",
    "upc_barcode": "012345678912",
    "name": "Organic Whole Milk",
    "brand": "Horizon Organic",
    "unit_size": "1 gallon",
    "price": 4.99,
    "old_price": 5.49,
    "sale_price": 4.49,
    "cost_price": 3.10,
    "stock": 40,
    "low_stock_threshold": 10,
    "is_organic": true,
    "is_gluten_free": true,
    "is_perishable": true,
    "status": "active",
    "image": "https://...",
    "gallery_images": [
        "https://...",
        "https://..."
    ],
    "nutrition_facts": {
        "calories": 150,
        "fat": "8g",
        "protein": "8g"
    }
}
```
- **Response (`201 Created`)**

---

### Update Product
- **Endpoint:** `PUT /api/v1/admin/products/{id}`
- **Request Body:** *(Any product fields to update)*
```json
{
    "price": 4.79,
    "sale_price": 3.99,
    "stock": 60
}
```
- **Response (`200 OK`)**

---

### Delete Product
- **Endpoint:** `DELETE /api/v1/admin/products/{id}`
- **Response (`200 OK`)**

---

## 📋 5. Admin Order Processing

### List All Store Orders
- **Endpoint:** `GET /api/v1/admin/orders`
- **Query Filters:**
    - `?order_status=pending` (`pending`, `confirmed`, `processing`, `ready_for_pickup`, `out_for_delivery`, `delivered`, `cancelled`)
    - `?payment_status=paid` (`pending`, `paid`, `failed`, `refunded`)
    - `?order_type=delivery` (`delivery`, `pickup`)
    - `?search=US-ORD` (Search order number or customer name)
    - `?page=1`
- **Response (`200 OK`):**
```json
{
    "data": [
        {
            "id": 12,
            "order_number": "US-ORD-20261008-XK92P1",
            "user": {
                "id": 1,
                "name": "Michael O.",
                "email": "michael@example.com",
                "phone": "+15125550142"
            },
            "total_price": "31.50",
            "order_status": "pending",
            "payment_status": "paid",
            "order_type": "delivery",
            "delivery": {
                "id": 8,
                "tracking_number": "TRK-US-88219034",
                "delivery_status": "pending"
            }
        }
    ]
}
```

---

### Update Order Status
- **Endpoint:** `PATCH /api/v1/admin/orders/{id}/status`
- **Request Body:**
```json
{
    "order_status": "processing",
    "payment_status": "paid"
}
```
- **Status Enum Values:**
    - `order_status`: `pending`, `confirmed`, `processing`, `ready_for_pickup`, `out_for_delivery`, `delivered`, `cancelled`
    - `payment_status`: `pending`, `paid`, `failed`, `refunded`
- **Response (`200 OK`):**
```json
{
    "message": "Order status updated successfully",
    "order": {
        "id": 12,
        "order_status": "processing",
        "payment_status": "paid"
    }
}
```

---

## 🚚 6. Admin Dispatch & Delivery Operations

### List Deliveries
- **Endpoint:** `GET /api/v1/admin/deliveries`
- **Query Filters:**
    - `?delivery_status=assigned` (`pending`, `assigned`, `picked_up`, `in_transit`, `out_for_delivery`, `delivered`, `failed`, `returned`)
    - `?search=TRK-US`
- **Response (`200 OK`)**

---

### Assign Driver & Dispatch Order
Assigns a courier, vehicle details, and estimated delivery timestamp.

- **Endpoint:** `POST /api/v1/admin/deliveries/{id}/assign`
- **Request Body:**
```json
{
    "driver_name": "Marcus Vance",
    "driver_phone": "+14155550199",
    "vehicle_info": "Silver Toyota Prius (CA 7XYZ89)",
    "estimated_delivery_time": "2026-10-08 14:30:00"
}
```
- **Response (`200 OK`):**
```json
{
    "message": "Driver assigned successfully",
    "delivery": {
        "id": 8,
        "tracking_number": "TRK-US-88219034",
        "delivery_status": "assigned",
        "driver_name": "Marcus Vance",
        "driver_phone": "+14155550199",
        "vehicle_info": "Silver Toyota Prius (CA 7XYZ89)",
        "estimated_delivery_time": "2026-10-08T14:30:00.000000Z"
    }
}
```

---

### Update Delivery Status & Proof Photo
- **Endpoint:** `PATCH /api/v1/admin/deliveries/{id}/status`
- **Request Body:**
```json
{
    "delivery_status": "delivered",
    "delivery_notes": "Delivered to front door",
    "proof_of_delivery_image": "https://..."
}
```
- **Allowed `delivery_status` Values:** `pending`, `assigned`, `picked_up`, `in_transit`, `out_for_delivery`, `delivered`, `failed`, `returned`
- **Response (`200 OK`)**

---

### Update Live GPS Driver Coordinates
Used by driver apps or dispatch tracking simulation to broadcast the courier's real-time latitude & longitude.

- **Endpoint:** `PATCH /api/v1/admin/deliveries/{id}/location`
- **Request Body:**
```json
{
    "latitude": 37.774929,
    "longitude": -122.419416
}
```
- **Response (`200 OK`):**
```json
{
    "message": "Location updated successfully",
    "current_location": {
        "latitude": 37.774929,
        "longitude": -122.419416
    }
}
```
