# 🚀 Migration Deployment Checklist

## Pre-Migration Checklist

### 1. Environment Setup ✅

-   [ ] Database connection configured in `.env`
-   [ ] Database credentials verified
-   [ ] Database exists and is accessible
-   [ ] PHP version compatible (8.1+)
-   [ ] Laravel version verified (12.x)

### 2. Backup (Production Only) ⚠️

-   [ ] Database backup created
-   [ ] Backup verified and stored safely
-   [ ] Rollback plan documented

### 3. Code Review ✅

-   [x] All migration files reviewed
-   [x] Foreign key relationships verified
-   [x] Unique constraints checked
-   [x] Indexes optimized
-   [x] Documentation complete

---

## Migration Execution

### Step 1: Check Current Status

```bash
php artisan migrate:status
```

**Expected Output**: List of pending migrations

### Step 2: Run Migrations (Development)

```bash
php artisan migrate
```

**Expected Output**:

```
Migrating: 2025_12_01_174425_create_specialties_table
Migrated:  2025_12_01_174425_create_specialties_table (XX.XXms)
...
```

### Step 3: Verify Migration Success

```bash
php artisan migrate:status
```

**Expected Output**: All migrations should show "Ran"

### Step 4: Verify Database Structure

```bash
php artisan db:show
php artisan db:table users
php artisan db:table doctors
php artisan db:table patients
```

---

## Post-Migration Checklist

### 1. Database Verification ✅

-   [ ] All 27 tables created
-   [ ] Foreign keys properly set
-   [ ] Indexes created
-   [ ] Unique constraints active

### 2. Test Data (Development Only)

```bash
# Create seeders first
php artisan make:seeder SpecialtySeeder
php artisan make:seeder UserSeeder

# Run seeders
php artisan db:seed
```

### 3. Application Testing

-   [ ] User registration works
-   [ ] Login functionality works
-   [ ] Database connections stable
-   [ ] No foreign key errors

---

## Migration Commands Reference

### Development Environment

#### Fresh Migration (Drops all tables)

```bash
php artisan migrate:fresh
```

#### Fresh Migration with Seeders

```bash
php artisan migrate:fresh --seed
```

#### Rollback Last Batch

```bash
php artisan migrate:rollback
```

#### Rollback All Migrations

```bash
php artisan migrate:reset
```

#### Refresh (Rollback + Migrate)

```bash
php artisan migrate:refresh
```

### Production Environment

#### Run Pending Migrations

```bash
php artisan migrate --force
```

#### Pretend Mode (See SQL without executing)

```bash
php artisan migrate --pretend
```

#### Step Mode (One migration at a time)

```bash
php artisan migrate --step
```

---

## Troubleshooting

### Issue: "SQLSTATE[HY000] [1045] Access denied"

**Solution**: Check database credentials in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Issue: "SQLSTATE[42S01]: Base table or view already exists"

**Solution**: Table already exists

```bash
# Development: Drop and recreate
php artisan migrate:fresh

# Production: Check migration status
php artisan migrate:status
```

### Issue: "SQLSTATE[HY000]: General error: 1005"

**Solution**: Foreign key constraint error

-   Ensure parent tables exist
-   Check column types match
-   Verify migration order

### Issue: "Class 'Database\Seeders\...' not found"

**Solution**: Create missing seeder

```bash
php artisan make:seeder SpecialtySeeder
```

---

## Rollback Plan (Production)

### If Migration Fails:

1. **Stop Application**

    ```bash
    php artisan down
    ```

2. **Rollback Migration**

    ```bash
    php artisan migrate:rollback --step=1
    ```

3. **Restore Backup** (if needed)

    ```bash
    mysql -u username -p database_name < backup.sql
    ```

4. **Verify Database**

    ```bash
    php artisan migrate:status
    ```

5. **Restart Application**
    ```bash
    php artisan up
    ```

---

## Next Steps After Migration

