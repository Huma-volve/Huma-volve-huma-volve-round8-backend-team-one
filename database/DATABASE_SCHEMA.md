# Database Schema - Entity Relationship Diagram

## Doctor Appointment Mobile App
;;;;
```jkljljllj
┌─────────────────────────────────────────────────────────────────────────────┐
│                           USERS (Central Table)                              │
├─────────────────────────────────────────────────────────────────────────────┤
│ id (PK)                                                                      │
│ name                                                                         │
│ email (UNIQUE)                                                               │
│ mobile (UNIQUE)                                                              │
│ password                                                                     │
│ google_id (nullable)                                                         │
│ role (patient|doctor|admin|helper)                                           │
│ profile_photo_path (nullable)                                                │
│ is_active                                                                    │
│ notifications_enabled                                                        │
│ mobile_verified_at (nullable)                                                │
│ email_verified_at (nullable)                                                 │
│ timestamps, soft_deletes                                                     │
└─────────────────────────────────────────────────────────────────────────────┘
         │                    │                    │
         │                    │                    │
         ▼                    ▼                    ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│    PATIENTS      │  │     DOCTORS      │  │ ADMIN_PERMISSIONS│
├──────────────────┤  ├──────────────────┤  ├──────────────────┤
│ id (PK)          │  │ id (PK)          │  │ id (PK)          │
│ user_id (FK)     │  │ user_id (FK)     │  │ user_id (FK)     │
│ birthdate        │  │ specialty_id (FK)│  │ permissions(JSON)│
│ gender           │  │ license_number   │  │ timestamps       │
│ latitude         │  │ bio              │  └──────────────────┘
│ longitude        │  │ session_price    │
│ timestamps       │  │ clinic_address   │
└──────────────────┘  │ latitude         │
         │            │ longitude        │
         │            │ rating_avg       │
         │            │ total_reviews    │
         │            │ is_approved      │
         │            │ temporary_password│
         │            │ password_changed │
         │            │ timestamps       │
         │            └──────────────────┘
         │                     │
         │                     │
         │    ┌────────────────┴─────────────────┐
         │    │                                   │
         │    ▼                                   ▼
         │  ┌──────────────────┐      ┌──────────────────────┐
         │  │ AVAILABILITY_    │      │    SPECIALTIES       │
         │  │     SLOTS        │      ├──────────────────────┤
         │  ├──────────────────┤      │ id (PK)              │
         │  │ id (PK)          │      │ name                 │
         │  │ doctor_id (FK)   │      │ image                │
         │  │ date             │      │ timestamps           │
         │  │ start_time       │      └──────────────────────┘
         │  │ end_time         │
         │  │ is_available     │
         │  │ is_booked        │
         │  │ timestamps       │
         │  └──────────────────┘
         │
         │
         ├─────────────────────────────────────┐
         │                                     │
         ▼                                     ▼
┌──────────────────┐              ┌──────────────────────┐
│   FAVORITES      │              │   SEARCH_HISTORY     │
├──────────────────┤              ├──────────────────────┤
│ id (PK)          │              │ id (PK)              │
│ patient_id (FK)  │              │ patient_id (FK)      │
│ doctor_id (FK)   │              │ search_query         │
│ timestamps       │              │ search_type          │
└──────────────────┘              │ latitude             │
                                  │ longitude            │
                                  │ timestamps           │
                                  └──────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                              BOOKINGS                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│ id (PK)                                                                      │
│ doctor_id (FK → doctors)                                                     │
│ patient_id (FK → patients)                                                   │
│ appointment_date                                                             │
│ appointment_time                                                             │
│ status (pending|confirmed|completed|cancelled|rescheduled)                   │
│ price_at_booking                                                             │
│ payment_method (paypal|stripe|cash)                                          │
│ payment_status (unpaid|paid|failed|refunded)                                 │
│ payment_transaction_id                                                       │
│ notes                                                                        │
│ cancellation_reason                                                          │
│ cancelled_at                                                                 │
│ cancelled_by (FK → users)                                                    │
│ timestamps                                                                   │
└─────────────────────────────────────────────────────────────────────────────┘
         │                    │                    │
         │                    │                    │
         ▼                    ▼                    ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  TRANSACTIONS    │  │     REVIEWS      │  │    FEEDBACKS     │
├──────────────────┤  ├──────────────────┤  ├──────────────────┤
│ id (PK)          │  │ id (PK)          │  │ id (PK)          │
│ booking_id (FK)  │  │ doctor_id (FK)   │  │ patient_id (FK)  │
│ external_id      │  │ patient_id (FK)  │  │ booking_id (FK)  │
│ amount           │  │ booking_id (FK)  │  │ doctor_id (FK)   │
│ type             │  │ rating (1-5)     │  │ feedback_text    │
│ status           │  │ comment          │  │ rating (1-5)     │
│ gateway          │  │ doctor_response  │  │ timestamps       │
│ payload (JSON)   │  │ responded_at     │  └──────────────────┘
│ currency         │  │ timestamps       │
│ failure_reason   │  └──────────────────┘
│ timestamps       │
└──────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                          CONVERSATIONS                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│ id (PK)                                                                      │
│ patient_id (FK → patients)                                                   │
│ doctor_id (FK → doctors)                                                     │
│ last_message_at                                                              │
│ is_archived_by_patient                                                       │
│ is_archived_by_doctor                                                        │
│ is_favorited_by_patient                                                      │
│ is_favorited_by_doctor                                                       │
│ timestamps                                                                   │
│ UNIQUE(patient_id, doctor_id)                                                │
└─────────────────────────────────────────────────────────────────────────────┘
         │                              │
         │                              │
         ▼                              ▼
┌──────────────────────────┐  ┌──────────────────────────┐
│ CONVERSATION_PARTICIPANTS│  │        MESSAGES          │
├──────────────────────────┤  ├──────────────────────────┤
│ id (PK)                  │  │ id (PK)                  │
│ conversation_id (FK)     │  │ conversation_id (FK)     │
│ user_id (FK)             │  │ sender_id (FK → users)   │
│ unread_count             │  │ receiver_id (FK → users) │
│ last_read_at             │  │ content                  │
│ timestamps               │  │ type (text|image|video)  │
│ UNIQUE(conversation_id,  │  │ attachment_url           │
│        user_id)          │  │ is_read                  │
└──────────────────────────┘  │ read_at                  │
                              │ timestamps               │
                              └──────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│                           NOTIFICATIONS                                      │
├─────────────────────────────────────────────────────────────────────────────┤
│ id (PK)                                                                      │
│ user_id (FK → users)                                                         │
│ title                                                                        │
│ message                                                                      │
│ type (booking_created|booking_confirmed|booking_cancelled|...)               │
│ data (JSON)                                                                  │
│ is_read                                                                      │
│ read_at                                                                      │
│ timestamps                                                                   │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────┐  ┌──────────────────────┐  ┌──────────────────────┐
│  PAYMENT_METHODS     │  │      OTP_CODES       │  │   ANNOUNCEMENTS      │
├──────────────────────┤  ├──────────────────────┤  ├──────────────────────┤
│ id (PK)              │  │ id (PK)              │  │ id (PK)              │
│ patient_id (FK)      │  │ identifier           │  │ title                │
│ type                 │  │ code (6 chars)       │  │ message              │
│ provider_customer_id │  │ type                 │  │ target_audience      │
│ last_four            │  │ is_verified          │  │ is_active            │
│ card_brand           │  │ expires_at           │  │ published_at         │
│ expiry_month         │  │ verified_at          │  │ expires_at           │
│ expiry_year          │  │ timestamps           │  │ created_by (FK)      │
│ is_default           │  └──────────────────────┘  │ timestamps           │
│ timestamps           │                            └──────────────────────┘
└──────────────────────┘

┌──────────────────────┐  ┌──────────────────────┐  ┌──────────────────────┐
│        FAQS          │  │    APP_SETTINGS      │  │    SYSTEM_LOGS       │
├──────────────────────┤  ├──────────────────────┤  ├──────────────────────┤
│ id (PK)              │  │ id (PK)              │  │ id (PK)              │
│ question             │  │ key (UNIQUE)         │  │ user_id (FK)         │
│ answer               │  │ value                │  │ event_type           │
│ order                │  │ type                 │  │ description          │
│ is_active            │  │ description          │  │ ip_address           │
│ timestamps           │  │ timestamps           │  │ user_agent           │
└──────────────────────┘  └──────────────────────┘  │ metadata (JSON)      │
                                                     │ severity             │
                                                     │ timestamps           │
                                                     └──────────────────────┘
```

