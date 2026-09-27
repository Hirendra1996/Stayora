<?php
$pageTitle  = "Estate Amenities";
$activePage = "amenities";

include __DIR__ . "/../Includes/admin_header.php";

$totalAmenities = (int) ($stats['total'] ?? count($amenities));
$totalEquipped  = (int) ($stats['total_equipped'] ?? 0);
$topAmenity     = $stats['top_amenity'] ?? 'None';
$topCount       = (int) ($stats['top_count'] ?? 0);
$byCat          = $stats['by_category'] ?? [];

$currentCat = strtolower($_GET['category'] ?? 'all');
$searchVal  = htmlspecialchars($_GET['q'] ?? '');

$amenityIcons = [
    'Essentials & Living' => ['wifi', 'local_parking', 'ac_unit', 'heat_pump', 'tv', 'ev_station', 'elevator', 'accessible', 'power', 'fireplace'],
    'Kitchen & Dining'    => ['cooking', 'flatware', 'coffee_maker', 'microwave_gen', 'local_bar', 'restaurant', 'kitchen', 'dishwasher_gen'],
    'Bedroom & Laundry'   => ['bed', 'dry_cleaning', 'iron', 'balcony', 'checkroom', 'meeting_room', 'wash'],
    'Bathroom & Health'   => ['bathtub', 'shower', 'medical_services', 'soap', 'spa', 'fitness_center', 'hot_tub'],
    'Outdoors & Fun'      => ['pool', 'outdoor_grill', 'deck', 'beach_access', 'forest', 'mountain_flag', 'potted_plant', 'yard'],
    'Safety & Security'   => ['videocam', 'sensor_occupied', 'fire_extinguisher', 'door_sensor', 'shield_lock', 'smoke_free', 'security']
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
                <span class="text-ink font-bold">Amenities</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-sky text-3xl">hotel_class</span>
                Estate Amenities
            </h1>
            <p class="text-muted text-sm mt-1">
                Manage luxury features, facilities, and comfort specifications available across all farmhouse listings.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <button type="button" onclick="openAddAmenityModal()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Add New Amenity
            </button>
        </div>
    </div>

    <!-- ── 3. COMPACT KPI METRIC BAR ───────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        
        <!-- Total Amenities -->
        <div class="bg-white rounded-xl border border-border p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sky-l border border-sky/20 flex items-center justify-center text-sky shrink-0">
                    <span class="material-symbols-outlined text-lg">hotel_class</span>
                </div>
                <div>
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $totalAmenities) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">Verified Features</div>
                </div>
            </div>
            <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2 py-0.5 rounded-md">Catalog</span>
        </div>

        <!-- Property Attachments -->
        <div class="bg-white rounded-xl border border-border p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">verified</span>
                </div>
                <div>
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $totalEquipped) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">Equipped In Listings</div>
                </div>
            </div>
            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Live Facilities</span>
        </div>

        <!-- Top Amenity -->
        <div class="bg-white rounded-xl border border-border p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                    <span class="material-symbols-outlined text-lg">star</span>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-black text-ink truncate leading-tight"><?= htmlspecialchars($topAmenity) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider mt-0.5">Most Equipped</div>
                </div>
            </div>
            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md shrink-0">
                <?= $topCount ?> Farms
            </span>
        </div>

    </div>

    <!-- ── 4. SEARCH, CATEGORIES & BULK STRIP ────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 shadow-sm space-y-3">
        
        <form method="GET" action="" id="amenityFilterForm" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            
            <!-- Search Input -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">search</span>
                <input type="text" name="q" id="amenityQuickSearch" value="<?= $searchVal ?>" oninput="filterAmenitiesTable(this.value)" 
                       placeholder="Instant search by amenity name or icon..." 
                       class="w-full bg-surface border border-border rounded-xl py-2 pl-10 pr-4 text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
            </div>

            <!-- Category Pills -->
            <div class="flex items-center flex-wrap bg-surface border border-border rounded-xl p-1 gap-1 shrink-0">
                <a href="?category=all<?= !empty($searchVal) ? '&q='.urlencode($searchVal) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all <?= ($currentCat === 'all' || empty($currentCat)) ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    All (<?= $totalAmenities ?>)
                </a>
                <a href="?category=general<?= !empty($searchVal) ? '&q='.urlencode($searchVal) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all <?= ($currentCat === 'general') ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    General (<?= $byCat['general'] ?? 0 ?>)
                </a>
                <a href="?category=kitchen<?= !empty($searchVal) ? '&q='.urlencode($searchVal) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all <?= ($currentCat === 'kitchen') ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Kitchen (<?= $byCat['kitchen'] ?? 0 ?>)
                </a>
                <a href="?category=bedroom<?= !empty($searchVal) ? '&q='.urlencode($searchVal) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all <?= ($currentCat === 'bedroom') ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Bedroom (<?= $byCat['bedroom'] ?? 0 ?>)
                </a>
                <a href="?category=bathroom<?= !empty($searchVal) ? '&q='.urlencode($searchVal) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all <?= ($currentCat === 'bathroom') ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Bathroom (<?= $byCat['bathroom'] ?? 0 ?>)
                </a>
                <a href="?category=outdoor<?= !empty($searchVal) ? '&q='.urlencode($searchVal) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition-all <?= ($currentCat === 'outdoor') ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Outdoor (<?= $byCat['outdoor'] ?? 0 ?>)
                </a>
            </div>

            <div class="text-xs text-muted font-bold shrink-0 hidden lg:block">
                Showing <span id="visibleAmenityCount" class="text-ink"><?= count($amenities) ?></span> Amenities
            </div>

        </form>

        <!-- Floating Bulk Bar -->
        <div id="amenityBulkBar" class="hidden items-center justify-between bg-ink text-white p-3 rounded-xl shadow-lg border border-sky/30 animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky animate-pulse"></span>
                <span id="amenitySelectedCount" class="text-xs font-bold text-sky-l">0 amenities selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitAmenityBulkDelete()" 
                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Delete Selected
                </button>
                <button type="button" onclick="deselectAllAmenities()" 
                        class="px-2.5 py-1.5 text-xs text-stone-300 hover:text-white underline">
                    Clear
                </button>
            </div>
        </div>

    </div>

    <!-- ── 5. AMENITIES DATA TABLE ───────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl border border-border shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="amenitiesMainTable">
                
                <thead>
                    <tr class="bg-surface border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" id="selectAllAmenitiesCheckbox" onchange="toggleSelectAllAmenities(this)" 
                                   class="rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-4 px-4 w-16 text-center">Icon</th>
                        <th class="py-4 px-4">Amenity Feature</th>
                        <th class="py-4 px-4">Category Group</th>
                        <th class="py-4 px-4 text-center">Equipped Across</th>
                        <th class="py-4 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border/60 text-sm">
                    <?php if (!empty($amenities)): ?>
                        <?php foreach ($amenities as $idx => $a): 
                            $encId = $a['encrypted_id'];
                            $icon  = !empty($a['icon_class']) ? $a['icon_class'] : 'hotel_class';
                            $usage = (int) ($a['usage_count'] ?? 0);
                            $cat   = strtolower($a['category'] ?? 'general');

                            $catBadge = match($cat) {
                                'kitchen'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                'bedroom'  => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'bathroom' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                'outdoor'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                default    => 'bg-sky-l text-sky-d border-sky/20',
                            };
                        ?>
                        <tr class="amenity-table-row hover:bg-surface/50 transition-colors" data-amenity-name="<?= htmlspecialchars(strtolower($a['name'])) ?>" data-amenity-icon="<?= htmlspecialchars(strtolower($icon)) ?>">
                            
                            <!-- Checkbox -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="<?= $encId ?>" class="amenity-checkbox rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer" onchange="onAmenityRowCheckboxChange()">
                            </td>

                            <!-- Icon -->
                            <td class="py-4 px-4 text-center">
                                <div class="w-10 h-10 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky mx-auto shadow-2xs">
                                    <span class="material-symbols-outlined text-xl"><?= htmlspecialchars($icon) ?></span>
                                </div>
                            </td>

                            <!-- Amenity Name -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-ink text-sm">
                                    <?= htmlspecialchars($a['name']) ?>
                                </div>
                                <div class="text-[11px] text-muted flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-muted">ID: #<?= $a['id'] ?></span>
                                    <span>•</span>
                                    <span class="font-mono text-sky"><?= htmlspecialchars($icon) ?></span>
                                </div>
                            </td>

                            <!-- Category Badge -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border <?= $catBadge ?>">
                                    <?= ucfirst($cat) ?>
                                </span>
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
                                        Not equipped yet
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    
                                    <!-- Quick Edit Modal -->
                                    <button type="button" 
                                            onclick="openEditAmenityModal('<?= $encId ?>', '<?= htmlspecialchars(addslashes($a['name'])) ?>', '<?= htmlspecialchars(addslashes($cat)) ?>', '<?= htmlspecialchars(addslashes($icon)) ?>')" 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-2xs" title="Edit Amenity">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>

                                    <!-- Delete Amenity -->
                                    <form method="POST" action="<?= url('admin/manageamenities') ?>" class="inline" onsubmit="return confirm('Delete amenity \'<?= htmlspecialchars(addslashes($a['name'])) ?>\'?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="amenity_id" value="<?= htmlspecialchars($encId) ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Delete Amenity">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-16 text-center text-muted">
                                <span class="material-symbols-outlined text-5xl text-muted/40 mb-2 block">room_service</span>
                                <h4 class="font-bold text-ink text-base">No Amenities Found</h4>
                                <p class="text-xs text-muted mt-1 max-w-sm mx-auto">
                                    No amenities match your current search or category filter.
                                </p>
                                <button type="button" onclick="openAddAmenityModal()" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-xl bg-sky text-white text-xs font-bold hover:bg-sky-d transition-colors">
                                    <span class="material-symbols-outlined text-base">add</span> Add First Amenity
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

