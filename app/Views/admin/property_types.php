<?php
$pageTitle  = "Property Types & Categories";
$activePage = "property_types";

include __DIR__ . "/../Includes/admin_header.php";

$totalTypes = count($propertyTypes);
$activeTypes = count(array_filter($propertyTypes, fn($t) => $t['status'] === 'active'));
$totalAssigned = array_sum(array_column($propertyTypes, 'property_count'));

$suggestedIcons = ['agriculture', 'holiday_village', 'villa', 'pool', 'cottage', 'home', 'apartment', 'celebration', 'camping', 'hotel', 'castle', 'nature_people'];
?>

<div class="space-y-6 pb-12">

    <!-- ── 1. FLASH NOTIFICATIONS ────────────────────────────────────────────── -->
    <?php if (!empty($_SESSION['success_msg'])): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($_SESSION['success_msg']) ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error_msg'])): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600 text-2xl">error</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($_SESSION['error_msg']) ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <?php unset($_SESSION['error_msg']); ?>
    <?php endif; ?>

    <!-- ── 2. PAGE HEADER ────────────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1 text-xs text-muted mb-1 font-semibold">
                <a href="<?= url('admin/dashboard') ?>" class="hover:text-sky transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink font-bold">Property Types</span>
            </nav>
            <h1 class="text-2xl font-black text-ink tracking-tight">Property Types & Architecture System</h1>
            <p class="text-xs text-muted mt-0.5">Manage marketplace categories, icons, search filters, and property classifications.</p>
        </div>

        <button onclick="openTypeModal()" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-md transition-all flex items-center gap-2 self-start md:self-auto cursor-pointer">
            <span class="material-symbols-outlined text-lg">add_circle</span>
            <span>Add Property Type</span>
        </button>
    </div>

    <!-- ── 3. KPI CARDS ────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">category</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Categories</p>
                <h3 class="text-2xl font-extrabold text-slate-800"><?= $totalTypes ?></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active in Search</p>
                <h3 class="text-2xl font-extrabold text-slate-800"><?= $activeTypes ?></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">holiday_village</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Listings</p>
                <h3 class="text-2xl font-extrabold text-slate-800"><?= $totalAssigned ?> Properties</h3>
            </div>
        </div>
    </div>

    <!-- ── 4. PROPERTY TYPES TABLE ────────────────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Configured Marketplace Categories</h2>
            <span class="text-xs text-slate-400"><?= $totalTypes ?> Registered Types</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4" style="width: 80px;">Icon</th>
                        <th class="py-3.5 px-4">Category Name</th>
                        <th class="py-3.5 px-4">URL Slug</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4 text-center">Listings</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($propertyTypes)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No property types found. Click "Add Property Type" to create one.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($propertyTypes as $type): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shadow-xs">
                                        <span class="material-symbols-outlined text-xl"><?= htmlspecialchars($type['icon_class'] ?: 'villa') ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900"><?= htmlspecialchars($type['name']) ?></div>
                                    <div class="text-[11px] text-slate-400">Order: <?= (int)$type['display_order'] ?></div>
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-sky-600">
                                    <?= htmlspecialchars($type['slug']) ?>
                                </td>
                                <td class="py-3 px-4 text-slate-600 text-xs max-w-xs truncate">
                                    <?= htmlspecialchars($type['description'] ?: '—') ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $type['property_count'] > 0 ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-500' ?>">
                                        <?= (int)$type['property_count'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="<?= url('admin/property-types/toggle?id=' . $type['id']) ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold transition-all <?= $type['status'] === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $type['status'] === 'active' ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                        <?= ucfirst($type['status']) ?>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button onclick='editType(<?= json_encode($type) ?>)' class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-sky-600 transition-colors" title="Edit Category">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </button>
                                        <?php if ($type['property_count'] == 0): ?>
                                            <form action="<?= url('admin/property-types/delete') ?>" method="POST" onsubmit="return confirm('Delete this property type?');" style="display:inline;">
                                                <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                                <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition-colors" title="Delete">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ── 5. ADD / EDIT PROPERTY TYPE MODAL ────────────────────────────────────────────── -->
<div id="typeModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl animate-fade-in border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Add Property Type</h3>
            <button onclick="closeTypeModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= url('admin/property-types/save') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="type_id" value="0">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Name *</label>
                <input type="text" name="name" id="type_name" required placeholder="e.g. Private Pool Villa, Cottage" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">URL Slug (Optional)</label>
                <input type="text" name="slug" id="type_slug" placeholder="e.g. private-pool-villa" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-sky-500 font-mono">
                <span class="text-[11px] text-slate-400">Leave blank to automatically generate from name.</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Material Icon Name</label>
                <div class="flex gap-2">
                    <input type="text" name="icon_class" id="type_icon" value="villa" placeholder="e.g. villa, pool, cottage" class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-sky-500">
                    <div id="iconPreview" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                        <span class="material-symbols-outlined text-xl">villa</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <?php foreach ($suggestedIcons as $icon): ?>
                        <button type="button" onclick="selectIcon('<?= $icon ?>')" class="px-2 py-1 rounded bg-slate-100 hover:bg-sky-50 hover:text-sky-600 text-xs font-mono transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm"><?= $icon ?></span>
                            <span><?= $icon ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" id="type_description" rows="2.5" placeholder="Brief summary of this property classification..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-sky-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Display Order</label>
                    <input type="number" name="display_order" id="type_order" value="0" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" id="type_status" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-sky-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeTypeModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold shadow-md">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('typeModal');
const modalTitle = document.getElementById('modalTitle');
const iconInput = document.getElementById('type_icon');
const iconPreview = document.querySelector('#iconPreview span');

function openTypeModal() {
    document.getElementById('type_id').value = '0';
    document.getElementById('type_name').value = '';
    document.getElementById('type_slug').value = '';
    document.getElementById('type_description').value = '';
    iconInput.value = 'villa';
    iconPreview.textContent = 'villa';
    document.getElementById('type_order').value = '0';
    document.getElementById('type_status').value = 'active';
    modalTitle.textContent = 'Add Property Type';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeTypeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function editType(type) {
    document.getElementById('type_id').value = type.id;
    document.getElementById('type_name').value = type.name;
    document.getElementById('type_slug').value = type.slug;
    document.getElementById('type_description').value = type.description || '';
    iconInput.value = type.icon_class || 'villa';
    iconPreview.textContent = type.icon_class || 'villa';
    document.getElementById('type_order').value = type.display_order || 0;
    document.getElementById('type_status').value = type.status;
    modalTitle.textContent = 'Edit Property Type';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function selectIcon(icon) {
    iconInput.value = icon;
    iconPreview.textContent = icon;
}

iconInput.addEventListener('input', (e) => {
    iconPreview.textContent = e.target.value.trim() || 'villa';
});
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>
