# 🛒 Storefront & Catalog API Documentation

**Base URL:** `https://webtesting.bond` (or local `http://127.0.0.1:8000`)  
**Prefix:** `/api/v1`

---

## 🏷️ 1. Categories & Subcategories

### List Categories
Returns all product categories with their nested subcategories.

- **Endpoint:** `GET /api/v1/categories`
- **Access:** Public
- **Response (`200 OK`):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Produce",
            "description": "Fresh organic fruits and vegetables",
            "image": "https://images.unsplash.com/photo-1610832958506-aa56368176cf",
            "sub_categories": [
                {
                    "id": 1,
                    "category_id": 1,
                    "name": "Fresh Fruits",
                    "slug": "fresh-fruits",
                    "description": "Apples, bananas, citrus & berries"
                }
            ]
        }
    ]
}
```

---

### Single Category
- **Endpoint:** `GET /api/v1/categories/{id}`
- **Access:** Public

---

### List Subcategories
- **Endpoint:** `GET /api/v1/subcategories`
- **Access:** Public

---

### Single Subcategory
- **Endpoint:** `GET /api/v1/subcategories/{id}`
- **Access:** Public

---

## 🍎 2. Product Catalog

### Search & Filter Products
Fetches grocery products with advanced search, facet filters, sorting, and pagination.

- **Endpoint:** `GET /api/v1/products`
- **Access:** Public

#### Query Parameters
| Parameter | Type | Example | Description |
| :--- | :--- | :--- | :--- |
| `search` | `string` | `apple` | Searches name, brand, SKU, UPC barcode, slug, description |
| `category_id` | `integer` | `1` | Filter by main category ID |
| `subcategory_id` | `integer` | `2` | Filter by subcategory ID |
| `brand` | `string` | `Chobani` | Filter by brand name |
| `tag` | `string` | `Organic` | Filter by product tag (e.g. `Organic`, `Sale`, `Trending`) |
| `is_organic` | `boolean` | `1` | `1` for USDA Organic |
| `is_gluten_free` | `boolean` | `1` | `1` for Gluten-Free |
| `is_perishable` | `boolean` | `1` | `1` for cold-chain / refrigerated |
| `local` | `boolean` | `1` | `1` for locally sourced items |
| `min_price` | `numeric` | `2.50` | Minimum retail price |
| `max_price` | `numeric` | `15.00` | Maximum retail price |
| `status` | `string` | `active` | `active`, `inactive`, or `out_of_stock` |
| `sort_by` | `string` | `price_asc` | `price_asc`, `price_desc`, `name_asc`, `name_desc`, `latest` |
| `per_page` | `integer` | `20` | Results per page (Default: `20`) |
| `all` | `boolean` | `true` | Returns all results without pagination |

#### Response (`200 OK`)
```json
{
    "data": [
        {
            "id": 1,
            "subcategory_id": 1,
            "category_id": 1,
            "name": "Organic Honeycrisp Apples",
            "slug": "organic-honeycrisp-apples",
            "brand": "Oliva Farms",
            "sku": "US-PROD-101",
            "upc_barcode": "012345678905",
            "unit_size": "2 lbs bag",
            "price": "4.99",
            "old_price": "5.49",
            "sale_price": "3.99",
            "save_pct": 20,
            "stock": 48,
            "is_organic": true,
            "is_gluten_free": true,
            "is_perishable": true,
            "image": "https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6",
            "gallery_images": [
                "https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6"
            ],
            "nutrition_facts": {
                "calories": 95,
                "fat": "0.3g",
                "carbs": "25g",
                "protein": "0.5g"
            },
            "status": "active"
        }
    ],
    "current_page": 1,
    "last_page": 3,
    "total": 54
}
```

---

### Single Product Details
- **Endpoint:** `GET /api/v1/products/{id}`
- **Access:** Public

---

## 🛍️ 3. Cart APIs

The cart system supports both **Guest Shoppers** (using `X-Session-ID` header or `session_id` query) and **Authenticated Users** (using Sanctum `Bearer` token). When a guest logs in, their cart automatically merges into their user account.

### Headers for Cart
- **For Authenticated User:** `Authorization: Bearer <token>`
- **For Guest Shopper:** `X-Session-ID: <uuid_or_custom_guest_id>`

---

### Get Active Cart & Pricing Summary
Calculates items, line totals, subtotal, delivery fee, taxes, tips, and **$35 Free Delivery Progress**.

- **Endpoint:** `GET /api/v1/cart`
- **Access:** Public / Authenticated
- **Response (`200 OK`):**
```json
{
    "session_id": "7a8f9c12-3456-4789-abcd-1234567890ef",
    "cart": {
        "items_count": 2,
        "items": [
            {
                "id": 101,
                "product_id": 1,
                "name": "Organic Honeycrisp Apples",
                "brand": "Oliva Farms",
                "unit_size": "2 lbs bag",
                "image": "https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6",
                "unit_price": "3.99",
                "quantity": 2,
                "line_total": "7.98",
                "in_stock": true,
                "available_stock": 48
            }
        ],
        "subtotal": "7.98",
        "delivery_fee": "2.99",
        "estimated_tax": "0.50",
        "total": "11.47",
        "free_delivery_threshold": "35.00",
        "amount_needed_for_free_delivery": "27.02",
        "free_delivery_progress_percentage": 22.8,
        "is_eligible_for_free_delivery": false
    }
}
```

---

### Add Item to Cart
- **Endpoint:** `POST /api/v1/cart/items`
- **Request Body:**
```json
{
    "product_id": 1,
    "quantity": 2
}
```

---

### Update Item Quantity
- **Endpoint:** `PUT /api/v1/cart/items/{cart_item_id}`
- **Request Body:**
```json
{
    "quantity": 3
}
```
*(Sending `quantity: 0` automatically removes the item)*

---

### Remove Item from Cart
- **Endpoint:** `DELETE /api/v1/cart/items/{cart_item_id}`

---

### Clear Entire Cart
- **Endpoint:** `DELETE /api/v1/cart/clear`

---

### Preview Checkout Breakdown (Coupons, Tips & Delivery Mode)
Calculates real-time checkout totals with coupon codes (e.g. `FRESH10` for 10% off) or driver tip.

- **Endpoint:** `POST /api/v1/cart/preview`
- **Request Body:**
```json
{
    "order_type": "delivery",
    "tip_amount": 3.00,
    "coupon_code": "FRESH10"
}
```
- **Response (`200 OK`):**
```json
{
    "subtotal": "38.00",
    "delivery_fee": "0.00",
    "estimated_tax": "2.39",
    "tip_amount": "3.00",
    "discount_amount": "3.80",
    "total": "39.59",
    "is_free_delivery": true,
    "amount_needed_for_free_delivery": "0.00"
}
```

---

## 🚚 4. Delivery Windows & Public Order Tracking

### Available Delivery Windows
- **Endpoint:** `GET /api/v1/delivery-slots`
- **Response (`200 OK`):**
```json
{
    "slots": [
        {
            "id": "asap",
            "title": "ASAP",
            "subtitle": "In about 45 min",
            "badge": "FASTEST",
            "is_default": true,
            "available": true
        },
        {
            "id": "today_18_19",
            "title": "6:00 – 7:00 pm",
            "subtitle": "Today",
            "badge": null,
            "is_default": false,
            "available": true
        },
        {
            "id": "tomorrow_09_11",
            "title": "9:00 – 11:00 am",
            "subtitle": "Tomorrow",
            "badge": null,
            "is_default": false,
            "available": true
        }
    ]
}
```

---

### Public Live Order Tracking
Allows customers or recipients to track delivery status and real-time courier location via tracking number without logging in.

- **Endpoint:** `GET /api/v1/tracking/{tracking_number}`
- **Example:** `GET /api/v1/tracking/TRK-US-A1B2C3D4`
- **Access:** Public
- **Response (`200 OK`):**
```json
{
    "tracking_number": "TRK-US-A1B2C3D4",
    "delivery_status": "in_transit",
    "driver_name": "Marcus Vance",
    "driver_phone": "+14155550199",
    "vehicle_info": "Silver Toyota Prius (CA 7XYZ89)",
    "current_location": {
        "latitude": 37.774929,
        "longitude": -122.419416
    },
    "estimated_delivery_time": "2026-10-08T14:30:00.000000Z",
    "delivery_notes": "Gate code #4492",
    "order_summary": {
        "order_number": "US-ORD-20261008-AB12CD",
        "items_count": 4,
        "total_price": "39.59",
        "shipping_address": {
            "line1": "123 Market St",
            "line2": "Apt 4B",
            "city": "San Francisco",
            "state": "CA",
            "zip_code": "94103"
        }
    }
}
```
