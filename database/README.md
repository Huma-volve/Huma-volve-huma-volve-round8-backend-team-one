# Database Migrations - Doctor Appointment Mobile App

## Overview

This directory contains all database migration files for the Doctor Appointment Mobile App, implementing the complete database schema as specified in the Software Requirements Specification (SRS).

## 📁 Documentation Files

-   **[MIGRATIONS_DOCUMENTATION.md](MIGRATIONS_DOCUMENTATION.md)** - Comprehensive documentation of all tables, fields, relationships, and migration order
-   **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Visual entity-relationship diagram and schema overview
-   **[MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)** - Quick reference guide with commands, best practices, and troubleshooting

## 📊 Database Statistics

-   **Total Migration Files**: 25
-   **Total Tables Created**: 27
-   **Total Relationships**: 40+
-   **Database Engine**: MySQL/PostgreSQL
-   **Laravel Version**: 12.x

## 🗂️ Table Categories

### Core Tables (5)

-   `users` - Central user authentication
-   `patients` - Patient profiles
-   `doctors` - Doctor profiles
-   `specialties` - Medical specialties
-   `admin_permissions` - RBAC permissions

### Booking System (5)

-   `bookings` - Appointments
-   `availability_slots` - Doctor schedules
-   `reviews` - Doctor reviews
-   `feedbacks` - Session feedback
-   `transactions` - Payment records

### Communication (4)

-   `conversations` - Chat threads
-   `conversation_participants` - Chat participants
-   `messages` - Chat messages
-   `notifications` - In-app notifications

### Payment System (2)

-   `payment_methods` - Saved payment methods
-   `transactions` - Payment records

### Content Management (6)

-   `favorites` - Favorite doctors
-   `search_history` - Search tracking
-   `faqs` - FAQ content
-   `app_settings` - App configuration
-   `announcements` - System announcements
-   `system_logs` - Admin logs

### Authentication (2)

-   `otp_codes` - OTP verification
-   `personal_access_tokens` - API tokens

### System Tables (3)

-   `cache` - Application cache
-   `jobs` - Queue jobs
-   `sessions` - User sessions

## 🚀 Quick Start

### 1. Run All Migrations

```bash
php artisan migrate
```

### 2. Check Migration Status

```bash
php artisan migrate:status
```

### 3. Refresh Database (Development Only)

```bash
php artisan migrate:fresh
```

### 4. Refresh with Seeders

```bash
php artisan migrate:fresh --seed
```

## 📋 Migration Files

| Order | File                                                           | Table                     | Dependencies                |
| ----- | -------------------------------------------------------------- | ------------------------- | --------------------------- |
| 1     | `0001_01_01_000000_create_users_table.php`                     | users                     | None                        |
| 2     | `0001_01_01_000001_create_cache_table.php`                     | cache                     | None                        |
| 3     | `0001_01_01_000002_create_jobs_table.php`                      | jobs                      | None                        |
| 4     | `2025_11_30_140130_create_personal_access_tokens_table.php`    | personal_access_tokens    | None                        |
| 5     | `2025_12_01_174425_create_specialties_table.php`               | specialties               | None                        |
| 6     | `2025_12_01_174628_create_doctors_table.php`                   | doctors                   | users, specialties          |
| 7     | `2025_12_01_174957_create_patients_table.php`                  | patients                  | users                       |
| 8     | `2025_12_01_175057_create_bookings_table.php`                  | bookings                  | doctors, patients           |
| 9     | `2025_12_01_175213_create_transactions_table.php`              | transactions              | bookings                    |
| 10    | `2025_12_01_175515_create_availability_slots_table.php`        | availability_slots        | doctors                     |
| 11    | `2025_12_01_175528_create_conversations_table.php`             | conversations             | patients, doctors           |
| 12    | `2025_12_01_175544_create_conversation_participants_table.php` | conversation_participants | conversations, users        |
| 13    | `2025_12_01_175556_create_messages_table.php`                  | messages                  | conversations, users        |
| 14    | `2025_12_01_175606_create_reviews_table.php`                   | reviews                   | doctors, patients, bookings |
| 15    | `2025_12_01_175616_create_otp_codes_table.php`                 | otp_codes                 | None                        |
| 16    | `2025_12_01_175629_create_system_logs_table.php`               | system_logs               | users                       |
| 17    | `2025_12_01_180000_create_favorites_table.php`                 | favorites                 | patients, doctors           |
| 18    | `2025_12_01_180100_create_search_history_table.php`            | search_history            | patients                    |
| 19    | `2025_12_01_180200_create_notifications_table.php`             | notifications             | users                       |
| 20    | `2025_12_01_180300_create_payment_methods_table.php`           | payment_methods           | patients                    |
| 21    | `2025_12_01_180400_create_feedbacks_table.php`                 | feedbacks                 | patients, bookings, doctors |
| 22    | `2025_12_01_180500_create_faqs_table.php`                      | faqs                      | None                        |
| 23    | `2025_12_01_180600_create_app_settings_table.php`              | app_settings              | None                        |
| 24    | `2025_12_01_180700_create_admin_permissions_table.php`         | admin_permissions         | users                       |
| 25    | `2025_12_01_180800_create_announcements_table.php`             | announcements             | users                       |