</div>

<!-- ── 6. ADD / EDIT AMENITY MODAL ────────────────────────────────────────── -->
<div id="amenityFormModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-xl p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto space-y-6">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-start pb-4 border-b border-border">
            <div>
                <h3 id="amenity_modal_title" class="text-xl font-extrabold text-ink">Add New Amenity</h3>
                <p class="text-xs text-muted mt-0.5">Feature or facility available for farmhouse stays.</p>
            </div>
            <button type="button" onclick="closeAmenityModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form id="amenityModalForm" method="POST" action="<?= url('admin/manageamenities') ?>" class="space-y-5">
            <input type="hidden" name="action" id="amenity_modal_action" value="create_amenity">
            <input type="hidden" name="amenity_id" id="amenity_modal_id" value="">
            <input type="hidden" name="icon_class" id="amenity_modal_icon" value="hotel_class">

            <!-- Name Input -->
            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                    Amenity Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="amenity_modal_name" required maxlength="100"
                       placeholder="e.g. Private Swimming Pool, High-Speed Wi-Fi" 
                       oninput="updateAmenityModalPreview()"
                       class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
            </div>

            <!-- Category Scope -->
            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                    Category Group <span class="text-rose-500">*</span>
                </label>
                <select name="category" id="amenity_modal_category" required 
                        class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all cursor-pointer">
                    <option value="general">🏠 General & Common</option>
                    <option value="bedroom">🛏️ Bedroom & Sleep</option>
                    <option value="kitchen">🍳 Kitchen & Dining</option>
                    <option value="bathroom">🚿 Bathroom & Hygiene</option>
                    <option value="outdoor">🌴 Outdoor & Recreation</option>
                    <option value="other">✨ Other Services</option>
                </select>
            </div>

            <!-- Live Appearance Preview -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block mb-2">Live Listing Preview</span>
                <div class="p-4 rounded-2xl bg-ink text-white flex items-center gap-3 border border-sky/30 shadow-md">
                    <div class="w-10 h-10 rounded-xl bg-sky/20 border border-sky/40 text-sky flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl" id="amenity_modal_preview_icon">hotel_class</span>
                    </div>
                    <div>
                        <span class="font-bold text-sm text-white block" id="amenity_modal_preview_text">Facility Name</span>
                        <span class="text-[10px] text-stone-400">Equipped Guest Feature</span>
                    </div>
                </div>
            </div>

            <!-- Categorized Icon Picker -->
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-2">Select Visual Icon</span>
                <div class="max-h-44 overflow-y-auto space-y-3 p-3 bg-surface rounded-2xl border border-border">
                    <?php foreach ($amenityIcons as $catName => $icons): ?>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-muted block mb-1.5"><?= $catName ?></span>
                            <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
                                <?php foreach ($icons as $ic): ?>
                                    <button type="button" onclick="selectAmenityModalIcon('<?= $ic ?>')" 
                                            class="amenity-modal-icon-btn w-9 h-9 rounded-xl border border-border bg-white text-ink hover:border-sky hover:bg-sky-l flex items-center justify-center transition-all" 
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
                <button type="button" onclick="closeAmenityModal()" class="px-5 py-2.5 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                    Cancel
                </button>
                <button type="submit" id="amenity_modal_submit_btn" 
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-sky/25 hover:shadow-sky/40 transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">save</span>
                    Save Amenity
                </button>
            </div>

        </form>

    </div>
