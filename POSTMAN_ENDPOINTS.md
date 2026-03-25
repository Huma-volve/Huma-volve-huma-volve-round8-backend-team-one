# Postman Endpoints (Raw JSON Bodies)

Copy the JSON content into the **Body -> raw -> JSON** section of Postman.

**Base URL:** `http://localhost:8000/api`
**Headers:** `Authorization: Bearer <your_token>` (for protected routes)

---

## Auth

**POST** `/auth/login`

```json
{
    "phone": "01012345678",
    "password": "password",
    "remember_me": "on"
}
```

**POST** `/auth/register`

```json
{
    "name": "Test Patient",
    "email": "test_patient@example.com",
    "phone": "01012345678",
    "password": "password",
    "password_confirmation": "password"
}
```

**POST** `/auth/verify-otp`

```json
{
    "phone": "01012345678",
    "otp": "1234"
}
```

**POST** `/auth/forget-password`

```json
{
    "phone": "01012345678"
}
```

**POST** `/auth/reset-password`

```json
{
    "phone": "01012345678",
    "new_password": "newPassword123",
    "new_password_confirmation": "newPassword123"
}
```

---

## Profile (Protected)

**POST** `/profile/edit`

```json
{
    "name": "Updated Name",
    "email": "updated@example.com",
    "birthdate": "1995-05-20"
}
```

**POST** `/profile/change-password`

```json
{
    "current_password": "password",
    "new_password": "newPassword123",
    "new_password_confirmation": "newPassword123"
}
```

**POST** `/profile/notifications/toggle`

```json
{
    "enable": true
}
```

---

## Bookings (Protected)

**POST** `/bookings`

```json
{
    "doctor_id": 1,
    "appointment_date": "2024-12-25",
    "appointment_time": "14:00",
    "payment_method": "cash",
    "notes": "Regular checkup"
}
```

**POST** `/bookings/{id}/cancel`

```json
{
    "cancellation_reason": "Something came up"
}
```

---

## Payment Methods (Protected)

**POST** `/saved-cards`

```json
{
    "provider_token": "pm_sample_token_from_stripe",
    "brand": "Visa",
    "last_four": "4242",
    "exp_month": 12,
    "exp_year": 2025,
    "is_default": true
}
```

---

## Chat (Protected)

**POST** `/conversations/start`

```json
{
    "doctor_id": 1
}
```

**POST** `/conversations/{id}/messages`

```json
{
    "body": "Hello doctor!"
}
```

---

## Reviews (Protected)

**POST** `/reviews`

```json
{
    "booking_id": 1,
    "rating": 5,
    "comment": "Excellent service!"
}
```

**POST** `/reviews/doctor/{review_id}/reply`

```json
{
    "doctor_response": "Thank you for your feedback!"
}
```
