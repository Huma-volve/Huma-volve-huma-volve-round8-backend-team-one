# Full API Endpoint List

**Base URL**: `http://127.0.0.1:8000/api`

## 🔓 Public Endpoints (No Token Required)

### Authentication

-   `POST` http://127.0.0.1:8000/api/auth/login
-   `POST` http://127.0.0.1:8000/api/auth/register
-   `POST` http://127.0.0.1:8000/api/auth/verify-otp
-   `POST` http://127.0.0.1:8000/api/auth/google-login
-   `POST` http://127.0.0.1:8000/api/auth/google-register
-   `POST` http://127.0.0.1:8000/api/auth/forget-password
-   `POST` http://127.0.0.1:8000/api/auth/reset-password

### General

-   `GET` http://127.0.0.1:8000/api/specialties
-   `GET` http://127.0.0.1:8000/api/doctors
-   `GET` http://127.0.0.1:8000/api/doctors/{id}
-   `GET` http://127.0.0.1:8000/api/doctors/{id}/availability

---

## 🔐 Protected Endpoints (Bearer Token Required)

### User Profile

-   `GET` http://127.0.0.1:8000/api/user
-   `POST` http://127.0.0.1:8000/api/profile/logout
-   `POST` http://127.0.0.1:8000/api/profile/edit
-   `POST` http://127.0.0.1:8000/api/profile/change-password
-   `DELETE` http://127.0.0.1:8000/api/profile/delete
-   `GET` http://127.0.0.1:8000/api/profile/favorites
-   `POST` http://127.0.0.1:8000/api/profile/notifications/toggle

### Interactions

-   `POST` http://127.0.0.1:8000/api/doctors/{id}/favorite

### Wallet & Payment Methods

-   `GET` http://127.0.0.1:8000/api/profile/payment-methods
-   `POST` http://127.0.0.1:8000/api/profile/payment-methods
-   `POST` http://127.0.0.1:8000/api/profile/payment-methods/{id}/default
-   `GET` http://127.0.0.1:8000/api/saved-cards
-   `POST` http://127.0.0.1:8000/api/saved-cards
-   `DELETE` http://127.0.0.1:8000/api/saved-cards/{id}
-   `POST` http://127.0.0.1:8000/api/payments/process

### Chat & Conversations

-   `GET` http://127.0.0.1:8000/api/conversations
-   `POST` http://127.0.0.1:8000/api/conversations/start
-   `GET` http://127.0.0.1:8000/api/conversations/{id}
-   `POST` http://127.0.0.1:8000/api/conversations/{id}/messages
-   `POST` http://127.0.0.1:8000/api/conversations/{id}/mark-read
-   `PATCH` http://127.0.0.1:8000/api/conversations/{id}/archive
-   `PATCH` http://127.0.0.1:8000/api/conversations/{id}/favorite

### Bookings

-   `GET` http://127.0.0.1:8000/api/bookings
-   `POST` http://127.0.0.1:8000/api/bookings
-   `GET` http://127.0.0.1:8000/api/bookings/{id}
-   `PUT` http://127.0.0.1:8000/api/bookings/{id}
-   `DELETE` http://127.0.0.1:8000/api/bookings/{id}
-   `POST` http://127.0.0.1:8000/api/bookings/{id}/cancel

### Reviews

-   `POST` http://127.0.0.1:8000/api/reviews
-   `GET` http://127.0.0.1:8000/api/reviews/doctor
-   `POST` http://127.0.0.1:8000/api/reviews/doctor/{reviewId}/reply

### Notifications

-   `GET` http://127.0.0.1:8000/api/notifications
-   `GET` http://127.0.0.1:8000/api/notifications/unread
-   `POST` http://127.0.0.1:8000/api/notifications/{id}/read
