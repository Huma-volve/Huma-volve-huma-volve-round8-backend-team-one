# Migration Quick Reference Guide

## Quick Commands

### Run All Migrations

```bash
php artisan migrate
```

### Rollback Last Migration Batch

```bash
php artisan migrate:rollback
```

### Rollback All Migrations

```bash
php artisan migrate:reset
```

### Refresh All Migrations (Drop & Re-run)

```bash
php artisan migrate:fresh
```

### Refresh with Seeding

```bash
php artisan migrate:fresh --seed
```

### Check Migration Status

```bash
php artisan migrate:status
```

---

## Migration Files Summary

| #   | File Name                                                      | Table Name                             | Purpose                  |
| --- | -------------------------------------------------------------- | -------------------------------------- | ------------------------ |
| 1   | `0001_01_01_000000_create_users_table.php`                     | users, password_reset_tokens, sessions | Core user authentication |
| 2   | `0001_01_01_000001_create_cache_table.php`                     | cache, cache_locks                     | Application caching      |
| 3   | `0001_01_01_000002_create_jobs_table.php`                      | jobs, job_batches, failed_jobs         | Queue management         |
| 4   | `2025_11_30_140130_create_personal_access_tokens_table.php`    | personal_access_tokens                 | API authentication       |
| 5   | `2025_12_01_174425_create_specialties_table.php`               | specialties                            | Medical specialties      |
| 6   | `2025_12_01_174628_create_doctors_table.php`                   | doctors                                | Doctor profiles          |
| 7   | `2025_12_01_174957_create_patients_table.php`                  | patients                               | Patient profiles         |
| 8   | `2025_12_01_175057_create_bookings_table.php`                  | bookings                               | Appointments             |
| 9   | `2025_12_01_175213_create_transactions_table.php`              | transactions                           | Payment records          |
| 10  | `2025_12_01_175515_create_availability_slots_table.php`        | availability_slots                     | Doctor schedules         |
| 11  | `2025_12_01_175528_create_conversations_table.php`             | conversations                          | Chat threads             |
| 12  | `2025_12_01_175544_create_conversation_participants_table.php` | conversation_participants              | Chat participants        |
| 13  | `2025_12_01_175556_create_messages_table.php`                  | messages                               | Chat messages            |
| 14  | `2025_12_01_175606_create_reviews_table.php`                   | reviews                                | Doctor reviews           |
| 15  | `2025_12_01_175616_create_otp_codes_table.php`                 | otp_codes                              | OTP verification         |
| 16  | `2025_12_01_175629_create_system_logs_table.php`               | system_logs                            | Admin logs               |
| 17  | `2025_12_01_180000_create_favorites_table.php`                 | favorites                              | Favorite doctors         |
| 18  | `2025_12_01_180100_create_search_history_table.php`            | search_history                         | Search tracking          |
| 19  | `2025_12_01_180200_create_notifications_table.php`             | notifications                          | In-app notifications     |
| 20  | `2025_12_01_180300_create_payment_methods_table.php`           | payment_methods                        | Saved payments           |
| 21  | `2025_12_01_180400_create_feedbacks_table.php`                 | feedbacks                              | Session feedback         |
| 22  | `2025_12_01_180500_create_faqs_table.php`                      | faqs                                   | FAQ content              |
| 23  | `2025_12_01_180600_create_app_settings_table.php`              | app_settings                           | App configuration        |
| 24  | `2025_12_01_180700_create_admin_permissions_table.php`         | admin_permissions                      | RBAC permissions         |
| 25  | `2025_12_01_180800_create_announcements_table.php`             | announcements                          | System announcements     |

**Total**: 25 migration files creating **27 tables**

---

## Table Dependencies

### No Dependencies (Can be created first)

-   `users`
-   `specialties`
-   `faqs`
-   `app_settings`
-   `otp_codes`

### Depends on Users

-   `patients` (users)
-   `doctors` (users, specialties)
-   `admin_permissions` (users)
-   `notifications` (users)
-   `system_logs` (users)
-   `announcements` (users)

### Depends on Patients/Doctors

-   `bookings` (doctors, patients)
-   `favorites` (patients, doctors)
-   `search_history` (patients)
-   `payment_methods` (patients)
-   `availability_slots` (doctors)
-   `conversations` (patients, doctors)

### Depends on Bookings

-   `transactions` (bookings)
-   `reviews` (doctors, patients, bookings)
-   `feedbacks` (patients, bookings, doctors)

### Depends on Conversations

-   `messages` (conversations, users)
-   `conversation_participants` (conversations, users)

---

## Common Enum Values

### User Roles

```php
['patient', 'doctor', 'admin', 'helper']
```

### Booking Status

```php
['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled']
```

### Payment Methods

```php
['paypal', 'stripe', 'cash']
```

### Payment Status

```php
['unpaid', 'paid', 'failed', 'refunded']
```

### Message Types

```php
['text', 'image', 'video']
```

### Notification Types

```php
[
    'booking_created',
    'booking_confirmed',
    'booking_cancelled',
    'booking_rescheduled',
    'booking_reminder',
    'new_review',
    'new_message',
    'payment_received',
    'payment_failed',
    'system_announcement'
]
```

### OTP Types

```php
['registration', 'login', 'password_reset', 'verification']
```

### Transaction Types

```php
['payment', 'refund']
```

