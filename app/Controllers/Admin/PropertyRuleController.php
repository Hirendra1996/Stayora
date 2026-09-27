<?php
namespace App\Controllers\Admin;

use App\Helpers\CryptoHelper;
use App\Config\Database;
use App\Models\Admin\PropertyRuleModel;
use Exception;

class PropertyRuleController {

    private $db;
    private PropertyRuleModel $model;

    public function __construct() {
        try {
            $this->db = Database::connect();
            $this->model = new PropertyRuleModel($this->db);
        } catch (Exception $e) {
            die("Database Initialization Failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    // MANAGE / LIST / BULK ACTIONS
    // GET /admin/managerules
    // ================================================================
    public function manage(): void {
        try {
            // Handle POST actions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';

                // 1. Single Delete
                if ($action === 'delete') {
                    $rawId = $_POST['rule_id'] ?? '';
                    $ruleId = $this->resolveId($rawId);
                    
                    if ($ruleId > 0 && $this->model->deleteRule($ruleId)) {
                        $this->flash('success_msg', "Rule directive successfully removed.");
                    } else {
                        $this->flash('error_msg', "Failed to delete rule directive.");
                    }
                    redirect('admin/managerules');
                }

                // 2. Bulk Delete
                if ($action === 'bulk_delete') {
                    $rawIds = $_POST['rule_ids'] ?? [];
                    if (!empty($rawIds) && is_array($rawIds)) {
                        $resolvedIds = [];
                        foreach ($rawIds as $raw) {
                            try {
                                $resolvedIds[] = $this->resolveId($raw);
                            } catch (Exception $e) {}
                        }
                        $count = $this->model->bulkDelete($resolvedIds);
                        $this->flash('success_msg', "Successfully deleted {$count} rule directive(s).");
                    } else {
                        $this->flash('error_msg', "No rules selected for deletion.");
                    }
                    redirect('admin/managerules');
                }

                // 3. Quick Inline Add from Modal
                if ($action === 'create_rule') {
                    $ruleName  = trim($_POST['rule_name'] ?? '');
                    $iconClass = trim($_POST['icon_class'] ?? 'gavel');

                    if (empty($ruleName)) {
                        $this->flash('error_msg', "Rule directive name is required.");
                    } elseif (!$this->model->isRuleNameUnique($ruleName)) {
                        $this->flash('error_msg', "A rule with the name '{$ruleName}' already exists.");
                    } else {
                        if ($this->model->createRule(['rule_name' => $ruleName, 'icon_class' => $iconClass])) {
                            $this->flash('success_msg', "New rule '{$ruleName}' added to platform presets.");
                        } else {
                            $this->flash('error_msg', "Failed to create rule directive.");
                        }
                    }
                    redirect('admin/managerules');
                }

                // 4. Quick Inline Update from Modal
                if ($action === 'update_rule') {
                    $rawId     = $_POST['rule_id'] ?? '';
                    $ruleId    = $this->resolveId($rawId);
                    $ruleName  = trim($_POST['rule_name'] ?? '');
                    $iconClass = trim($_POST['icon_class'] ?? 'gavel');

                    if (empty($ruleName)) {
                        $this->flash('error_msg', "Rule directive name is required.");
                    } elseif (!$this->model->isRuleNameUnique($ruleName, $ruleId)) {
                        $this->flash('error_msg', "Another rule with the name '{$ruleName}' already exists.");
                    } else {
                        if ($this->model->updateRule($ruleId, ['rule_name' => $ruleName, 'icon_class' => $iconClass])) {
                            $this->flash('success_msg', "Rule directive '{$ruleName}' updated successfully.");
                        } else {
                            $this->flash('error_msg', "Failed to update rule directive.");
                        }
                    }
                    redirect('admin/managerules');
                }
            }

            // Search query
            $search = trim($_GET['q'] ?? '');
            $rules  = $this->model->getAllRules($search);
            $stats  = $this->model->getRuleStats();

            // Encrypt Rule IDs
            $rules = array_map(function(array $r): array {
                $r['encrypted_id'] = CryptoHelper::encrypt((string)$r['id']);
                return $r;
            }, $rules);

            $success_message = $this->popFlash('success_msg');
            $error_message   = $this->popFlash('error_msg');

            include __DIR__ . '/../../Views/admin/manage_rules.php';
            $this->db->close();
            
        } catch (Exception $e) {
            die("Error loading rules: " . htmlspecialchars($e->getMessage()));
        }
    }

    // ================================================================
    // ADD RULE PRESET (Dedicated Page)
    // GET /admin/addrule
    // ================================================================
    public function add(): void {
        $message = null; $messageType = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rule_name  = trim($_POST['rule_name'] ?? '');
            $icon_class = trim($_POST['icon_class'] ?? 'gavel');

            if (empty($rule_name)) {
                $message = "Rule name is required."; $messageType = "error";
            } elseif (!$this->model->isRuleNameUnique($rule_name)) {
                $message = "This Rule Preset already exists."; $messageType = "error";
            } else {
                if ($this->model->createRule(compact('rule_name', 'icon_class'))) {
                    $this->flash('success_msg', "Rule '{$rule_name}' published successfully.");
                    redirect('admin/managerules');
                } else {
                    $message = "Failed to add Rule Preset."; $messageType = "error";
                }
            }
        }
        include __DIR__ . '/../../Views/admin/add_rule.php';
        $this->db->close();
    }

    // ================================================================
    // EDIT RULE PRESET (Dedicated Page)
    // GET /admin/editrule?id=...
    // ================================================================
    public function edit(): void {
        try {
            $rawId = $_GET['id'] ?? '';
            $id = $this->resolveId($rawId);

            $message = null; $messageType = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $rule_name  = trim($_POST['rule_name'] ?? '');
                $icon_class = trim($_POST['icon_class'] ?? 'gavel');

                if (empty($rule_name)) {
                    $message = "Rule name is required."; $messageType = "error";
                } elseif (!$this->model->isRuleNameUnique($rule_name, $id)) {
                    $message = "This Rule Preset name already exists."; $messageType = "error";
                } else {
                    if ($this->model->updateRule($id, compact('rule_name', 'icon_class'))) {
                        $this->flash('success_msg', "Rule preset updated successfully.");
                        redirect('admin/managerules');
                    } else {
                        $message = "Failed to update Rule preset."; $messageType = "error";
                    }
                }
            }

            $rule = $this->model->getRuleById($id);
            if (!$rule) {
                $this->flash('error_msg', "Rule preset not found.");
                redirect('admin/managerules');
            }

            $rule['encrypted_id'] = CryptoHelper::encrypt((string)$rule['id']);

            include __DIR__ . '/../../Views/admin/edit_rule.php';
            $this->db->close();

        } catch (Exception $e) {
            die("Error loading rule editor: " . htmlspecialchars($e->getMessage()));
        }
    }

    // Helper: ID resolver
    private function resolveId(string $raw): int {
        if (empty($raw)) throw new Exception("Missing ID.");
        if (ctype_digit($raw)) return (int) $raw;
        $decrypted = CryptoHelper::decrypt($raw);
        if (!$decrypted || !ctype_digit($decrypted)) throw new Exception("Invalid ID.");
        return (int) $decrypted;
    }

    // Flash Messaging Helpers
    private function flash(string $key, string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[$key] = $message;
    }
    private function popFlash(string $key): ?string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $msg = $_SESSION[$key] ?? null; unset($_SESSION[$key]);
        return $msg;
    }
}