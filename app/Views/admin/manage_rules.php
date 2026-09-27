<?php
$pageTitle  = "Important Property Rules";
$activePage = "rules";

include __DIR__ . "/../Includes/admin_header.php";

$totalRules = (int) ($stats['total_rules'] ?? count($rules));
$totalEnforcements = (int) ($stats['total_enforcements'] ?? 0);
$topRule = $stats['top_rule'] ?? 'None';
$topRuleCount = (int) ($stats['top_rule_count'] ?? 0);

// Comprehensive Material Symbols Icon Set for Property Rules
$iconCategories = [
    'General & Timing' => [
        'gavel', 'info', 'schedule', 'door_front', 'vpn_key', 'home', 'task_alt', 'list_alt', 'priority_high'
    ],
    'Restrictions & Noise' => [
        'volume_off', 'volume_up', 'block', 'smoke_free', 'no_photography', 'no_meals', 
        'no_drinks', 'celebration', 'person_remove', 'alarm_off', 'groups'
    ],
    'Amenities & Living' => [
        'pool', 'wifi', 'local_parking', 'kitchen', 'flatware', 'tv', 'ac_unit', 
        'bathtub', 'shower', 'bed', 'local_laundry_service'
    ],
    'Pets & Outdoors' => [
        'pets', 'potted_plant', 'park', 'deck', 'balcony', 'outdoor_grill', 'yard', 'recycling'
    ],
    'Safety & Security' => [
        'shield_lock', 'videocam', 'camera_indoor', 'fire_extinguisher', 'medical_services', 'health_and_safety'
    ]
];
?>