## Key Relationships

### One-to-One

-   `users` ↔ `patients` (when role = patient)
-   `users` ↔ `doctors` (when role = doctor)

### One-to-Many

-   `users` → `notifications`
-   `users` → `messages` (as sender/receiver)
-   `users` → `system_logs`
-   `specialties` → `doctors`
-   `doctors` → `bookings`
-   `doctors` → `reviews`
-   `doctors` → `availability_slots`
-   `patients` → `bookings`
-   `patients` → `favorites`
-   `patients` → `search_history`
-   `patients` → `payment_methods`
-   `bookings` → `transactions`
-   `bookings` → `reviews`
-   `bookings` → `feedbacks`
-   `conversations` → `messages`
-   `conversations` → `conversation_participants`

### Many-to-Many (via pivot tables)

-   `patients` ↔ `doctors` (via `favorites`)
-   `patients` ↔ `doctors` (via `bookings`)
-   `patients` ↔ `doctors` (via `conversations`)

## Cascade Rules

### ON DELETE CASCADE

-   When a user is deleted, all related records are deleted:
    -   `patients`, `doctors`, `notifications`, `messages`, `bookings`, etc.

### ON DELETE RESTRICT

-   Cannot delete a specialty if doctors are using it

### ON DELETE SET NULL

-   When a booking is deleted, reviews keep the booking_id as null
-   When a user is deleted, system_logs keep the user_id as null