## 🔑 Key Features Implemented

### ✅ User Management

-   Multi-role system (Patient, Doctor, Admin, Helper)
-   Google OAuth integration
-   OTP verification for mobile/email
-   Soft deletes for account tracking
-   Profile photo support

### ✅ Doctor Management

-   Admin-created accounts with temporary passwords
-   Specialty categorization
-   License verification
-   Location-based services (latitude/longitude)
-   Rating and review system
-   Availability slot management

### ✅ Booking System

-   Flexible appointment scheduling
-   Multiple payment methods (PayPal, Stripe, Cash)
-   Booking status tracking (pending, confirmed, completed, cancelled, rescheduled)
-   Cancellation tracking with reasons
-   Price locking at booking time

### ✅ Communication

-   Real-time chat (WebSocket ready)
-   Text, image, and video message support
-   Unread message tracking
-   Conversation archiving and favoriting
-   Read receipts

### ✅ Payment Integration

-   PayPal and Stripe support
-   Saved payment methods
-   Transaction logging
-   Refund tracking
-   Payment failure handling

### ✅ Notifications

-   In-app notifications
-   Multiple notification types
-   Read/unread tracking
-   FCM/APNS ready

### ✅ Content Management

-   FAQs
-   App settings (privacy policy, terms)
-   System announcements
-   Search history
-   Favorite doctors

### ✅ Admin Features

-   RBAC permissions
-   System logs with severity levels
-   User management
-   Content management
-   Analytics ready

## 🔒 Security Features

-   Password hashing (bcrypt)
-   Soft deletes for user data
-   Foreign key constraints
-   Unique constraints on sensitive fields
-   OTP expiration tracking
-   Session management
-   API token support

## 📈 Performance Optimizations

-   **40+ Indexes** for frequently queried columns
-   Composite indexes for complex queries
-   Foreign key indexes
-   JSON fields for flexible metadata
-   Efficient cascade rules

## 🔗 Relationships Summary

### One-to-One

-   Users ↔ Patients
-   Users ↔ Doctors

### One-to-Many

-   Users → Notifications
-   Doctors → Bookings
-   Patients → Bookings
-   Bookings → Transactions
-   Conversations → Messages

### Many-to-Many

-   Patients ↔ Doctors (via Favorites)
-   Patients ↔ Doctors (via Bookings)
-   Patients ↔ Doctors (via Conversations)

## ⚠️ Important Notes

1. **Doctor Accounts**: Created by admin only, not self-registered
2. **Temporary Passwords**: Doctors must change password on first login
3. **Soft Deletes**: Users table uses soft deletes
4. **Cascade Rules**: Carefully designed to prevent data loss
5. **Unique Constraints**: Prevent duplicate favorites, conversations, etc.
6. **Rating Validation**: Must be between 1-5 (implement in model/request)
7. **OTP Expiration**: Implement expiration logic in code
8. **Payment Security**: Never store full card numbers

## 🧪 Testing

Before deploying to production:

1. Test all migrations in development
2. Verify foreign key constraints
3. Test cascade delete behavior
4. Validate unique constraints
5. Check index performance
6. Test rollback functionality

```bash
# Test migration
php artisan migrate:fresh --seed

# Verify structure
php artisan db:show
php artisan db:table users
```

## 📝 Next Steps

After running migrations:

1. **Create Models** - Generate Eloquent models for all tables
2. **Define Relationships** - Add relationship methods in models
3. **Create Seeders** - Populate initial data (specialties, FAQs, etc.)
4. **Create Factories** - For testing and development
5. **Create Controllers** - API controllers for each resource
6. **Define Routes** - API routes in `routes/api.php`
7. **Create Requests** - Form validation requests
8. **Create Resources** - API resources for JSON responses

## 🐛 Troubleshooting

### Common Issues

**Foreign Key Constraint Error**

```bash
# Ensure migrations run in correct order
php artisan migrate:fresh
```

**Table Already Exists**

```bash
# Drop all tables and re-run
php artisan migrate:fresh
```

**Seeder Not Found**

```bash
# Create missing seeder
php artisan make:seeder SpecialtySeeder
```

For more troubleshooting tips, see [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md).

## 📚 Additional Resources

-   [Laravel Migration Documentation](https://laravel.com/docs/migrations)
-   [Laravel Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
-   [Database Indexing Best Practices](https://laravel.com/docs/queries#indexes)

## 📞 Support

For questions or issues with migrations:

1. Check the documentation files in this directory
2. Review the SRS document
3. Contact the development team

---

**Created**: December 1, 2025  
**Laravel Version**: 12.x  
**Database**: MySQL/PostgreSQL  
**Status**: ✅ Ready for Migration
