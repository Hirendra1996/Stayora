<?php
namespace App\Models\Admin;

use App\Config\Database;

class PropertyTypeModel {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?: Database::connect();
    }

    public function getAll(): array {
        $sql = "SELECT pt.*, COUNT(f.id) AS property_count 
                FROM property_types pt 
                LEFT JOIN farmhouses f ON f.property_type_id = pt.id 
                GROUP BY pt.id 
                ORDER BY pt.display_order ASC, pt.name ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getActive(): array {
        $sql = "SELECT * FROM property_types WHERE status = 'active' ORDER BY display_order ASC, name ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM property_types WHERE id = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res ?: null;
    }

    public function save(array $data): bool {
        $id = !empty($data['id']) ? (int)$data['id'] : 0;
        $name = trim($data['name'] ?? '');
        $slug = trim($data['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }
        $description = trim($data['description'] ?? '');
        $iconClass = trim($data['icon_class'] ?? 'villa');
        $displayOrder = (int)($data['display_order'] ?? 0);
        $status = in_array($data['status'] ?? '', ['active', 'inactive'], true) ? $data['status'] : 'active';

        if ($id > 0) {
            $stmt = $this->db->prepare(
                "UPDATE property_types 
                 SET name = ?, slug = ?, description = ?, icon_class = ?, display_order = ?, status = ? 
                 WHERE id = ?"
            );
            if (!$stmt) return false;
            $stmt->bind_param("ssssisi", $name, $slug, $description, $iconClass, $displayOrder, $status, $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO property_types (name, slug, description, icon_class, display_order, status) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) return false;
            $stmt->bind_param("ssssis", $name, $slug, $description, $iconClass, $displayOrder, $status);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }
    }

    public function toggleStatus(int $id): bool {
        $stmt = $this->db->prepare("UPDATE property_types SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete(int $id): bool {
        // Prevent deletion if farmhouses are attached
        $check = $this->db->query("SELECT COUNT(*) AS total FROM farmhouses WHERE property_type_id = $id");
        if ($check && ($row = $check->fetch_assoc()) && (int)$row['total'] > 0) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM property_types WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
