<?php
/**
 * database/migrate_m3_m5.php
 * Database migration and seeder for Modules M3 (Room/Unit Management) & M5 (Pricing Engine)
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/app/Config/Database.php';

$db = \App\Config\Database::connect();
echo "==> Starting Migration for M3 (Room/Unit) & M5 (Pricing Engine)...\n";

// Helper to execute SQL query
function runQuery($db, $sql, $label) {
    if ($db->query($sql)) {
        echo " [OK] $label\n";
    } else {
        echo " [ERR] $label: " . $db->error . "\n";
    }
}

// ── 1. M3: Images room_type_id ──
runQuery($db, "ALTER TABLE images ADD COLUMN IF NOT EXISTS room_type_id INT NULL DEFAULT NULL AFTER farmhouse_id", "Add room_type_id to images");

// ── 2. M3: farmhouse_amenities room_type_id ──
runQuery($db, "ALTER TABLE farmhouse_amenities ADD COLUMN IF NOT EXISTS room_type_id INT NULL DEFAULT NULL AFTER amenity_id", "Add room_type_id to farmhouse_amenities");

// ── 3. M3: farmhouse_room_types weekend_price & image_url ──
runQuery($db, "ALTER TABLE farmhouse_room_types ADD COLUMN IF NOT EXISTS weekend_price DECIMAL(10,2) NULL DEFAULT NULL AFTER price_per_room", "Add weekend_price to farmhouse_room_types");
runQuery($db, "ALTER TABLE farmhouse_room_types ADD COLUMN IF NOT EXISTS image_url TEXT NULL DEFAULT NULL AFTER description", "Add image_url to farmhouse_room_types");

// ── 4. M5: farmhouses pricing fields ──
runQuery($db, "ALTER TABLE farmhouses ADD COLUMN IF NOT EXISTS weekend_price DECIMAL(10,2) NULL DEFAULT NULL AFTER price", "Add weekend_price to farmhouses");
runQuery($db, "ALTER TABLE farmhouses ADD COLUMN IF NOT EXISTS security_deposit DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER weekend_price", "Add security_deposit to farmhouses");
runQuery($db, "ALTER TABLE farmhouses ADD COLUMN IF NOT EXISTS cleaning_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER security_deposit", "Add cleaning_fee to farmhouses");
runQuery($db, "ALTER TABLE farmhouses ADD COLUMN IF NOT EXISTS weekly_discount_percent DECIMAL(5,2) NOT NULL DEFAULT 10.00 AFTER cleaning_fee", "Add weekly_discount_percent to farmhouses");
runQuery($db, "ALTER TABLE farmhouses ADD COLUMN IF NOT EXISTS monthly_discount_percent DECIMAL(5,2) NOT NULL DEFAULT 20.00 AFTER weekly_discount_percent", "Add monthly_discount_percent to farmhouses");

// Populate default weekend_price and security deposit for existing properties
$db->query("UPDATE farmhouses SET weekend_price = ROUND(price * 1.2, 0) WHERE weekend_price IS NULL OR weekend_price = 0");
$db->query("UPDATE farmhouses SET security_deposit = 2500.00 WHERE security_deposit = 0");
$db->query("UPDATE farmhouses SET cleaning_fee = 500.00 WHERE cleaning_fee = 0");
echo " [OK] Backfilled farmhouses weekend_price, security_deposit & cleaning_fee\n";

// ── 5. M5: Seasonal Prices Table ──
$sqlSeasonal = "CREATE TABLE IF NOT EXISTS seasonal_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmhouse_id INT NOT NULL,
    season_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    multiplier DECIMAL(4,2) DEFAULT 1.25,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_farm_dates (farmhouse_id, start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
runQuery($db, $sqlSeasonal, "Create seasonal_prices table");

// ── 6. M5: Property Addons Table ──
$sqlAddons = "CREATE TABLE IF NOT EXISTS property_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmhouse_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    price_type ENUM('per_stay','per_night','per_guest') NOT NULL DEFAULT 'per_stay',
    icon_class VARCHAR(50) DEFAULT 'add_circle',
    description VARCHAR(255) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_farm_addon (farmhouse_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
runQuery($db, $sqlAddons, "Create property_addons table");

// ── 7. M5: Coupons Table ──
$sqlCoupons = "CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    discount_type ENUM('percentage','flat') NOT NULL DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    min_booking_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    max_discount_amount DECIMAL(10,2) NULL DEFAULT NULL,
    valid_from DATE NULL,
    valid_until DATE NULL,
    usage_limit INT DEFAULT 1000,
    times_used INT DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
runQuery($db, $sqlCoupons, "Create coupons table");

// Seed standard coupons
$coupons = [
    ['WELCOME10', 'Welcome offer: 10% discount on first stay', 'percentage', 10.00, 2000.00, 1500.00, '2026-01-01', '2027-12-31'],
    ['STAYORA500', 'Flat ₹500 discount for verified stays', 'flat', 500.00, 2500.00, 500.00, '2026-01-01', '2027-12-31'],
    ['LONGSTAY15', 'Special 15% discount for 7+ day stays', 'percentage', 15.00, 8000.00, 4000.00, '2026-01-01', '2027-12-31'],
    ['FESTIVE2026', 'Festive season savings of 12%', 'percentage', 12.00, 5000.00, 2500.00, '2026-01-01', '2027-12-31']
];

foreach ($coupons as $c) {
    $codeEsc = $db->real_escape_string($c[0]);
    $chk = $db->query("SELECT id FROM coupons WHERE code = '{$codeEsc}'");
    if ($chk && $chk->num_rows === 0) {
        $descEsc = $db->real_escape_string($c[1]);
        $typeEsc = $c[2];
        $val = $c[3];
        $min = $c[4];
        $max = $c[5];
        $from = $c[6];
        $to = $c[7];
        $db->query("INSERT INTO coupons (code, description, discount_type, discount_value, min_booking_amount, max_discount_amount, valid_from, valid_until, status) 
                    VALUES ('{$codeEsc}', '{$descEsc}', '{$typeEsc}', {$val}, {$min}, {$max}, '{$from}', '{$to}', 'active')");
    }
}
echo " [OK] Seeded promotional coupons\n";

// ── 8. M5: booking_requests pricing breakdown columns ──
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS weekend_nights INT NOT NULL DEFAULT 0 AFTER price", "Add weekend_nights to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS security_deposit DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER weekend_nights", "Add security_deposit to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS cleaning_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER security_deposit", "Add cleaning_fee to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS addon_charges DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER cleaning_fee", "Add addon_charges to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS addons_selected TEXT NULL DEFAULT NULL AFTER addon_charges", "Add addons_selected to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER addons_selected", "Add discount_amount to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(50) NULL DEFAULT NULL AFTER discount_amount", "Add coupon_code to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS platform_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER coupon_code", "Add platform_fee to booking_requests");
runQuery($db, "ALTER TABLE booking_requests ADD COLUMN IF NOT EXISTS owner_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER platform_fee", "Add owner_amount to booking_requests");

// Seed standard addons for all active farmhouses if none exist
$fhRes = $db->query("SELECT id FROM farmhouses WHERE status = 'active' OR admin_approval_status = 'approved'");
while ($fh = $fhRes->fetch_assoc()) {
    $fId = (int)$fh['id'];
    $cnt = $db->query("SELECT COUNT(*) AS c FROM property_addons WHERE farmhouse_id = {$fId}")->fetch_assoc()['c'];
    if ((int)$cnt === 0) {
        $addons = [
            ['Bonfire & Music Setup', 1200.00, 'per_stay', 'fireplace', 'Evening bonfire setup with wood logs and portable party speaker'],
            ['Barbecue Grill Kit', 800.00, 'per_stay', 'outdoor_grill', 'Charcoal grill equipment with skewers & starter pack (veg/non-veg)'],
            ['Extra Mattress & Linen', 500.00, 'per_night', 'bed', 'Comfortable single floor mattress with fresh duvet and pillow'],
            ['Private Cook / Chef on Demand', 1500.00, 'per_stay', 'restaurant', 'Dedicated on-site cook service for breakfast, lunch & dinner']
        ];
        foreach ($addons as $ad) {
            $nameEsc = $db->real_escape_string($ad[0]);
            $descEsc = $db->real_escape_string($ad[4]);
            $db->query("INSERT INTO property_addons (farmhouse_id, name, price, price_type, icon_class, description, status) 
                        VALUES ({$fId}, '{$nameEsc}', {$ad[1]}, '{$ad[2]}', '{$ad[3]}', '{$descEsc}', 'active')");
        }
    }
}
echo " [OK] Seeded property addons for active farmhouses\n";

echo "==> M3 & M5 Migration completed successfully!\n";
