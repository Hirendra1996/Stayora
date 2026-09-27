<?php
$pageTitle  = "Edit Amenity";
$activePage = "amenities";

include __DIR__ . "/../Includes/admin_header.php";

$currentName     = $_POST['name'] ?? ($amenity['name'] ?? '');
$currentCategory = $_POST['category'] ?? ($amenity['category'] ?? 'general');
$currentIcon     = $_POST['icon_class'] ?? ($amenity['icon_class'] ?? 'hotel_class');

$amenityIcons = [
    'Essentials & Living' => ['wifi', 'local_parking', 'ac_unit', 'heat_pump', 'tv', 'ev_station', 'elevator', 'accessible', 'power', 'fireplace'],
    'Kitchen & Dining'    => ['cooking', 'flatware', 'coffee_maker', 'microwave_gen', 'local_bar', 'restaurant', 'kitchen', 'dishwasher_gen'],
    'Bedroom & Laundry'   => ['bed', 'dry_cleaning', 'iron', 'balcony', 'checkroom', 'meeting_room', 'wash'],
    'Bathroom & Health'   => ['bathtub', 'shower', 'medical_services', 'soap', 'spa', 'fitness_center', 'hot_tub'],
    'Outdoors & Fun'      => ['pool', 'outdoor_grill', 'deck', 'beach_access', 'forest', 'mountain_flag', 'potted_plant', 'yard'],
    'Safety & Security'   => ['videocam', 'sensor_occupied', 'fire_extinguisher', 'door_sensor', 'shield_lock', 'smoke_free', 'security']
];
?>

<div class="space-y-6 max-w-4xl mx-auto pb-12">

    <!-- ── BREADCRUMBS & PAGE HEADER ── -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1 text-xs text-muted mb-1 font-semibold">
                <a href="<?= url('admin/dashboard') ?>" class="hover:text-sky transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a href="<?= url('admin/manageamenities') ?>" class="hover:text-sky transition-colors">Amenities</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink font-bold">Edit Amenity</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-sky text-3xl">settings_suggest</span>
                Edit Amenity Settings
            </h1>
            <p class="text-muted text-sm mt-0.5">
                Update naming, classification, or visual icon for this facility.
            </p>
        </div>

        <a href="<?= url('admin/manageamenities') ?>" 
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs border border-border shadow-sm transition-all active:scale-95">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Back to Amenities
        </a>
    </div>

    <!-- ── FLASH NOTIFICATION ── -->
    <?php if (!empty($message)): ?>
        <div class="p-4 rounded-2xl flex items-center gap-3 border <?= $messageType === 'error' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' ?>">
            <span class="material-symbols-outlined text-xl"><?= $messageType === 'error' ? 'error' : 'check_circle' ?></span>
            <span class="font-bold text-xs"><?= htmlspecialchars($message) ?></span>
        </div>
    <?php endif; ?>

    <!-- ── MAIN FORM ── -->
    <form method="POST" action="" class="space-y-6">
        <input type="hidden" name="icon_class" id="selected_icon_input" value="<?= htmlspecialchars($currentIcon) ?>">

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Left Column: Name, Category & Live Preview -->
            <div class="md:col-span-5 space-y-6">
                
                <div class="bg-white rounded-3xl border border-border p-6 shadow-sm space-y-4">
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                            Amenity Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="amenity_name_input" required maxlength="100"
                               value="<?= htmlspecialchars($currentName) ?>"
                               placeholder="e.g. Private Swimming Pool" 
                               oninput="document.getElementById('live-preview-text').textContent = this.value || 'Facility Name'"
                               class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                        <p class="text-[11px] text-muted mt-1.5">Amenity ID: #<?= $amenity['id'] ?? 0 ?></p>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                            Category Scope <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" required 
                                class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all cursor-pointer">
                            <option value="general" <?= $currentCategory === 'general' ? 'selected' : '' ?>>🏠 General & Common</option>
                            <option value="bedroom" <?= $currentCategory === 'bedroom' ? 'selected' : '' ?>>🛏️ Bedroom & Sleep</option>
                            <option value="kitchen" <?= $currentCategory === 'kitchen' ? 'selected' : '' ?>>🍳 Kitchen & Dining</option>
                            <option value="bathroom" <?= $currentCategory === 'bathroom' ? 'selected' : '' ?>>🚿 Bathroom & Hygiene</option>
                            <option value="outdoor" <?= $currentCategory === 'outdoor' ? 'selected' : '' ?>>🌴 Outdoor & Recreation</option>
                            <option value="other" <?= $currentCategory === 'other' ? 'selected' : '' ?>>✨ Other Services</option>
                        </select>
                    </div>

                    <!-- Live Appearance Preview -->
                    <div class="pt-3 border-t border-border">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-muted block mb-2">Live Listing Preview</span>
                        <div class="p-4 rounded-2xl bg-ink text-white flex items-center gap-3 border border-sky/30 shadow-md">
                            <div class="w-10 h-10 rounded-xl bg-sky/20 border border-sky/40 text-sky flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-xl" id="live-preview-icon"><?= htmlspecialchars($currentIcon) ?></span>
                            </div>
                            <div>
                                <span class="font-bold text-xs text-white block" id="live-preview-text">
                                    <?= !empty($currentName) ? htmlspecialchars($currentName) : 'Facility Name' ?>
                                </span>
                                <span class="text-[10px] text-stone-400">Equipped Guest Feature</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Icon Picker Grid -->
            <div class="md:col-span-7">
                <div class="bg-white rounded-3xl border border-border p-6 shadow-sm space-y-4 flex flex-col justify-between h-full">
                    <div>
                        <h3 class="font-bold text-ink text-sm">Select Visual Symbol</h3>
                        <p class="text-xs text-muted">Choose a Material Symbol representing this amenity.</p>

                        <div class="mt-4 max-h-72 overflow-y-auto space-y-4 p-3 bg-surface rounded-2xl border border-border">
                            <?php foreach ($amenityIcons as $catName => $icons): ?>
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-muted block mb-1.5"><?= $catName ?></span>
                                    <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
                                        <?php foreach ($icons as $ic): 
                                            $isSel = ($currentIcon === $ic);
                                        ?>
                                            <button type="button" onclick="selectPageIcon('<?= $ic ?>')" 
                                                    class="icon-page-btn w-9 h-9 rounded-xl border flex items-center justify-center transition-all <?= $isSel ? 'border-sky bg-sky-l text-sky font-bold shadow-xs' : 'border-border bg-white text-ink hover:border-sky hover:bg-sky-l' ?>" 
                                                    data-icon="<?= $ic ?>" title="<?= $ic ?>">
                                                <span class="material-symbols-outlined text-lg"><?= $ic ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-border flex items-center justify-end gap-3">
                        <a href="<?= url('admin/manageamenities') ?>" class="px-5 py-2.5 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-sky/25 hover:shadow-sky/40 active:scale-95 transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">save</span>
                            Save Amenity Changes
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

<script>
    function selectPageIcon(iconName) {
        document.getElementById('selected_icon_input').value = iconName;
        document.getElementById('live-preview-icon').textContent = iconName;

        document.querySelectorAll('.icon-page-btn').forEach(btn => {
            if (btn.getAttribute('data-icon') === iconName) {
                btn.className = 'icon-page-btn w-9 h-9 rounded-xl border border-sky bg-sky-l text-sky font-bold flex items-center justify-center transition-all shadow-xs';
            } else {
                btn.className = 'icon-page-btn w-9 h-9 rounded-xl border border-border bg-white text-ink hover:border-sky hover:bg-sky-l flex items-center justify-center transition-all';
            }
        });
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>