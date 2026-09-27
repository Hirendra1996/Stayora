<?php
namespace App\Models\Owner;

use mysqli;

/**
 * OwnerProfileModel
 * DB operations for the owner's own profile.
 * Table touched: owners
 */
class OwnerProfileModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    // ================================================================
    //  READ
    // ================================================================

    /**
     * Fetch a single owner row by primary key.
     * Password hash is included so the controller can verify it for password changes.
     */
    public function getOwnerById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM owners WHERE id = ?");
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    // ================================================================
    //  UNIQUENESS CHECKS
    // ================================================================

    /**
     * Check whether an email is already registered to a *different* owner.
     * Used to validate email changes without blocking the owner from
     * submitting their own current email.
     */
    public function emailExistsForOther(string $email, int $excludeOwnerId): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM owners WHERE email = ? AND id != ? LIMIT 1"
        );
        if (!$stmt) return false;

        $stmt->bind_param("si", $email, $excludeOwnerId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // ================================================================
    //  WRITE
    // ================================================================

    /**
     * Update profile fields.
     * $data may contain: name, email, phone, and optionally profile_image.
     * Only the keys present in $data will be used — the method builds
     * the SET clause dynamically to avoid overwriting profile_image
     * when no new image was uploaded.
     */
    public function updateProfile(int $id, array $data): bool {
        $allowedFields = ['name', 'email', 'phone', 'profile_image'];

        $setClauses = [];
        $types      = '';
        $values     = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $setClauses[] = "`{$field}` = ?";
                $types       .= 's';
                $values[]     = $data[$field];
            }
        }

        if (empty($setClauses)) return false;

        $types   .= 'i';   // for WHERE id = ?
        $values[] = $id;

        $sql  = "UPDATE owners SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    /**
     * Update the password hash only.
     *
     * @param int    $id      Owner primary key
     * @param string $hashed  Already-hashed password (PASSWORD_DEFAULT)
     */
    public function updatePassword(int $id, string $hashed): bool {
        $stmt = $this->db->prepare("UPDATE owners SET password = ? WHERE id = ?");
        if (!$stmt) return false;

        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }
}
