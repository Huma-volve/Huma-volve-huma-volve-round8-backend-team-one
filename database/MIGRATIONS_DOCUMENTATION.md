# Database Migrations Documentation

## Doctor Appointment Mobile App

This document provides a comprehensive overview of all database migrations for the Doctor Appointment Mobile App based on the Software Requirements Specification (SRS).

---

## Table of Contents

1. [Core Tables](#core-tables)
2. [User Management](#user-management)
3. [Booking System](#booking-system)
4. [Communication](#communication)
5. [Payment System](#payment-system)
6. [Content Management](#content-management)
7. [Migration Order](#migration-order)

---

## Core Tables

### 1. **users** (0001_01_01_000000_create_users_table.php)

**Purpose**: Central user table for all user types (Patient, Doctor, Admin, Helper)

**Key Fields**:

-   `name`: User's full name
-   `email`: Unique email address
-   `mobile`: Unique mobile number (for OTP verification)
-   `password`: Hashed password
-   `google_id`: For Google OAuth login
-   `role`: Enum (patient, doctor, admin, helper)
-   `profile_photo_path`: Profile picture URL
-   `is_active`: Account status
-   `notifications_enabled`: User notification preference
-   `mobile_verified_at`: Mobile verification timestamp
-   `email_verified_at`: Email verification timestamp

**Relationships**:

-   One-to-One with `patients` (if role is patient)
-   One-to-One with `doctors` (if role is doctor)
-   One-to-Many with `bookings`, `messages`, `notifications`

---

### 2. **specialties** (2025_12_01_174425_create_specialties_table.php)

**Purpose**: Medical specialties for doctor categorization

**Key Fields**:

-   `name`: Specialty name (e.g., Cardiology, Dermatology)
-   `image`: Optional specialty icon/image

**Relationships**:

-   One-to-Many with `doctors`

---

## User Management

### 3. **patients** (2025_12_01_174957_create_patients_table.php)

**Purpose**: Patient-specific profile information

**Key Fields**:

-   `user_id`: Foreign key to users table
-   `birthdate`: Date of birth (optional)
-   `gender`: Enum (male, female) - optional
-   `latitude`, `longitude`: Current location for nearby doctor search

**Relationships**:

-   Belongs to `users`
-   One-to-Many with `bookings`, `favorites`, `search_history`, `feedbacks`

---

### 4. **doctors** (2025_12_01_174628_create_doctors_table.php)

**Purpose**: Doctor-specific profile and professional information

**Key Fields**:

-   `user_id`: Foreign key to users table
-   `specialty_id`: Foreign key to specialties table
-   `license_number`: Unique medical license number
-   `bio`: Doctor's biography
-   `session_price`: Price per session
-   `clinic_address`: Physical clinic address
-   `latitude`, `longitude`: Clinic location coordinates
-   `rating_avg`: Average rating (0-5)
-   `total_reviews`: Total number of reviews
-   `is_approved`: Admin approval status
-   `temporary_password`: Auto-generated password for admin-created accounts
-   `password_changed`: Track if temporary password was changed

**Relationships**:

-   Belongs to `users` and `specialties`
-   One-to-Many with `bookings`, `reviews`, `availability_slots`

**Note**: Doctor accounts are created by admin, not self-registered.

---

### 5. **admin_permissions** (2025_12_01_180700_create_admin_permissions_table.php)

**Purpose**: Role-Based Access Control (RBAC) for admin and helper users

**Key Fields**:

-   `user_id`: Foreign key to users table
-   `permissions`: JSON array of permissions (e.g., ['create_doctor', 'manage_users', 'view_reports'])

**Relationships**:

-   Belongs to `users`

---

## Booking System

### 6. **availability_slots** (2025_12_01_175515_create_availability_slots_table.php)

**Purpose**: Doctor's available time slots for appointments

**Key Fields**:

-   `doctor_id`: Foreign key to doctors table
-   `date`: Available date
-   `start_time`, `end_time`: Time slot range
-   `is_available`: Slot availability status
-   `is_booked`: Whether slot is already booked

**Unique Constraint**: `[doctor_id, date, start_time]` - prevents duplicate slots

**Relationships**:

-   Belongs to `doctors`

---

### 7. **bookings** (2025_12_01_175057_create_bookings_table.php)

**Purpose**: Patient appointment bookings with doctors

**Key Fields**:

-   `doctor_id`: Foreign key to doctors table
-   `patient_id`: Foreign key to patients table
-   `appointment_date`, `appointment_time`: Scheduled appointment
-   `status`: Enum (pending, confirmed, completed, cancelled, rescheduled)
-   `price_at_booking`: Session price at time of booking
-   `payment_method`: Enum (paypal, stripe, cash)
-   `payment_status`: Enum (unpaid, paid, failed, refunded)
-   `payment_transaction_id`: PayPal/Stripe transaction ID
-   `notes`: Additional notes
-   `cancellation_reason`: Reason for cancellation
-   `cancelled_at`: Cancellation timestamp
-   `cancelled_by`: User who cancelled the booking

**Relationships**:

-   Belongs to `doctors` and `patients`
-   One-to-Many with `transactions`, `reviews`, `feedbacks`

---

### 8. **reviews** (2025_12_01_175606_create_reviews_table.php)

**Purpose**: Public reviews and ratings for doctors

**Key Fields**:

-   `doctor_id`: Foreign key to doctors table
-   `patient_id`: Foreign key to patients table
-   `booking_id`: Optional foreign key to bookings table
-   `rating`: Integer (1-5)
-   `comment`: Review text (optional)
-   `doctor_response`: Doctor's response to review (optional)
-   `responded_at`: Response timestamp

**Relationships**:

-   Belongs to `doctors`, `patients`, and optionally `bookings`

**Note**: Reviews notify doctors and are publicly visible.

---

### 9. **feedbacks** (2025_12_01_180400_create_feedbacks_table.php)

**Purpose**: Private session feedback (separate from public reviews)

**Key Fields**:

-   `patient_id`: Foreign key to patients table
-   `booking_id`: Foreign key to bookings table
-   `doctor_id`: Foreign key to doctors table
-   `feedback_text`: Feedback content
-   `rating`: Session quality rating (1-5)

**Relationships**:

-   Belongs to `patients`, `doctors`, and `bookings`

**Note**: Feedbacks are private and submitted after sessions.

---

## Communication

### 10. **conversations** (2025_12_01_175528_create_conversations_table.php)

**Purpose**: Chat threads between patients and doctors

**Key Fields**:

-   `patient_id`: Foreign key to patients table
-   `doctor_id`: Foreign key to doctors table
-   `last_message_at`: Timestamp of last message
-   `is_archived_by_patient`, `is_archived_by_doctor`: Archive status
-   `is_favorited_by_patient`, `is_favorited_by_doctor`: Favorite status

**Unique Constraint**: `[patient_id, doctor_id]` - one conversation per patient-doctor pair

**Relationships**:

-   Belongs to `patients` and `doctors`
-   One-to-Many with `messages` and `conversation_participants`

---

### 11. **conversation_participants** (2025_12_01_175544_create_conversation_participants_table.php)

**Purpose**: Track unread message counts for conversation participants

**Key Fields**:

-   `conversation_id`: Foreign key to conversations table
-   `user_id`: Foreign key to users table
-   `unread_count`: Number of unread messages
-   `last_read_at`: Last read timestamp

**Unique Constraint**: `[conversation_id, user_id]`

**Relationships**:

-   Belongs to `conversations` and `users`

---

### 12. **messages** (2025_12_01_175556_create_messages_table.php)

**Purpose**: Real-time chat messages between users

**Key Fields**:

-   `conversation_id`: Foreign key to conversations table
-   `sender_id`: Foreign key to users table
-   `receiver_id`: Foreign key to users table
-   `content`: Text message content
-   `type`: Enum (text, image, video)
-   `attachment_url`: URL for image/video attachments
-   `is_read`: Read status
-   `read_at`: Read timestamp

**Relationships**:

-   Belongs to `conversations`, `sender (users)`, and `receiver (users)`

**Note**: Supports text, image, and video messages via WebSocket.

---

### 13. **notifications** (2025_12_01_180200_create_notifications_table.php)

**Purpose**: In-app notifications for users

**Key Fields**:

-   `user_id`: Foreign key to users table
-   `title`: Notification title
-   `message`: Notification content
-   `type`: Enum (booking_created, booking_confirmed, booking_cancelled, booking_rescheduled, booking_reminder, new_review, new_message, payment_received, payment_failed, system_announcement)
-   `data`: JSON data (e.g., booking_id, doctor_id)
-   `is_read`: Read status
-   `read_at`: Read timestamp

**Relationships**:

-   Belongs to `users`

**Note**: Delivered via FCM/APNS with 95% success rate requirement.

---

### 14. **announcements** (2025_12_01_180800_create_announcements_table.php)

**Purpose**: System-wide announcements from admin

**Key Fields**:

-   `title`: Announcement title
-   `message`: Announcement content
-   `target_audience`: Enum (all, patients, doctors)
-   `is_active`: Active status
-   `published_at`: Publication timestamp
-   `expires_at`: Expiration timestamp
-   `created_by`: Foreign key to users table (admin)

**Relationships**:

-   Belongs to `users` (admin)

---

## Payment System

### 15. **transactions** (2025_12_01_175213_create_transactions_table.php)

**Purpose**: Payment transaction records

**Key Fields**:

-   `booking_id`: Foreign key to bookings table
-   `external_id`: PayPal/Stripe transaction ID
-   `amount`: Transaction amount
-   `type`: Enum (payment, refund)
-   `status`: Enum (success, failed, pending)
-   `gateway`: Enum (stripe, paypal, cash)
-   `payload`: JSON response from payment gateway
-   `currency`: Currency code (default: USD)
-   `failure_reason`: Reason if payment failed

**Relationships**:

-   Belongs to `bookings`

---

### 16. **payment_methods** (2025_12_01_180300_create_payment_methods_table.php)

**Purpose**: Saved payment methods for patients

**Key Fields**:

-   `patient_id`: Foreign key to patients table
-   `type`: Enum (paypal, stripe, card)
-   `provider_customer_id`: Stripe customer ID or PayPal email
-   `last_four`: Last 4 digits of card
-   `card_brand`: Card brand (Visa, Mastercard, etc.)
-   `expiry_month`, `expiry_year`: Card expiration
-   `is_default`: Default payment method flag

**Relationships**:

-   Belongs to `patients`

---

## Content Management

### 17. **favorites** (2025_12_01_180000_create_favorites_table.php)

**Purpose**: Patient's favorite doctors

**Key Fields**:

-   `patient_id`: Foreign key to patients table
-   `doctor_id`: Foreign key to doctors table

**Unique Constraint**: `[patient_id, doctor_id]` - prevents duplicates

**Relationships**:

-   Belongs to `patients` and `doctors`

---

### 18. **search_history** (2025_12_01_180100_create_search_history_table.php)

**Purpose**: Patient's search history

**Key Fields**:

-   `patient_id`: Foreign key to patients table
-   `search_query`: Search term
-   `search_type`: Enum (specialty, name, location)
-   `latitude`, `longitude`: For location-based searches

**Relationships**:

-   Belongs to `patients`

---

### 19. **otp_codes** (2025_12_01_175616_create_otp_codes_table.php)

**Purpose**: OTP verification codes

**Key Fields**:

-   `identifier`: Mobile number or email
-   `code`: 6-digit OTP code
-   `type`: Enum (registration, login, password_reset, verification)
-   `is_verified`: Verification status
-   `expires_at`: Expiration timestamp
-   `verified_at`: Verification timestamp

**Note**: OTPs must be sent within 10 seconds and verified within expiration time.

---

### 20. **faqs** (2025_12_01_180500_create_faqs_table.php)

**Purpose**: Frequently Asked Questions

**Key Fields**:

-   `question`: FAQ question
-   `answer`: FAQ answer
-   `order`: Display order
-   `is_active`: Active status

**Note**: Managed by admin, displayed in patient/doctor apps.

---

### 21. **app_settings** (2025_12_01_180600_create_app_settings_table.php)

**Purpose**: Application-wide settings

**Key Fields**:

-   `key`: Setting key (e.g., 'privacy_policy', 'terms_of_service')
-   `value`: Setting value
-   `type`: Value type (text, json, boolean, number)
-   `description`: Setting description

**Note**: Includes privacy policy, terms of service, app version, etc.

---

### 22. **system_logs** (2025_12_01_175629_create_system_logs_table.php)

**Purpose**: System event logging for admin monitoring

**Key Fields**:

-   `user_id`: Foreign key to users table (optional)
-   `event_type`: Event type (e.g., 'user_created', 'booking_cancelled')
-   `description`: Event description
-   `ip_address`: User's IP address
-   `user_agent`: User's browser/device info
-   `metadata`: JSON additional data
-   `severity`: Enum (info, warning, error, critical)

**Relationships**:

-   Belongs to `users` (optional)

---

## Migration Order

The migrations should be run in the following order to respect foreign key constraints:

1. **users** (0001_01_01_000000)
2. **password_reset_tokens** (included in users migration)
3. **sessions** (included in users migration)
4. **cache** (0001_01_01_000001)
5. **jobs** (0001_01_01_000002)
6. **personal_access_tokens** (2025_11_30_140130)
7. **specialties** (2025_12_01_174425)
8. **doctors** (2025_12_01_174628) - requires users, specialties
9. **patients** (2025_12_01_174957) - requires users
10. **availability_slots** (2025_12_01_175515) - requires doctors
11. **bookings** (2025_12_01_175057) - requires doctors, patients
12. **transactions** (2025_12_01_175213) - requires bookings
13. **reviews** (2025_12_01_175606) - requires doctors, patients, bookings
14. **conversations** (2025_12_01_175528) - requires patients, doctors
15. **conversation_participants** (2025_12_01_175544) - requires conversations, users
16. **messages** (2025_12_01_175556) - requires conversations, users
17. **otp_codes** (2025_12_01_175616)
18. **system_logs** (2025_12_01_175629) - requires users
19. **favorites** (2025_12_01_180000) - requires patients, doctors
20. **search_history** (2025_12_01_180100) - requires patients
21. **notifications** (2025_12_01_180200) - requires users
22. **payment_methods** (2025_12_01_180300) - requires patients
23. **feedbacks** (2025_12_01_180400) - requires patients, bookings, doctors
24. **faqs** (2025_12_01_180500)
25. **app_settings** (2025_12_01_180600)
26. **admin_permissions** (2025_12_01_180700) - requires users
27. **announcements** (2025_12_01_180800) - requires users

---

## Running Migrations

To run all migrations:

```bash
php artisan migrate
```

To rollback all migrations:

```bash
php artisan migrate:rollback
```

To refresh all migrations (drop all tables and re-run):

```bash
php artisan migrate:fresh
```

To refresh and seed:

```bash
php artisan migrate:fresh --seed
```

---

## Database Indexes

The following indexes have been added for performance optimization:

### Users Table

-   `email` (unique)
-   `mobile` (unique)

### Doctors Table

-   `user_id` (foreign key)
-   `specialty_id` (foreign key)
-   `license_number` (unique)
-   `[doctor_id, rating]` (composite)

### Bookings Table

-   `doctor_id` (foreign key)
-   `patient_id` (foreign key)
-   `[doctor_id, appointment_date]` (composite)

### Messages Table

-   `[conversation_id, created_at]` (composite)
-   `[sender_id, receiver_id]` (composite)

### Notifications Table

-   `[user_id, is_read, created_at]` (composite)
-   `[user_id, type]` (composite)

### System Logs Table

-   `[event_type, created_at]` (composite)
-   `[user_id, created_at]` (composite)

---

## Data Validation Rules

### Rating Fields

-   Must be between 1 and 5 (inclusive)
-   Applied to: `reviews.rating`, `feedbacks.rating`

### Payment Amounts

-   Must be positive decimal values
-   Precision: 8 digits, 2 decimal places
-   Applied to: `bookings.price_at_booking`, `transactions.amount`, `doctors.session_price`

### Location Coordinates

-   Latitude: -90 to 90 (10 digits, 8 decimal places)
-   Longitude: -180 to 180 (11 digits, 8 decimal places)

### OTP Codes

-   Must be 6 characters
-   Expires after configured time (e.g., 10 minutes)

---

## Security Considerations

1. **Password Hashing**: All passwords must be hashed using Laravel's bcrypt
2. **Soft Deletes**: Users table uses soft deletes for account deletion tracking
3. **Foreign Key Constraints**: All foreign keys have proper `onDelete` actions
4. **Unique Constraints**: Prevent duplicate data (emails, mobile numbers, favorites, etc.)
5. **Indexes**: Optimize query performance for frequently accessed data
6. **JSON Fields**: Used for flexible metadata storage (permissions, notification data, etc.)

---

## Notes

-   All timestamps are automatically managed by Laravel (`created_at`, `updated_at`)
-   Foreign key constraints ensure referential integrity
-   Enum fields restrict values to predefined options
-   Nullable fields are explicitly marked for optional data
-   Composite indexes improve query performance for common search patterns

---

**Last Updated**: December 1, 2025
**Laravel Version**: 12.x
**Database**: MySQL/PostgreSQL