<div class="space-y-5 pb-12">

    <!-- ── 1. FLASH NOTIFICATIONS ────────────────────────────────────────────── -->
    <?php if (!empty($success_message)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($success_message) ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600 text-2xl">error</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($error_message) ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- ── 2. PAGE HEADER & ACTIONS ─────────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1 text-xs text-muted mb-1 font-semibold">
                <a href="<?= url('admin/dashboard') ?>" class="hover:text-sky transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink font-bold">Important Rules</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-sky text-3xl">gavel</span>
                Important Property Rules
            </h1>
            <p class="text-muted text-sm mt-1">
                Configure global stay policies, restrictions, and house rule templates enforced across farmhouses.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <button type="button" onclick="openAddRuleModal()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Add New Rule
            </button>
        </div>
    </div>

    <!-- ── 3. COMPACT KPI METRIC BAR ───────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        
        <!-- Total Rules -->
        <div class="bg-white rounded-xl border border-border p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sky-l border border-sky/20 flex items-center justify-center text-sky shrink-0">
                    <span class="material-symbols-outlined text-lg">gavel</span>
                </div>
                <div>
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $totalRules) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">Rule Presets</div>
                </div>
            </div>
            <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2 py-0.5 rounded-md">Global Presets</span>
        </div>

        <!-- Enforcements -->
        <div class="bg-white rounded-xl border border-border p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">verified</span>
                </div>
                <div>
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $totalEnforcements) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">Property Enforcements</div>
                </div>
            </div>
            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Active Sync</span>
        </div>

        <!-- Top Rule -->
        <div class="bg-white rounded-xl border border-border p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">hotel_class</span>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-black text-ink truncate leading-tight"><?= htmlspecialchars($topRule) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">Most Enforced</div>
                </div>
            </div>
            <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md shrink-0">
                <?= $topRuleCount ?> Farms
            </span>
        </div>

    </div>

    <!-- ── 4. SEARCH & BULK STRIP ────────────────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 shadow-sm space-y-3">
        
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">search</span>
                <input type="text" id="ruleQuickSearch" oninput="filterRulesTable(this.value)" 
                       placeholder="Instant search by rule directive name or icon..." 
                       class="w-full bg-surface border border-border rounded-xl py-2 pl-10 pr-4 text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
            </div>

            <div class="text-xs text-muted font-bold shrink-0">
                Showing <span id="visibleRuleCount" class="text-ink"><?= count($rules) ?></span> of <?= count($rules) ?> Rules
            </div>
        </div>

        <!-- Floating Bulk Bar -->
        <div id="ruleBulkBar" class="hidden items-center justify-between bg-ink text-white p-3 rounded-xl shadow-lg border border-sky/30 animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky animate-pulse"></span>
                <span id="ruleSelectedCount" class="text-xs font-bold text-sky-l">0 rules selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitRuleBulkDelete()" 
                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Delete Selected
                </button>
                <button type="button" onclick="deselectAllRules()" 
                        class="px-2.5 py-1.5 text-xs text-stone-300 hover:text-white underline">
                    Clear
                </button>
            </div>
        </div>

    </div>

    <!-- ── 5. RULES DATA TABLE ───────────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl border border-border shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="rulesMainTable">
                
                <thead>
                    <tr class="bg-surface border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" id="selectAllRulesCheckbox" onchange="toggleSelectAllRules(this)" 
                                   class="rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-4 px-4 w-16 text-center">Icon</th>
                        <th class="py-4 px-4">Rule Directive & Description</th>
                        <th class="py-4 px-4 text-center">Enforced Across</th>
                        <th class="py-4 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border/60 text-sm">
                    <?php if (!empty($rules)): ?>
                        <?php foreach ($rules as $idx => $r): 
                            $encId = $r['encrypted_id'];
                            $icon  = !empty($r['icon_class']) ? $r['icon_class'] : 'gavel';
                            $usage = (int) ($r['usage_count'] ?? 0);
                        ?>
                        <tr class="rule-table-row hover:bg-surface/50 transition-colors" data-rule-name="<?= htmlspecialchars(strtolower($r['rule_name'])) ?>" data-rule-icon="<?= htmlspecialchars(strtolower($icon)) ?>">
                            
                            <!-- Checkbox -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="<?= $encId ?>" class="rule-checkbox rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer" onchange="onRuleRowCheckboxChange()">
                            </td>

                            <!-- Icon -->
                            <td class="py-4 px-4 text-center">
                                <div class="w-10 h-10 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky mx-auto shadow-2xs">
                                    <span class="material-symbols-outlined text-xl"><?= htmlspecialchars($icon) ?></span>
                                </div>
                            </td>

                            <!-- Rule Name -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-ink text-sm">
                                    <?= htmlspecialchars($r['rule_name']) ?>
                                </div>
                                <div class="text-[11px] text-muted flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-muted">ID: #<?= $r['id'] ?></span>
                                    <span>•</span>
                                    <span class="font-mono text-sky"><?= htmlspecialchars($icon) ?></span>
                                </div>
                            </td>

                            <!-- Usage Count -->
                            <td class="py-4 px-4 text-center">
                                <?php if ($usage > 0): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <?= $usage ?> <?= $usage === 1 ? 'Farmhouse' : 'Farmhouses' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface text-muted border border-border">
                                        Not assigned yet
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    
                                    <!-- Quick Edit Modal -->
                                    <button type="button" 
                                            onclick="openEditRuleModal('<?= $encId ?>', '<?= htmlspecialchars(addslashes($r['rule_name'])) ?>', '<?= htmlspecialchars(addslashes($icon)) ?>')" 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-2xs" title="Edit Rule">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>

                                    <!-- Delete Rule -->
                                    <form method="POST" action="<?= url('admin/managerules') ?>" class="inline" onsubmit="return confirm('Delete rule directive \'<?= htmlspecialchars(addslashes($r['rule_name'])) ?>\'?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="rule_id" value="<?= htmlspecialchars($encId) ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Delete Rule">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-16 text-center text-muted">
                                <span class="material-symbols-outlined text-5xl text-muted/40 mb-2 block">playlist_add</span>
                                <h4 class="font-bold text-ink text-base">No Property Rules Defined</h4>
                                <p class="text-xs text-muted mt-1 max-w-sm mx-auto">
                                    Create standard rule presets to enable toggle controls for property listings.
                                </p>
                                <button type="button" onclick="openAddRuleModal()" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-xl bg-sky text-white text-xs font-bold hover:bg-sky-d transition-colors">
                                    <span class="material-symbols-outlined text-base">add</span> Add First Rule
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