</div>

<!-- Hidden Bulk Action Submission Form -->
<form id="amenityBulkActionForm" method="POST" action="<?= url('admin/manageamenities') ?>" class="hidden">
    <input type="hidden" name="action" value="bulk_delete">
    <div id="amenity_bulk_ids_container"></div>
</form>

<!-- ── 7. JAVASCRIPT LOGIC ──────────────────────────────────────────────── -->
<script>
    function filterAmenitiesTable(q) {
        q = q.toLowerCase().trim();
        const rows = document.querySelectorAll('.amenity-table-row');
        let visible = 0;
        rows.forEach(r => {
            const name = r.getAttribute('data-amenity-name') || '';
            const icon = r.getAttribute('data-amenity-icon') || '';
            if (!q || name.includes(q) || icon.includes(q)) {
                r.classList.remove('hidden');
                visible++;
            } else {
                r.classList.add('hidden');
            }
        });
        const cnt = document.getElementById('visibleAmenityCount');
        if (cnt) cnt.textContent = visible;
    }

    // Modal Handlers
    function openAddAmenityModal() {
        document.getElementById('amenity_modal_title').textContent = 'Add New Amenity';
        document.getElementById('amenity_modal_action').value = 'create_amenity';
        document.getElementById('amenity_modal_id').value = '';
        document.getElementById('amenity_modal_name').value = '';
        document.getElementById('amenity_modal_category').value = 'general';
        document.getElementById('amenity_modal_icon').value = 'hotel_class';
        document.getElementById('amenity_modal_submit_btn').innerHTML = '<span class="material-symbols-outlined text-base">add</span> Add Amenity';
        
        selectAmenityModalIcon('hotel_class');
        updateAmenityModalPreview();
        document.getElementById('amenityFormModal').classList.remove('hidden');
        document.getElementById('amenity_modal_name').focus();
    }

    function openEditAmenityModal(encId, name, category, icon) {
        document.getElementById('amenity_modal_title').textContent = 'Edit Amenity';
        document.getElementById('amenity_modal_action').value = 'update_amenity';
        document.getElementById('amenity_modal_id').value = encId;
        document.getElementById('amenity_modal_name').value = name;
        document.getElementById('amenity_modal_category').value = category;
        document.getElementById('amenity_modal_icon').value = icon;
        document.getElementById('amenity_modal_submit_btn').innerHTML = '<span class="material-symbols-outlined text-base">save</span> Save Amenity Changes';

        selectAmenityModalIcon(icon);
        updateAmenityModalPreview();
        document.getElementById('amenityFormModal').classList.remove('hidden');
        document.getElementById('amenity_modal_name').focus();
    }

    function closeAmenityModal() {
        document.getElementById('amenityFormModal').classList.add('hidden');
    }

    function selectAmenityModalIcon(iconName) {
        document.getElementById('amenity_modal_icon').value = iconName;
        document.getElementById('amenity_modal_preview_icon').textContent = iconName;
        
        document.querySelectorAll('.amenity-modal-icon-btn').forEach(btn => {
            if (btn.getAttribute('data-icon') === iconName) {
                btn.className = 'amenity-modal-icon-btn w-9 h-9 rounded-xl border border-sky bg-sky-l text-sky font-bold flex items-center justify-center transition-all shadow-xs';
            } else {
                btn.className = 'amenity-modal-icon-btn w-9 h-9 rounded-xl border border-border bg-white text-ink hover:border-sky hover:bg-sky-l flex items-center justify-center transition-all';
            }
        });
    }

    function updateAmenityModalPreview() {
        const nameVal = document.getElementById('amenity_modal_name').value.trim();
        document.getElementById('amenity_modal_preview_text').textContent = nameVal || 'Facility Name';
    }

    // Bulk Handlers
    function toggleSelectAllAmenities(master) {
        const checkboxes = document.querySelectorAll('.amenity-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateAmenityBulkBar();
    }

    function onAmenityRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.amenity-checkbox');
        const master = document.getElementById('selectAllAmenitiesCheckbox');
        const checked = document.querySelectorAll('.amenity-checkbox:checked');
        if (master) {
            master.checked = (checkboxes.length > 0 && checked.length === checkboxes.length);
        }
        updateAmenityBulkBar();
    }

    function updateAmenityBulkBar() {
        const checked = document.querySelectorAll('.amenity-checkbox:checked');
        const bar = document.getElementById('amenityBulkBar');
        const countSpan = document.getElementById('amenitySelectedCount');

        if (checked.length > 0) {
            countSpan.textContent = `${checked.length} amenit${checked.length === 1 ? 'y' : 'ies'} selected`;
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function deselectAllAmenities() {
        document.querySelectorAll('.amenity-checkbox').forEach(cb => cb.checked = false);
        const master = document.getElementById('selectAllAmenitiesCheckbox');
        if (master) master.checked = false;
        updateAmenityBulkBar();
    }

    function submitAmenityBulkDelete() {
        const checked = document.querySelectorAll('.amenity-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Permanently delete ${checked.length} amenity item(s)? This will unbind them from any farmhouses.`)) return;

        const form = document.getElementById('amenityBulkActionForm');
        const container = document.getElementById('amenity_bulk_ids_container');
        container.innerHTML = '';

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'amenity_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>