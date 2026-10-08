# Olivia Supermarket — REST API Documentation Hub

**Base URL:** `https://webtesting.bond` (or local `http://127.0.0.1:8000`)  
**API Version:** `v1` (`/api/v1`)

---

## 📚 Dedicated API Documentation Pages

For clarity and frontend/backend team collaboration, the documentation is divided into 4 modular documentation pages:

| Section | Documentation Page | Description & Key Endpoints |
| :--- | :--- | :--- |
| 🔐 **Authentication** | [docs/AUTH_API.md](docs/AUTH_API.md) | User Registration, Login, Social Auth, Forgot/Reset Password, Token Validation, Logout. |
| 🛒 **Storefront & Catalog** | [docs/STOREFRONT_API.md](docs/STOREFRONT_API.md) | Categories, Subcategories, Filterable Product Catalog, Cart APIs ($35 Free Delivery math, coupons), Delivery Slots, and Public Live Order Tracking. |
| 👤 **User & Customer** | [docs/USER_API.md](docs/USER_API.md) | Customer Profile, Multi-step Checkout / Place Order, Order History, Order Details, Customer Live Tracking. |
| 🛡️ **Admin Portal** | [docs/ADMIN_API.md](docs/ADMIN_API.md) | Admin Dashboard KPI stats, User List, Category CRUD, Subcategory CRUD, Product CRUD, Order Status workflow, Dispatch & Live Courier GPS Updates. |

---

## 🔑 Global Headers

| Header | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `Accept` | `string` | **Yes** | Must be `application/json` |
| `Content-Type` | `string` | **Yes** | `application/json` (for POST, PUT, PATCH requests) |
| `Authorization` | `string` | For protected routes | `Bearer <sanctum_access_token>` |
| `X-Session-ID` | `string` | For guest carts | UUID or unique string to persist a guest shopper's cart across browser sessions |

---

## 🚦 HTTP Response Codes

| Code | Status | Description |
| :--- | :--- | :--- |
| `200` | **OK** | Request completed successfully. |
| `201` | **Created** | New resource (User, Category, Product, Order) successfully created. |
| `400` | **Bad Request** | Invalid payload, expired reset token, or insufficient inventory stock. |
| `401` | **Unauthorized** | Missing, expired, or revoked Sanctum token. |
| `403` | **Forbidden** | User authenticated but lacks required role (e.g. non-admin accessing admin route). |
| `404` | **Not Found** | Resource, order, product, or tracking number not found. |
| `422` | **Unprocessable Entity** | Form validation failed (e.g., duplicate email, missing required fields). |
| `500` | **Server Error** | Internal application exception. |

---

## 🔗 Quick Endpoint Directory

### 1. Authentication ([Full Guide](docs/AUTH_API.md))
- `POST /api/v1/auth/register` — Register new user
- `POST /api/v1/auth/login` — Login user
- `POST /api/v1/auth/social-login` — Google / Facebook OAuth login
- `POST /api/v1/auth/forgot-password` — Email password reset link
- `POST /api/v1/auth/validate-reset-token` — Validate token before showing reset form
- `POST /api/v1/auth/reset-password` — Complete password reset
- `POST /api/v1/auth/logout` — Revoke active token (Auth required)

### 2. Storefront Browsing & Cart ([Full Guide](docs/STOREFRONT_API.md))
- `GET /api/v1/categories` — List all categories & subcategories
- `GET /api/v1/categories/{id}` — Single category
- `GET /api/v1/subcategories` — List subcategories
- `GET /api/v1/products` — Filter products by search, category, brand, organic, price, sorting
- `GET /api/v1/products/{id}` — Single product details
- `GET /api/v1/cart` — Get active cart + pricing calculations
- `POST /api/v1/cart/items` — Add item to cart
- `PUT /api/v1/cart/items/{id}` — Update item quantity
- `DELETE /api/v1/cart/items/{id}` — Remove item
- `DELETE /api/v1/cart/clear` — Empty cart
- `POST /api/v1/cart/preview` — Preview discount coupon & tip calculation
- `GET /api/v1/delivery-slots` — Available delivery time windows
- `GET /api/v1/tracking/{tracking_number}` — Public live tracking

### 3. Customer Operations ([Full Guide](docs/USER_API.md))
- `GET /api/v1/user/profile` — User profile, total order count & 5 recent orders
- `GET /api/v1/user/orders/recent` — Dedicated 5-10 recent orders for dashboard / reordering
- `POST /api/v1/user/orders` — Place order / checkout
- `GET /api/v1/user/orders` — Customer order history (paginated)
- `GET /api/v1/user/orders/{id}` — Single order details
- `GET /api/v1/user/orders/{id}/track-delivery` — Live courier GPS tracking

### 4. Admin Management ([Full Guide](docs/ADMIN_API.md))
- `GET /api/v1/admin/dashboard` — Revenue, orders, deliveries & stock metrics
- `GET /api/v1/admin/users` — List registered customers & staff
- `POST /api/v1/admin/categories` — Create category
- `PUT /api/v1/admin/categories/{id}` — Update category
- `DELETE /api/v1/admin/categories/{id}` — Delete category
- `POST /api/v1/admin/subcategories` — Create subcategory
- `PUT /api/v1/admin/subcategories/{id}` — Update subcategory
- `DELETE /api/v1/admin/subcategories/{id}` — Delete subcategory
- `POST /api/v1/admin/products` — Create product
- `PUT /api/v1/admin/products/{id}` — Update product
- `DELETE /api/v1/admin/products/{id}` — Delete product
- `GET /api/v1/admin/orders` — Search and filter all orders
- `PATCH /api/v1/admin/orders/{id}/status` — Update order & payment status
- `GET /api/v1/admin/deliveries` — List deliveries
- `POST /api/v1/admin/deliveries/{id}/assign` — Assign driver & dispatch
- `PATCH /api/v1/admin/deliveries/{id}/status` — Update delivery step & proof image
- `PATCH /api/v1/admin/deliveries/{id}/location` — Update live GPS coordinates