</div>

<!-- ── 6. ADD / EDIT RULE MODAL ───────────────────────────────────────────── -->
<div id="ruleFormModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-xl p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto space-y-6">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-start pb-4 border-b border-border">
            <div>
                <h3 id="rule_modal_title" class="text-xl font-extrabold text-ink">Add Property Rule</h3>
                <p class="text-xs text-muted mt-0.5">Template directive for listing stay policies.</p>
            </div>
            <button type="button" onclick="closeRuleModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form id="ruleModalForm" method="POST" action="<?= url('admin/managerules') ?>" class="space-y-6">
            <input type="hidden" name="action" id="rule_modal_action" value="create_rule">
            <input type="hidden" name="rule_id" id="rule_modal_id" value="">
            <input type="hidden" name="icon_class" id="rule_modal_icon" value="gavel">

            <!-- Rule Name Input -->
            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                    Rule Directive Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="rule_name" id="rule_modal_name" required maxlength="100"
                       placeholder="e.g. Quiet Hours after 10 PM" 
                       oninput="updateModalPreview()"
                       class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                <p class="text-[11px] text-muted mt-1">Examples: Pets Allowed, Strictly No Smoking, Loud Music Not Allowed, Swimming Pool Guidelines.</p>
            </div>

            <!-- Live Appearance Preview -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block mb-2">Live Listing Preview</span>
                <div class="p-4 rounded-2xl bg-ink text-white flex items-center justify-between border border-sky/30 shadow-md">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-sky/20 border border-sky/40 text-sky flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl" id="modal_preview_icon">gavel</span>
                        </div>
                        <span class="font-bold text-sm text-white truncate" id="modal_preview_text">Property Rule Name</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                        ✓ Allowed
                    </span>
                </div>
            </div>

            <!-- Categorized Icon Picker -->
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-2">Select Visual Icon</span>
                <div class="max-h-48 overflow-y-auto space-y-3 p-3 bg-surface rounded-2xl border border-border">
                    <?php foreach ($iconCategories as $catName => $icons): ?>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-muted block mb-1.5"><?= $catName ?></span>
                            <div class="grid grid-cols-6 sm:grid-cols-9 gap-2">
                                <?php foreach ($icons as $ic): ?>
                                    <button type="button" onclick="selectModalIcon('<?= $ic ?>')" 
                                            class="modal-icon-btn w-9 h-9 rounded-xl border border-border bg-white text-ink hover:border-sky hover:bg-sky-l flex items-center justify-center transition-all" 
                                            data-icon="<?= $ic ?>" title="<?= $ic ?>">
                                        <span class="material-symbols-outlined text-lg"><?= $ic ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit Bar -->
            <div class="pt-4 border-t border-border flex items-center justify-end gap-3">
                <button type="button" onclick="closeRuleModal()" class="px-5 py-2.5 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                    Cancel
                </button>
                <button type="submit" id="rule_modal_submit_btn" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-sky/25 hover:shadow-sky/40 transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">save</span>
                    Save Rule
                </button>
            </div>

        </form>

    </div>
</div>

<!-- Hidden Bulk Action Submission Form -->
<form id="ruleBulkActionForm" method="POST" action="<?= url('admin/managerules') ?>" class="hidden">
    <input type="hidden" name="action" value="bulk_delete">
    <div id="rule_bulk_ids_container"></div>
</form>

