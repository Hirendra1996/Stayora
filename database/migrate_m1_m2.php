<?php
require_once __DIR__ . '/../app/Config/Database.php';

try {
    $db = App\Config\Database::connect();
    echo "=== Migrating Database for M1 (Auth & Users) & M2 (Property Management) ===\n\n";

    // 1. M1: Add google_id and oauth_provider to users table
    echo "1. Checking and updating `users` table for Social Login...\n";
    $db->query("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(100) DEFAULT NULL AFTER `email`");
    $db->query("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `oauth_provider` VARCHAR(50) DEFAULT NULL AFTER `google_id`");
    $db->query("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `avatar_url` TEXT DEFAULT NULL AFTER `profile_image`");
    echo "   -> `users` updated with `google_id`, `oauth_provider`, `avatar_url`.\n";

    // 2. M2: Create `property_types` table
    echo "2. Creating `property_types` table...\n";
    $createTypesSql = "CREATE TABLE IF NOT EXISTS `property_types` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL UNIQUE,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `description` TEXT DEFAULT NULL,
        `icon_class` VARCHAR(100) NOT NULL DEFAULT 'villa',
        `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
        `display_order` INT(11) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $db->query($createTypesSql);
    echo "   -> `property_types` table verified/created.\n";

    // Seed default property types
    $seedTypes = [
        ['Farmhouse', 'farmhouse', 'Spacious countryside estates with green lawns, private pools, and farm activities.', 'agriculture', 1],
        ['Resort', 'resort', 'Full-service vacation resorts with dining, recreation, and luxury stays.', 'holiday_village', 2],
        ['Villa', 'villa', 'Standalone luxury villas with private courtyards, bedrooms, and terraces.', 'villa', 3],
        ['Private Pool Villa', 'private-pool-villa', 'Exclusive private villas featuring an attached swimming pool and sundeck.', 'pool', 4],
        ['Cottage', 'cottage', 'Charming cozy cottages surrounded by nature, gardens, and quiet environments.', 'cottage', 5],
        ['Guest House', 'guest-house', 'Comfortable family guest houses with home-cooked meals and hospitality.', 'home', 6],
        ['Apartment', 'apartment', 'Modern urban serviced apartments with fully equipped kitchens.', 'apartment', 7],
        ['Banquet & Party Property', 'banquet-party-property', 'Spacious lawns and halls for weddings, corporate offsites, and parties.', 'celebration', 8],
        ['Camp & Glamping', 'camp-glamping', 'Eco-friendly luxury tents and adventure camping in pristine outdoors.', 'camping', 9],
        ['Hotel', 'hotel', 'Boutique hotels with standard amenities, room service, and suites.', 'hotel', 10],
    ];

    $checkStmt = $db->prepare("SELECT id FROM property_types WHERE slug = ? LIMIT 1");
    $insertStmt = $db->prepare("INSERT INTO property_types (name, slug, description, icon_class, display_order, status) VALUES (?, ?, ?, ?, ?, 'active')");

    foreach ($seedTypes as $t) {
        $checkStmt->bind_param("s", $t[1]);
        $checkStmt->execute();
        $res = $checkStmt->get_result();
        if ($res->num_rows === 0) {
            $insertStmt->bind_param("ssssi", $t[0], $t[1], $t[2], $t[3], $t[4]);
            $insertStmt->execute();
            echo "   -> Seeded Property Type: {$t[0]}\n";
        }
    }
    $checkStmt->close();
    $insertStmt->close();

    // 3. M2: Add `property_type_id`, `is_verified`, `verification_notes` to `farmhouses`
    echo "3. Updating `farmhouses` table...\n";
    $db->query("ALTER TABLE `farmhouses` ADD COLUMN IF NOT EXISTS `property_type_id` INT(11) DEFAULT NULL AFTER `category`");
    $db->query("ALTER TABLE `farmhouses` ADD COLUMN IF NOT EXISTS `is_verified` TINYINT(1) NOT NULL DEFAULT 1 AFTER `admin_approval_status`");
    $db->query("ALTER TABLE `farmhouses` ADD COLUMN IF NOT EXISTS `verification_notes` VARCHAR(255) DEFAULT 'Physically Inspected & Host Verified' AFTER `is_verified`");
    
    // Backfill `property_type_id` based on existing category text
    $db->query("UPDATE farmhouses f 
                JOIN property_types pt ON LOWER(f.category) = LOWER(pt.name) OR LOWER(f.category) = LOWER(pt.slug)
                SET f.property_type_id = pt.id 
                WHERE f.property_type_id IS NULL");
    // Fallback any remaining to Farmhouse (id 1)
    $db->query("UPDATE farmhouses SET property_type_id = (SELECT id FROM property_types WHERE slug = 'farmhouse' LIMIT 1) WHERE property_type_id IS NULL OR property_type_id = 0");
    echo "   -> `farmhouses` updated with `property_type_id`, `is_verified`, `verification_notes`.\n";

    // 4. M2: Add `category`, `caption`, `is_featured`, `sort_order` to `images` table
    echo "4. Updating `images` table for Categorized Gallery...\n";
    $db->query("ALTER TABLE `images` ADD COLUMN IF NOT EXISTS `category` VARCHAR(50) NOT NULL DEFAULT 'exterior' AFTER `image_url`");
    $db->query("ALTER TABLE `images` ADD COLUMN IF NOT EXISTS `caption` VARCHAR(255) DEFAULT NULL AFTER `category`");
    $db->query("ALTER TABLE `images` ADD COLUMN IF NOT EXISTS `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `caption`");
    $db->query("ALTER TABLE `images` ADD COLUMN IF NOT EXISTS `sort_order` INT(11) NOT NULL DEFAULT 0 AFTER `is_featured`");
    echo "   -> `images` updated with `category`, `caption`, `is_featured`, `sort_order`.\n";

    echo "\n=== Migration Finished Successfully! ===\n";
} catch (\Throwable $e) {
    echo "MIGRATION ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