## Indexes Summary

### Primary Indexes (PK)

-   All tables have `id` as primary key

### Unique Indexes

-   `users.email`
-   `users.mobile`
-   `doctors.license_number`
-   `favorites(patient_id, doctor_id)`
-   `conversations(patient_id, doctor_id)`
-   `conversation_participants(conversation_id, user_id)`
-   `availability_slots(doctor_id, date, start_time)`
-   `app_settings.key`

### Composite Indexes (for performance)

-   `doctors(doctor_id, rating)`
-   `bookings(doctor_id, appointment_date)`
-   `messages(conversation_id, created_at)`
-   `messages(sender_id, receiver_id)`
-   `notifications(user_id, is_read, created_at)`
-   `notifications(user_id, type)`
-   `system_logs(event_type, created_at)`
-   `system_logs(user_id, created_at)`
-   `favorites(patient_id, created_at)`
-   `search_history(patient_id, created_at)`
-   `payment_methods(patient_id, is_default)`
-   `feedbacks(doctor_id, created_at)`
-   `reviews(doctor_id, rating)`
-   `transactions(booking_id, status)`
-   `availability_slots(doctor_id, date, is_available)`
-   `conversation_participants(user_id, unread_count)`
-   `conversations(patient_id, last_message_at)`
-   `conversations(doctor_id, last_message_at)`
-   `announcements(is_active, published_at)`
-   `faqs(is_active, order)`
-   `otp_codes(identifier, code, is_verified)`

---

**Total Tables**: 27
**Total Relationships**: 40+
**Database Engine**: MySQL/PostgreSQL
**Laravel Version**: 12.x