<!-- ── 7. JAVASCRIPT LOGIC ──────────────────────────────────────────────── -->
<script>
    function filterRulesTable(q) {
        q = q.toLowerCase().trim();
        const rows = document.querySelectorAll('.rule-table-row');
        let visible = 0;
        rows.forEach(r => {
            const name = r.getAttribute('data-rule-name') || '';
            const icon = r.getAttribute('data-rule-icon') || '';
            if (!q || name.includes(q) || icon.includes(q)) {
                r.classList.remove('hidden');
                visible++;
            } else {
                r.classList.add('hidden');
            }
        });
        document.getElementById('visibleRuleCount').textContent = visible;
    }

    // Modal Handlers
    function openAddRuleModal() {
        document.getElementById('rule_modal_title').textContent = 'Add Property Rule';
        document.getElementById('rule_modal_action').value = 'create_rule';
        document.getElementById('rule_modal_id').value = '';
        document.getElementById('rule_modal_name').value = '';
        document.getElementById('rule_modal_icon').value = 'gavel';
        document.getElementById('rule_modal_submit_btn').innerHTML = '<span class="material-symbols-outlined text-base">add</span> Add Rule Directive';
        
        selectModalIcon('gavel');
        updateModalPreview();
        document.getElementById('ruleFormModal').classList.remove('hidden');
        document.getElementById('rule_modal_name').focus();
    }

    function openEditRuleModal(encId, name, icon) {
        document.getElementById('rule_modal_title').textContent = 'Edit Property Rule';
        document.getElementById('rule_modal_action').value = 'update_rule';
        document.getElementById('rule_modal_id').value = encId;
        document.getElementById('rule_modal_name').value = name;
        document.getElementById('rule_modal_icon').value = icon;
        document.getElementById('rule_modal_submit_btn').innerHTML = '<span class="material-symbols-outlined text-base">save</span> Save Rule Changes';

        selectModalIcon(icon);
        updateModalPreview();
        document.getElementById('ruleFormModal').classList.remove('hidden');
        document.getElementById('rule_modal_name').focus();
    }

    function closeRuleModal() {
        document.getElementById('ruleFormModal').classList.add('hidden');
    }

    function selectModalIcon(iconName) {
        document.getElementById('rule_modal_icon').value = iconName;
        document.getElementById('modal_preview_icon').textContent = iconName;
        
        document.querySelectorAll('.modal-icon-btn').forEach(btn => {
            if (btn.getAttribute('data-icon') === iconName) {
                btn.className = 'modal-icon-btn w-9 h-9 rounded-xl border border-sky bg-sky-l text-sky font-bold flex items-center justify-center transition-all shadow-xs';
            } else {
                btn.className = 'modal-icon-btn w-9 h-9 rounded-xl border border-border bg-white text-ink hover:border-sky hover:bg-sky-l flex items-center justify-center transition-all';
            }
        });
    }

    function updateModalPreview() {
        const nameVal = document.getElementById('rule_modal_name').value.trim();
        document.getElementById('modal_preview_text').textContent = nameVal || 'Property Rule Name';
    }

    // Bulk Handlers
    function toggleSelectAllRules(master) {
        const checkboxes = document.querySelectorAll('.rule-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateRuleBulkBar();
    }

    function onRuleRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.rule-checkbox');
        const master = document.getElementById('selectAllRulesCheckbox');
        const checked = document.querySelectorAll('.rule-checkbox:checked');
        if (master) {
            master.checked = (checkboxes.length > 0 && checked.length === checkboxes.length);
        }
        updateRuleBulkBar();
    }

    function updateRuleBulkBar() {
        const checked = document.querySelectorAll('.rule-checkbox:checked');
        const bar = document.getElementById('ruleBulkBar');
        const countSpan = document.getElementById('ruleSelectedCount');

        if (checked.length > 0) {
            countSpan.textContent = `${checked.length} rule${checked.length === 1 ? '' : 's'} selected`;
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function deselectAllRules() {
        document.querySelectorAll('.rule-checkbox').forEach(cb => cb.checked = false);
        const master = document.getElementById('selectAllRulesCheckbox');
        if (master) master.checked = false;
        updateRuleBulkBar();
    }

    function submitRuleBulkDelete() {
        const checked = document.querySelectorAll('.rule-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Permanently delete ${checked.length} rule directive(s)? This will unbind them from any farmhouses.`)) return;

        const form = document.getElementById('ruleBulkActionForm');
        const container = document.getElementById('rule_bulk_ids_container');
        container.innerHTML = '';

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'rule_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>