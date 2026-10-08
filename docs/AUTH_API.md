# 🔐 Authentication API Documentation

**Base URL:** `https://webtesting.bond` (or local `http://127.0.0.1:8000`)  
**Prefix:** `/api/v1/auth`

---

## Headers

| Header | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `Accept` | `string` | **Yes** | Must be `application/json` |
| `Content-Type` | `string` | **Yes** | Must be `application/json` for POST/PUT requests |
| `Authorization` | `string` | For protected routes | `Bearer <token>` (Required for `/logout`) |

---

## Endpoints

### 1. Register User

Creates a new user account and returns an authentication token.

- **Endpoint:** `POST /api/v1/auth/register`
- **Access:** Public

#### Request Body
```json
{
    "name": "Michael O.",
    "email": "michael@example.com",
    "phone": "+15125550142",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
}
```

#### Response (`201 Created`)
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "Michael O.",
        "email": "michael@example.com",
        "phone": "+15125550142",
        "role": "user",
        "created_at": "2026-10-08T06:30:00.000000Z"
    },
    "token": "1|sanctum_token_string..."
}
```

---

### 2. Login

Authenticates existing user with email and password.

- **Endpoint:** `POST /api/v1/auth/login`
- **Access:** Public

#### Request Body
```json
{
    "email": "michael@example.com",
    "password": "password123"
}
```

#### Response (`200 OK`)
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Michael O.",
        "email": "michael@example.com",
        "phone": "+15125550142",
        "role": "user"
    },
    "token": "2|sanctum_token_string..."
}
```

---

### 3. Social Login (Google / Facebook)

Authenticates or registers a user via OAuth provider token.

- **Endpoint:** `POST /api/v1/auth/social-login`
- **Access:** Public

#### Request Body
```json
{
    "provider": "google",
    "token": "oauth_token_from_google_or_facebook"
}
```

#### Response (`200 OK`)
```json
{
    "message": "Social login successful",
    "user": {
        "id": 2,
        "name": "Sarah Connor",
        "email": "sarah@gmail.com",
        "role": "user"
    },
    "token": "3|sanctum_token_string..."
}
```

---

### 4. Forgot Password (Request Reset Link)

Dispatches a password reset email with a tokenized React frontend link.

- **Endpoint:** `POST /api/v1/auth/forgot-password`
- **Access:** Public

#### Request Body
```json
{
    "email": "michael@example.com"
}
```

#### Response (`200 OK`)
```json
{
    "message": "We have emailed your password reset link."
}
```

---

### 5. Validate Reset Token

Verifies whether a reset token is valid before rendering the reset password form in React.

- **Endpoint:** `POST /api/v1/auth/validate-reset-token` (also supports `GET /api/v1/auth/validate-reset-token?token=...&email=...`)
- **Access:** Public

#### Request Body
```json
{
    "email": "michael@example.com",
    "token": "6c4e0b0e5ad3e8e19b88..."
}
```

#### Response (`200 OK - Valid Token`)
```json
{
    "valid": true,
    "message": "Reset token is valid.",
    "email": "michael@example.com",
    "name": "Michael O."
}
```

#### Response (`400 Bad Request - Invalid / Expired Token`)
```json
{
    "valid": false,
    "message": "This password reset link is invalid or has expired."
}
```

---

### 6. Reset Password

Sets a new password using the validated token.

- **Endpoint:** `POST /api/v1/auth/reset-password`
- **Access:** Public

#### Request Body
```json
{
    "token": "6c4e0b0e5ad3e8e19b88...",
    "email": "michael@example.com",
    "password": "NewSecurePassword123",
    "password_confirmation": "NewSecurePassword123"
}
```

#### Response (`200 OK`)
```json
{
    "message": "Your password has been reset."
}
```

---

### 7. Logout

Revokes the current Sanctum token and terminates session.

- **Endpoint:** `POST /api/v1/auth/logout`
- **Access:** Authenticated (`Authorization: Bearer <token>`)

#### Response (`200 OK`)
```json
{
    "message": "Logged out successfully"
}
```
