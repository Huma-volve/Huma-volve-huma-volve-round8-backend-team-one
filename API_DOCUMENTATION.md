# Backend API Documentation

Base URL: `http://localhost:8000/api` (or your server URL)

All "Protected" routes require the `Authorization` header:
`Authorization: Bearer <token>`

---

## 1. Authentication

### Login

-   **Method:** `POST`
-   **URL:** `/auth/login`
-   **Request Body:**
    ```json
    {
        "phone": "01xxxxxxxxx", // Required, Egypt phone format
        "password": "password", // Required
        "remember_me": "on" // Optional (on/off)
    }
    ```
-   **Response:**
    -   Success (200): Returns Auth Token and User Data.

### Register

-   **Method:** `POST`
-   **URL:** `/auth/register`
-   **Request Body:**
    ```json
    {
        "name": "John Doe", // Required
        "email": "john@example.com", // Required, Unique
        "phone": "01xxxxxxxxx", // Required, Egypt format, Unique
        "password": "password", // Required, Min 8 chars
        "password_confirmation": "password" // Required
    }
    ```
-   **Response:**
    -   Success (201): returns `UserResource` and message "Account created. Please verify using the OTP...".

### Verify OTP

-   **Method:** `POST`
-   **URL:** `/auth/verify-otp`
-   **Request Body:**
    ```json
    {
        "phone": "01xxxxxxxxx", // Required
        "otp": "1234" // Required, 4 digits
    }
    ```
-   **Response:**
    -   Success: Returns Auth Token.

### Google Login

-   **Method:** `POST`
-   **URL:** `/auth/google-login`
-   **Request Body:**
    ```json
    {
        "id_token": "..." // Required (from Google)
    }
    ```
-   **Response:**
    -   Success: Returns `{ "token": "..." }`.

### Google Register

-   **Method:** `POST`
-   **URL:** `/auth/google-register`
-   **Request Body:**
    ```json
    {
        "id_token": "..." // Required
    }
    ```
-   **Response:**
    -   Success: Returns `{ "token": "..." }`.

### Forget Password

-   **Method:** `POST`
-   **URL:** `/auth/forget-password`
-   **Request Body:**
    ```json
    {
        "phone": "01xxxxxxxxx" // Required
    }
    ```
-   **Response:**
    -   Success: Sends OTP.

### Reset Password

-   **Method:** `POST`
-   **URL:** `/auth/reset-password`
-   **Request Body:**
    ```json
    {
        "phone": "01xxxxxxxxx", // Required
        "new_password": "newpassword", // Required, Min 8
        "new_password_confirmation": "newpassword"
    }
    ```
-   **Response:**
    -   Success: Password updated.

---

## 2. Public Data

### Get Specialties

-   **Method:** `GET`
-   **URL:** `/specialties`
-   **Response:**
    ```json
    [
        {
            "id": 1,
            "name": "Dermatology",
            "image": "http://..."
        },
        ...
    ]
    ```

### Get Doctors (List)

-   **Method:** `GET`
-   **URL:** `/doctors`
-   **Query Parameters (Filters):**
    -   `search`: string (name search)
    -   `specialty_id`: integer
    -   `min_rating`: numeric (0-5)
    -   `min_price`, `max_price`: numeric
    -   `available_date`: YYYY-MM-DD
    -   `sort_by`: `rating`, `price`, `distance`, `experience`
    -   `sort_order`: `asc`, `desc`
    -   `per_page`: integer
-   **Response:** Paginated list of Doctor Resources.

### Get Doctor Details

-   **Method:** `GET`
-   **URL:** `/doctors/{doctor_id}`
-   **Response:** Single Doctor Resource.
    -   Includes: `id`, `name`, `specialty`, `location`, `rating`, `reviews` (optional), `availability` (optional).

### Get Doctor Availability

-   **Method:** `GET`
-   **URL:** `/doctors/{doctor_id}/availability`
-   **Response:** List of availability slots.
    ```json
    [
        {
            "id": 1,
            "date": "2024-12-10",
            "start_time": "10:00",
            "end_time": "10:30",
            "is_active": true,
            "is_booked": false
        }
    ]
    ```

---

## 3. Profile Management (Protected)

### Get User Info

-   **Method:** `GET`
-   **URL:** `/user`
-   **Response:** User object.

### Edit Profile

-   **Method:** `POST`
-   **URL:** `/profile/edit`
-   **Request Body:**
    ```json
    {
        "name": "New Name",
        "email": "new@email.com",
        "phone": "01xxxxxxxxx",
        "birthdate": "1990-01-01"
    }
    ```

### Change Password

