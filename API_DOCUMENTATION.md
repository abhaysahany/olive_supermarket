# Marketplace Project API Documentation

Base URL: `/api`

## Authentication

All authenticated endpoints require a Bearer token in the `Authorization` header.
Example: `Authorization: Bearer <your_token_here>`

---

### 1. Register User / Admin

- **Endpoint:** `/register`
- **Method:** `POST`
- **Description:** Registers a new user or admin.
- **Request Body (JSON):**
  ```json
  {
      "name": "John Doe",
      "email": "john@example.com",
      "password": "password123",
      "password_confirmation": "password123",
      "role": "user" // optional: 'user' or 'admin'
  }
  ```
- **Success Response:** `201 Created`
  ```json
  {
      "message": "User registered successfully",
      "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com",
          "role": "user",
          "created_at": "...",
          "updated_at": "..."
      },
      "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
  ```

### 2. Login

- **Endpoint:** `/login`
- **Method:** `POST`
- **Description:** Authenticates a user and returns a token.
- **Request Body (JSON):**
  ```json
  {
      "email": "john@example.com",
      "password": "password123"
  }
  ```
- **Success Response:** `200 OK`
  ```json
  {
      "message": "Login successful",
      "user": { ... },
      "token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
  ```
- **Error Response:** `401 Unauthorized`
  ```json
  {
      "message": "Invalid credentials"
  }
  ```

### 3. Logout (Requires Auth)

- **Endpoint:** `/logout`
- **Method:** `POST`
- **Description:** Logs out the current user and invalidates the token.
- **Headers:** `Authorization: Bearer <token>`
- **Success Response:** `200 OK`
  ```json
  {
      "message": "Logged out successfully"
  }
  ```

---

## Admin Endpoints

These endpoints require the user to be authenticated and have the `admin` role.

### 1. Admin Dashboard

- **Endpoint:** `/admin/dashboard`
- **Method:** `GET`
- **Description:** Retrieves the admin dashboard data.
- **Headers:** `Authorization: Bearer <admin_token>`
- **Success Response:** `200 OK`
  ```json
  {
      "message": "Welcome to Admin Dashboard"
  }
  ```

### 2. Get All Users

- **Endpoint:** `/admin/users`
- **Method:** `GET`
- **Description:** Retrieves a list of all registered users.
- **Headers:** `Authorization: Bearer <admin_token>`
- **Success Response:** `200 OK`
  ```json
  {
      "users": [
          {
              "id": 1,
              "name": "Admin User",
              "email": "admin@example.com",
              "role": "admin"
          }
      ]
  }
  ```

---

## User Endpoints

These endpoints require the user to be authenticated.

### 1. User Profile

- **Endpoint:** `/user/profile`
- **Method:** `GET`
- **Description:** Retrieves the profile of the currently authenticated user.
- **Headers:** `Authorization: Bearer <token>`
- **Success Response:** `200 OK`
  ```json
  {
      "user": {
          "id": 2,
          "name": "John Doe",
          "email": "john@example.com",
          "role": "user"
      }
  }
  ```
