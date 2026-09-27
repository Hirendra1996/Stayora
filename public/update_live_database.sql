-- ====================================================================
-- FarmLelo Live Database Schema Update Script
-- Target Database: u272390999_leloleo (or active Farmlelo production DB)
-- Server: MariaDB 10.2+ / 11.x / MySQL 8.x
-- Timezone: Indian Standard Time (IST – UTC+5:30)
-- 
-- PURPOSE:
-- 1. Fix "Farmhouse not found or not yet active." error on farmhouse_details
--    by adding missing `google_map_link` column to `farmhouses`.
-- 2. Add Room-Wise Booking columns (`room_type_id`, `room_type_name`, `price_per_room`)
--    to `booking_requests`.
-- 3. Create `farmhouse_room_types` table and backfill room types for existing farmhouses.
-- 4. Add `is_phone_verified` and `is_email_verified` to `users` and `owners`.
-- 5. Create `otp_verifications` table for SMS/Email verification.
-- 6. Ensure `locked_by` & `owner_id` on `blocked_dates`, and `active_session_id` on `admins`.
-- ====================================================================

-- Step 1: Enforce Indian Standard Time (IST - UTC+5:30) for session
SET time_zone = '+05:30';
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Add `google_map_link` to `farmhouses`
-- (Resolves the 404 / 'Farmhouse not found or not yet active' error)
ALTER TABLE `farmhouses`
  ADD COLUMN IF NOT EXISTS `google_map_link` TEXT DEFAULT NULL AFTER `address`;

-- Step 3: Add Room-Wise Booking & Room Capacity columns to `booking_requests`
ALTER TABLE `booking_requests`
  ADD COLUMN IF NOT EXISTS `booking_type` ENUM('complete', 'per_room') NOT NULL DEFAULT 'complete' AFTER `farmhouse_id`,
  ADD COLUMN IF NOT EXISTS `room_type_id` INT(11) DEFAULT NULL AFTER `booking_type`,
  ADD COLUMN IF NOT EXISTS `room_type_name` VARCHAR(100) DEFAULT NULL AFTER `room_type_id`,
  ADD COLUMN IF NOT EXISTS `rooms` INT(11) NOT NULL DEFAULT 1 AFTER `guests`,
  ADD COLUMN IF NOT EXISTS `price_per_room` DECIMAL(10,2) DEFAULT NULL AFTER `rooms`;

-- Step 4: Create `farmhouse_room_types` Table
CREATE TABLE IF NOT EXISTS `farmhouse_room_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `farmhouse_id` INT(11) NOT NULL,
  `room_type_name` VARCHAR(100) NOT NULL COMMENT 'e.g. Standard Room, Deluxe Room, Suite',
  `total_rooms` INT(11) NOT NULL DEFAULT 1 COMMENT 'Number of available rooms of this type',
  `price_per_room` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Nightly rate per room',
  `capacity_per_room` INT(11) NOT NULL DEFAULT 2 COMMENT 'Max guests per room',
  `description` VARCHAR(255) DEFAULT NULL COMMENT 'Optional notes/features (e.g. AC, King Bed, Attached Bath)',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_farmhouse_id` (`farmhouse_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_room_types_farmhouse` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 5: Backfill default room types for existing farmhouses with room booking enabled
INSERT INTO `farmhouse_room_types` (`farmhouse_id`, `room_type_name`, `total_rooms`, `price_per_room`, `capacity_per_room`, `description`, `status`)
SELECT 
    f.id,
    'Standard Room',
    GREATEST(COALESCE(f.bedrooms, 1), 1),
    COALESCE(f.room_price, 0.00),
    GREATEST(COALESCE(f.bedroom_capacity, 2), 1),
    'Standard comfortable bedroom with essential amenities',
    'active'
FROM `farmhouses` f
WHERE (f.allow_room_booking = 1 OR (f.room_price IS NOT NULL AND f.room_price > 0))
  AND NOT EXISTS (
      SELECT 1 FROM `farmhouse_room_types` rt WHERE rt.farmhouse_id = f.id
  );

-- Step 6: Add phone & email verification flags to `users` and `owners`
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `is_phone_verified` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = phone verified, 0 = unverified',
  ADD COLUMN IF NOT EXISTS `is_email_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = email verified, 0 = unverified';

ALTER TABLE `owners`
  ADD COLUMN IF NOT EXISTS `is_phone_verified` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = phone verified, 0 = unverified',
  ADD COLUMN IF NOT EXISTS `is_email_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = email verified, 0 = unverified';

-- Step 7: Create `otp_verifications` Table
CREATE TABLE IF NOT EXISTS `otp_verifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(100) NOT NULL COMMENT 'Mobile (+91XXXXXXXXXX or 10 digits) or email address',
  `otp_code` VARCHAR(255) NOT NULL COMMENT '6-digit secure OTP code',
  `purpose` ENUM('registration', 'phone_change', 'forgot_password_sms', 'forgot_password_email') NOT NULL COMMENT 'Context of OTP',
  `user_id` INT(11) DEFAULT NULL COMMENT 'User ID if authenticated or known',
  `role` ENUM('users', 'owners', 'admins') NOT NULL DEFAULT 'users' COMMENT 'User role',
  `meta_data` LONGTEXT DEFAULT NULL COMMENT 'JSON-encoded context',
  `attempts` INT(11) NOT NULL DEFAULT 0 COMMENT 'Verification attempts count',
  `max_attempts` INT(11) NOT NULL DEFAULT 5 COMMENT 'Max allowed attempts',
  `expires_at` DATETIME NOT NULL COMMENT 'Expiration timestamp (IST)',
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = pending, 1 = verified',
  `verified_at` DATETIME DEFAULT NULL COMMENT 'Verified timestamp (IST)',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Client IP address',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation timestamp (IST)',
  PRIMARY KEY (`id`),
  KEY `idx_identifier_purpose` (`identifier`, `purpose`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Secure OTP verification records';

-- Step 8: Ensure `blocked_dates` has `locked_by` and `owner_id`
ALTER TABLE `blocked_dates`
  ADD COLUMN IF NOT EXISTS `locked_by` ENUM('admin','owner') NOT NULL DEFAULT 'admin' COMMENT 'Who initiated the lock',
  ADD COLUMN IF NOT EXISTS `owner_id` INT(11) DEFAULT NULL COMMENT 'Owner ID if locked by owner';

-- Step 9: Ensure `admins` has `active_session_id` for single device login
ALTER TABLE `admins`
  ADD COLUMN IF NOT EXISTS `active_session_id` VARCHAR(255) DEFAULT NULL AFTER `is_logged_in`;

SET FOREIGN_KEY_CHECKS = 1;

-- Step 10: Verification Query to confirm all updates applied
SELECT 
  'SUCCESS' AS `migration_status`,
  NOW() AS `current_time_ist`,
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE table_schema = DATABASE() AND table_name = 'farmhouses' AND column_name = 'google_map_link') AS `farmhouses_google_map_link_exists`,
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE table_schema = DATABASE() AND table_name = 'booking_requests' AND column_name = 'room_type_id') AS `booking_requests_room_type_id_exists`,
  (SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = DATABASE() AND table_name = 'farmhouse_room_types') AS `farmhouse_room_types_table_exists`,
  (SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = DATABASE() AND table_name = 'otp_verifications') AS `otp_verifications_table_exists`;