-   **Method:** `POST`
-   **URL:** `/profile/change-password`
-   **Request Body:**
    ```json
    {
        "current_password": "oldpassword",
        "new_password": "newpassword",
        "new_password_confirmation": "newpassword"
    }
    ```

### Logout

-   **Method:** `POST`
-   **URL:** `/profile/logout`

### Delete Account

-   **Method:** `DELETE`
-   **URL:** `/profile/delete`

### Get Favorites

-   **Method:** `GET`
-   **URL:** `/profile/favorites`
-   **Response:** List of favorite doctors.

### Toggle Favorite Doctor

-   **Method:** `POST`
-   **URL:** `/doctors/{doctor_id}/favorite`
-   **Response:** `{ "is_favorite": true/false }`

### Notification Settings (Toggle)

-   **Method:** `POST`
-   **URL:** `/profile/notifications/toggle`
-   **Request Body:** `{ "enable": true/false }`

### Saved Cards (Payment Methods)

-   **Method:** `GET`
-   **URL:** `/saved-cards`
-   **Response:** List of saved cards.

    ```json
    [
        {
            "id": 1,
            "brand": "Visa",
            "last_four": "4242",
            "is_default": true
        }
    ]
    ```

-   **Method:** `POST`
-   **URL:** `/saved-cards`
-   **Request Body:**

    ```json
    {
        "provider_token": "pm_card_...", // Token from Stripe
        "brand": "Visa",
        "last_four": "4242",
        "exp_month": 12,
        "exp_year": 2025,
        "is_default": true
    }
    ```

-   **Method:** `DELETE`
-   **URL:** `/saved-cards/{id}`

---

## 4. Chat / Conversations (Protected)

### Get Conversations

-   **Method:** `GET`
-   **URL:** `/conversations`
-   **Query Params:** `search` (optional)
-   **Response:** List of conversations.

### Start Conversation

-   **Method:** `POST`
-   **URL:** `/conversations/start`
-   **Request Body:** `{ "doctor_id": 123 }`
-   **Response:** Conversation Resource.

### Get Messages

-   **Method:** `GET`
-   **URL:** `/conversations/{conversation_id}`
-   **Response:** List of messages.

### Send Message

-   **Method:** `POST`
-   **URL:** `/conversations/{conversation_id}/messages`
-   **Request Body:** (Multipart/Form-Data if file)
    -   `body`: string (required if no attachment)
    -   `attachment`: file (optional, max 50MB)
-   **Response:** Message Resource.

### Mark as Read

-   **Method:** `POST`
-   **URL:** `/conversations/{conversation_id}/mark-read`

---

## 5. Bookings (Protected)

### Get Bookings

-   **Method:** `GET`
-   **URL:** `/bookings`
-   **Response:** List of bookings (includes doctor/patient info, status, etc.).

### Create Booking

-   **Method:** `POST`
-   **URL:** `/bookings`
-   **Request Body:**
    ```json
    {
        "doctor_id": 1,
        "appointment_date": "2024-12-10",
        "appointment_time": "10:00", // H:i format
        "payment_method": "stripe", // paypal, stripe, cash
        "notes": "Optional notes"
    }
    ```
    _Note: If payment_method is stripe, it attempts to charge the user's default saved card._

### Cancel Booking

-   **Method:** `POST`
-   **URL:** `/bookings/{booking_id}/cancel`
-   **Request Body:**
    ```json
    {
        "cancellation_reason": "Reason..."
    }
    ```

---

## 6. Payments (Protected)

### Process Payment (Manual)

-   **Method:** `POST`
-   **URL:** `/payments/process`
-   **Request Body:**
    ```json
    {
        "booking_id": 123,
        "payment_method_id": "pm_...", // Stripe Payment Method ID
        "gateway": "stripe"
    }
    ```

---

## 7. Reviews (Protected)

### Get Doctor Reviews

-   **Method:** `GET`
-   **URL:** `/reviews/doctor`
-   **Response:** List of reviews for the logged-in doctor.

### Submit Review

-   **Method:** `POST`
-   **URL:** `/reviews`
-   **Request Body:**
    ```json
    {
        "booking_id": 123,
        "rating": 5, // 1-5
        "comment": "Great doctor!"
    }
    ```

### Reply to Review (Doctor)

-   **Method:** `POST`
-   **URL:** `/reviews/doctor/{review_id}/reply`
-   **Request Body:**
    ```json
    {
        "doctor_response": "Thank you!"
    }
    ```

---

## 8. Notifications (Protected)

### Get Notifications

-   **Method:** `GET`
-   **URL:** `/notifications`
-   **Response:** List of all notifications.

### Get Unread Notifications

-   **Method:** `GET`
-   **URL:** `/notifications/unread`

### Mark Notification as Read

-   **Method:** `POST`
-   **URL:** `/notifications/{id}/read`
