<?php
namespace App\Models\Admin;

use mysqli;
use Exception;

class PropertyRuleModel {

    private mysqli $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Get all rules with real-time farmhouse enforcement usage counts
     */
    public function getAllRules(string $search = ''): array {
        $sql = "SELECT 
                    rp.*, 
                    (SELECT COUNT(DISTINCT fr.farmhouse_id) 
                     FROM farmhouse_rules fr 
                     WHERE LOWER(TRIM(fr.rule_name)) = LOWER(TRIM(rp.rule_name))) AS usage_count
                FROM rule_presets rp";

        if (!empty($search)) {
            $q = $this->db->real_escape_string(strtolower($search));
            $sql .= " WHERE LOWER(rp.rule_name) LIKE '%$q%' OR LOWER(rp.icon_class) LIKE '%$q%'";
        }

        $sql .= " ORDER BY rp.rule_name ASC";

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getRuleById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT rp.*, (SELECT COUNT(DISTINCT fr.farmhouse_id) FROM farmhouse_rules fr WHERE LOWER(TRIM(fr.rule_name)) = LOWER(TRIM(rp.rule_name))) AS usage_count FROM rule_presets rp WHERE rp.id = ?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function isRuleNameUnique(string $ruleName, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM rule_presets WHERE LOWER(TRIM(rule_name)) = LOWER(TRIM(?))";
        if ($excludeId) $sql .= " AND id != ?";
        
        $stmt = $this->db->prepare($sql);
        if ($excludeId) {
            $stmt->bind_param("si", $ruleName, $excludeId);
        } else {
            $stmt->bind_param("s", $ruleName);
        }
        
        $stmt->execute();
        return $stmt->get_result()->num_rows === 0;
    }

    public function createRule(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO rule_presets (rule_name, icon_class) VALUES (?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ss", $data['rule_name'], $data['icon_class']);
        return $stmt->execute();
    }

    public function updateRule(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE rule_presets SET rule_name = ?, icon_class = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("ssi", $data['rule_name'], $data['icon_class'], $id);
        return $stmt->execute();
    }

    public function deleteRule(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM rule_presets WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function bulkDelete(array $ids): int {
        if (empty($ids)) return 0;
        $intIds = array_map('intval', $ids);
        $inList = implode(',', $intIds);
        $this->db->query("DELETE FROM rule_presets WHERE id IN ($inList)");
        return $this->db->affected_rows;
    }

    /**
     * KPI Analytics for Rules System
     */
    public function getRuleStats(): array {
        $stats = [
            'total_rules'       => 0,
            'total_enforcements'=> 0,
            'top_rule'          => 'None',
            'top_rule_count'    => 0,
        ];

        $resCount = $this->db->query("SELECT COUNT(*) as c FROM rule_presets");
        if ($resCount) {
            $stats['total_rules'] = (int)($resCount->fetch_assoc()['c'] ?? 0);
        }

        $resEnf = $this->db->query("SELECT COUNT(*) as c FROM farmhouse_rules");
        if ($resEnf) {
            $stats['total_enforcements'] = (int)($resEnf->fetch_assoc()['c'] ?? 0);
        }

        $resTop = $this->db->query(
            "SELECT rule_name, COUNT(*) as c 
             FROM farmhouse_rules 
             GROUP BY LOWER(TRIM(rule_name)) 
             ORDER BY c DESC LIMIT 1"
        );
        if ($resTop && $resTop->num_rows > 0) {
            $row = $resTop->fetch_assoc();
            $stats['top_rule'] = $row['rule_name'];
            $stats['top_rule_count'] = (int)$row['c'];
        }

        return $stats;
    }
}