# Supermarket API Documentation

Base URL: `http://127.0.0.1:8000/api/v1`

## Authentication

All authenticated endpoints require a Bearer token in the `Authorization` header.
Example: `Authorization: Bearer <your_token_here>`

---

## 1. Authentication Endpoints (Public)

### Register User / Admin
- **Endpoint:** `/auth/register`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "password": "password123",
      "password_confirmation": "password123",
      "role": "user" // optional: 'user' or 'admin'
  }
  ```

### Login
- **Endpoint:** `/auth/login`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
      "email": "john@example.com",
      "password": "password123"
  }
  ```

### Forgot Password
- **Endpoint:** `/auth/forgot-password`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
      "email": "john@example.com"
  }
  ```

### Reset Password
- **Endpoint:** `/auth/reset-password`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
      "token": "token_received_in_email",
      "email": "john@example.com",
      "password": "newpassword123",
      "password_confirmation": "newpassword123"
  }
  ```

### Logout (Requires Auth)
- **Endpoint:** `/auth/logout`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer <token>`

---

## 2. Public Shop Endpoints

These endpoints do NOT require authentication. Users can browse categories and products.

### Get All Categories
- **Endpoint:** `/categories`
- **Method:** `GET`

### Get Single Category
- **Endpoint:** `/categories/{id}`
- **Method:** `GET`

### Get All Products
- **Endpoint:** `/products`
- **Method:** `GET`

### Get Single Product
- **Endpoint:** `/products/{id}`
- **Method:** `GET`

---

## 3. Customer / User Endpoints (Requires Auth)

These endpoints require the user to be authenticated with `role: "user"` or `role: "admin"`.

### User Profile
- **Endpoint:** `/user/profile`
- **Method:** `GET`

### Get My Orders
- **Endpoint:** `/user/orders`
- **Method:** `GET`

### Place an Order
- **Endpoint:** `/user/orders`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
      "items": [
          { "product_id": 1, "quantity": 2 },
          { "product_id": 3, "quantity": 1 }
      ]
  }
  ```

### Get Order Details
- **Endpoint:** `/user/orders/{id}`
- **Method:** `GET`

---

## 4. Admin Endpoints (Requires Auth + Admin Role)

These endpoints require the user to be authenticated and have the `admin` role.

### Admin Dashboard & Users
- **GET** `/admin/dashboard`
- **GET** `/admin/users`

### Manage Categories
- **POST** `/admin/categories`
  - Body (JSON): `{"name": "Snacks", "description": "Tasty snacks", "image": "url"}`
- **PUT** `/admin/categories/{id}`
- **DELETE** `/admin/categories/{id}`

### Manage Products
- **POST** `/admin/products`
  - Body (JSON): `{"category_id": 1, "name": "Chips", "price": 10.50, "stock": 100}`
- **PUT** `/admin/products/{id}`
- **DELETE** `/admin/products/{id}`

### Manage Orders
- **GET** `/admin/orders` (View all orders across the store)
- **PATCH** `/admin/orders/{id}/status`
  - Body (JSON): `{"status": "completed"}` // Valid values: pending, processing, completed, cancelled

---

## 5. Common HTTP Status Codes

When consuming this API, you will encounter standard HTTP status codes:

- `200 OK` - Request was successful.
- `201 Created` - Resource (like a new User, Order, or Product) was successfully created.
- `400 Bad Request` - The request was invalid (e.g., incorrect format or logic error like out of stock).
- `401 Unauthorized` - Missing or invalid Bearer token. You need to log in.
- `403 Forbidden` - You are authenticated, but don't have permission (e.g., a User trying to access Admin routes).
- `404 Not Found` - The requested resource (URL or ID) does not exist.
- `422 Unprocessable Entity` - Validation failed. The request body is missing required fields or data is invalid.
- `500 Internal Server Error` - Something went wrong on the server side.