### Transaction Status

```php
['success', 'failed', 'pending']
```

### Payment Gateways

```php
['stripe', 'paypal', 'cash']
```

### Search Types

```php
['specialty', 'name', 'location']
```

### Announcement Audiences

```php
['all', 'patients', 'doctors']
```

### Log Severity

```php
['info', 'warning', 'error', 'critical']
```

---

## Important Constraints

### Unique Constraints

```php
// Users
users.email
users.mobile

// Doctors
doctors.license_number

// Prevent Duplicates
favorites(patient_id, doctor_id)
conversations(patient_id, doctor_id)
conversation_participants(conversation_id, user_id)
availability_slots(doctor_id, date, start_time)

// Settings
app_settings.key
```

### Foreign Key Actions

#### ON DELETE CASCADE

```php
// When parent is deleted, children are deleted
users → patients
users → doctors
doctors → bookings
patients → bookings
bookings → transactions
conversations → messages
```

#### ON DELETE RESTRICT

```php
// Cannot delete parent if children exist
specialties → doctors (must reassign doctors first)
```

#### ON DELETE SET NULL

```php
// When parent is deleted, foreign key becomes null
bookings → reviews (booking_id becomes null)
users → system_logs (user_id becomes null)
```

---

## Field Types Reference

### Common Field Types

```php
// IDs
$table->id();                          // Auto-increment primary key
$table->foreignId('user_id');          // Foreign key

// Strings
$table->string('name');                // VARCHAR(255)
$table->string('code', 6);             // VARCHAR(6)
$table->text('description');           // TEXT

// Numbers
$table->integer('count');              // INTEGER
$table->decimal('price', 8, 2);        // DECIMAL(8,2)
$table->decimal('latitude', 10, 8);    // DECIMAL(10,8)

// Booleans
$table->boolean('is_active');          // BOOLEAN

// Dates/Times
$table->date('birthdate');             // DATE
$table->time('start_time');            // TIME
$table->timestamp('created_at');       // TIMESTAMP
$table->timestamps();                  // created_at, updated_at

// Special
$table->enum('role', ['patient', 'doctor']); // ENUM
$table->json('metadata');              // JSON
$table->softDeletes();                 // deleted_at
```

---

## Indexing Strategy

### When to Add Indexes

✅ **Add indexes for:**

-   Foreign keys (automatically indexed)
-   Frequently searched columns
-   Columns used in WHERE clauses
-   Columns used in ORDER BY
-   Composite columns used together in queries

❌ **Avoid indexes for:**

-   Small tables (< 1000 rows)
-   Columns with low cardinality (few unique values)
-   Frequently updated columns
-   Large text/blob columns

### Index Examples

```php
// Single column index
$table->index('email');

// Composite index
$table->index(['user_id', 'created_at']);

// Unique index
$table->unique('email');

// Unique composite index
$table->unique(['patient_id', 'doctor_id']);
```

---

## Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1005"

**Cause**: Foreign key constraint error
**Solution**: Ensure parent table exists and column types match

### Error: "SQLSTATE[23000]: Integrity constraint violation"

**Cause**: Duplicate entry for unique field
**Solution**: Check for existing data before inserting

### Error: "SQLSTATE[42S01]: Base table or view already exists"

**Cause**: Table already exists
**Solution**: Run `php artisan migrate:fresh` or drop table manually

### Error: "Class 'Database\Seeders\...' not found"

**Cause**: Seeder class doesn't exist
**Solution**: Create seeder with `php artisan make:seeder NameSeeder`

---

## Best Practices

### ✅ DO

-   Always use foreign keys for relationships
-   Add indexes for frequently queried columns
-   Use enum for fixed value sets
-   Use soft deletes for user data
-   Add timestamps to all tables
-   Use descriptive column names
-   Document complex relationships

### ❌ DON'T

-   Store sensitive data unencrypted
-   Use varchar for boolean values
-   Create circular dependencies
-   Forget to add indexes on foreign keys
-   Use generic column names (data, value, etc.)
-   Skip migration testing

---

## Testing Migrations

### Test Migration Up

```bash
php artisan migrate --pretend
```

### Test Migration Down

```bash
php artisan migrate:rollback --pretend
```

### Test Fresh Migration

```bash
php artisan migrate:fresh --seed
```

### Verify Database Structure

```bash
php artisan db:show
php artisan db:table users
```

---

## Next Steps After Migration

1. **Create Model Classes**

    ```bash
    php artisan make:model Patient
    php artisan make:model Doctor
    # etc.
    ```

2. **Define Model Relationships**

    - hasMany, belongsTo, belongsToMany
    - Define in model classes

3. **Create Seeders**

    ```bash
    php artisan make:seeder SpecialtySeeder
    php artisan make:seeder UserSeeder
    ```

4. **Create Factories**

    ```bash
    php artisan make:factory PatientFactory
    php artisan make:factory DoctorFactory
    ```

5. **Create Controllers**

    ```bash
    php artisan make:controller Api/PatientController --api
    php artisan make:controller Api/DoctorController --api
    ```

6. **Define API Routes**

    - Add routes in `routes/api.php`

7. **Create Form Requests**
    ```bash
    php artisan make:request StoreBookingRequest
    php artisan make:request UpdateBookingRequest
    ```

---

**Last Updated**: December 1, 2025
**Laravel Version**: 12.x