### 1. Create Models

```bash
php artisan make:model Patient
php artisan make:model Doctor
php artisan make:model Booking
php artisan make:model Review
php artisan make:model Message
php artisan make:model Conversation
php artisan make:model Notification
php artisan make:model Transaction
php artisan make:model Specialty
php artisan make:model AvailabilitySlot
php artisan make:model Favorite
php artisan make:model SearchHistory
php artisan make:model PaymentMethod
php artisan make:model Feedback
php artisan make:model FAQ
php artisan make:model AppSetting
php artisan make:model AdminPermission
php artisan make:model Announcement
php artisan make:model OtpCode
php artisan make:model SystemLog
```

### 2. Create Seeders

```bash
php artisan make:seeder SpecialtySeeder
php artisan make:seeder UserSeeder
php artisan make:seeder FAQSeeder
php artisan make:seeder AppSettingSeeder
```

### 3. Create Factories

```bash
php artisan make:factory PatientFactory
php artisan make:factory DoctorFactory
php artisan make:factory BookingFactory
```

### 4. Create Controllers

```bash
php artisan make:controller Api/AuthController
php artisan make:controller Api/PatientController --api
php artisan make:controller Api/DoctorController --api
php artisan make:controller Api/BookingController --api
php artisan make:controller Api/ReviewController --api
php artisan make:controller Api/MessageController --api
php artisan make:controller Api/NotificationController --api
```

### 5. Create Form Requests

```bash
php artisan make:request Auth/RegisterRequest
php artisan make:request Auth/LoginRequest
php artisan make:request Booking/StoreBookingRequest
php artisan make:request Booking/UpdateBookingRequest
php artisan make:request Review/StoreReviewRequest
```

### 6. Create API Resources

```bash
php artisan make:resource PatientResource
php artisan make:resource DoctorResource
php artisan make:resource BookingResource
php artisan make:resource ReviewResource
php artisan make:resource MessageResource
```

---

## Verification Queries

### Check Table Count

```sql
SELECT COUNT(*) as table_count
FROM information_schema.tables
WHERE table_schema = 'your_database_name';
```

**Expected**: 27 tables

### Check Foreign Keys

```sql
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'your_database_name'
ORDER BY TABLE_NAME;
```

### Check Indexes

```sql
SELECT
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as COLUMNS
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'your_database_name'
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME;
```

---

## Success Criteria

### ✅ Migration Successful If:

-   [ ] All 25 migration files executed without errors
-   [ ] 27 tables created in database
-   [ ] All foreign keys properly set
-   [ ] All indexes created
-   [ ] `php artisan migrate:status` shows all migrations as "Ran"
-   [ ] No error messages in console
-   [ ] Application can connect to database
-   [ ] Basic CRUD operations work

---

## Support & Documentation

### Documentation Files

-   `README.md` - Overview and quick start
-   `MIGRATIONS_DOCUMENTATION.md` - Detailed table documentation
-   `DATABASE_SCHEMA.md` - ER diagram
-   `MIGRATION_GUIDE.md` - Quick reference
-   `IMPLEMENTATION_SUMMARY.md` - Implementation details

### Useful Commands

```bash
# Show database info
php artisan db:show

# Show table structure
php artisan db:table users

# Show migration status
php artisan migrate:status

# Tinker (test database)
php artisan tinker
```

---

## Final Checklist

-   [ ] All migrations executed successfully
-   [ ] Database structure verified
-   [ ] Foreign keys working
-   [ ] Indexes created
-   [ ] Models created
-   [ ] Seeders created (optional)
-   [ ] Controllers created
-   [ ] Routes defined
-   [ ] API tested
-   [ ] Documentation reviewed

---

**Status**: Ready for Migration ✅  
**Date**: December 1, 2025  
**Project**: Doctor Appointment Mobile App  
**Database**: MySQL/PostgreSQL  
**Laravel**: 12.x
