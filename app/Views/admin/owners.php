<?php
$pageTitle  = "Owner Management";
$activePage = "owners";

include __DIR__ . "/../Includes/admin_header.php";

// Query parameters for pagination and filtering
$currentQuery = $_GET;
unset($currentQuery['page']);
$baseUrl = "?" . http_build_query($currentQuery) . (empty($currentQuery) ? "" : "&");

$searchVal   = htmlspecialchars($_GET['q'] ?? '');
$statusVal   = strtolower($_GET['status'] ?? 'all');
$sortVal     = strtolower($_GET['sort'] ?? 'newest');
$perPageVal  = (int) ($_GET['per_page'] ?? 10);

$totalOwnersAll  = (int) ($stats['total'] ?? 0);
$activeOwners    = (int) ($stats['active'] ?? 0);
$disabledOwners  = (int) ($stats['disabled'] ?? 0);
$totalProperties = (int) ($stats['total_properties'] ?? 0);

$activePercent = $totalOwnersAll > 0 ? round(($activeOwners / $totalOwnersAll) * 100) : 0;
?>

<div class="space-y-8 pb-12">

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
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px]">groups</span>
                    Partner Host Network
                </span>
                <span class="text-xs font-semibold text-muted flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Verified Network
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                Owner Management
            </h1>
            <p class="text-muted text-sm mt-1">
                Manage registered farmhouse hosts, listing assignments, account statuses, and host communication.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <a href="<?= url('admin/owners/export') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs border border-border shadow-sm hover:border-sky/40 transition-all active:scale-95">
                <span class="material-symbols-outlined text-sky text-lg">download</span>
                Export CSV
            </a>
            <button type="button" onclick="openAddOwnerModal()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Add New Owner
            </button>
        </div>
    </div>

    <!-- ── 3. EXECUTIVE KPI BENTO GRID ───────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Owners -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-indigo-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">groups</span>
                </div>
                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    <?= $activePercent ?>% Verified
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $totalOwnersAll) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Registered Property Owners
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-indigo-50 flex items-center justify-between text-xs text-muted">
                <span>Partner Host Roster</span>
                <span class="text-ink font-bold"><?= $totalCount ?> Listed</span>
            </div>
        </div>

        <!-- Active Hosts -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-emerald-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Live Partners
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $activeOwners) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Active Verified Hosts
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-emerald-600 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Can publish & manage
                </span>
                <a href="?status=active" class="text-emerald-700 font-bold hover:underline">Filter</a>
            </div>
        </div>

        <!-- Suspended Hosts -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-rose-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">block</span>
                </div>
                <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Disabled
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $disabledOwners) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Suspended Accounts
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-rose-600 font-semibold">Login disabled</span>
                <a href="?status=disabled" class="text-rose-700 font-bold hover:underline">Filter</a>
            </div>
        </div>

        <!-- Network Inventory -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-sky/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">cottage</span>
                </div>
                <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Inventory
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $totalProperties) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Hosted Farmhouse Listings
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span>Network capacity</span>
                <a href="<?= url('admin/managefarmhouses') ?>" class="text-sky hover:text-sky-d font-bold">Manage All</a>
            </div>
        </div>

    </div>

    <!-- ── 4. FILTER & SEARCH CONTROL BAR ────────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 md:p-5 shadow-sm space-y-4">
        
        <form method="GET" action="" id="ownerFilterForm" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Search Field -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xl pointer-events-none">search</span>
                <input type="text" 
                       name="q" 
                       value="<?= $searchVal ?>" 
                       placeholder="Search by owner name, email, phone number..." 
                       class="w-full bg-surface border border-border rounded-xl py-2.5 pl-10 pr-10 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                <?php if (!empty($searchVal)): ?>
                <a href="?<?= http_build_query(array_diff_key($_GET, ['q' => ''])) ?>" 
                   class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-ink text-sm">
                    <span class="material-symbols-outlined text-base">close</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- Segmented Status Tabs -->
            <div class="flex items-center flex-wrap bg-surface border border-border rounded-xl p-1 gap-1 shrink-0">
                <button type="submit" name="status" value="all" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'all' || empty($statusVal)) ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    All (<?= $totalOwnersAll ?>)
                </button>
                <button type="submit" name="status" value="active" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'active') ? 'bg-white text-emerald-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Active (<?= $activeOwners ?>)
                </button>
                <button type="submit" name="status" value="disabled" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'disabled') ? 'bg-white text-rose-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Disabled (<?= $disabledOwners ?>)
                </button>
                <button type="submit" name="status" value="has_properties" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'has_properties') ? 'bg-white text-indigo-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    With Properties
                </button>
            </div>

            <!-- Sort & Items Per Page -->
            <div class="flex items-center gap-2 shrink-0">
                <select name="sort" onchange="document.getElementById('ownerFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="newest" <?= $sortVal === 'newest' ? 'selected' : '' ?>>Newest Registered</option>
                    <option value="oldest" <?= $sortVal === 'oldest' ? 'selected' : '' ?>>Oldest Registered</option>
                    <option value="name_asc" <?= $sortVal === 'name_asc' ? 'selected' : '' ?>>Name (A to Z)</option>
                    <option value="name_desc" <?= $sortVal === 'name_desc' ? 'selected' : '' ?>>Name (Z to A)</option>
                    <option value="most_properties" <?= $sortVal === 'most_properties' ? 'selected' : '' ?>>Most Properties</option>
                    <option value="most_bookings" <?= $sortVal === 'most_bookings' ? 'selected' : '' ?>>Most Bookings Received</option>
                </select>

                <select name="per_page" onchange="document.getElementById('ownerFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="10" <?= $perPageVal === 10 ? 'selected' : '' ?>>10 / page</option>
                    <option value="25" <?= $perPageVal === 25 ? 'selected' : '' ?>>25 / page</option>
                    <option value="50" <?= $perPageVal === 50 ? 'selected' : '' ?>>50 / page</option>
                </select>
            </div>

        </form>

        <!-- ── Bulk Action Floating Strip (Visible when items are selected) ── -->
        <div id="ownerBulkBar" class="hidden items-center justify-between bg-ink text-white p-3.5 rounded-xl shadow-lg border border-sky/30 animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky animate-pulse"></span>
                <span id="ownerSelectedCount" class="text-xs font-bold text-sky-l">0 owners selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitOwnerBulkAction('active')" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                    Activate
                </button>
                <button type="button" onclick="submitOwnerBulkAction('disabled')" 
                        class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors">
                    Disable
                </button>
                <button type="button" onclick="submitOwnerBulkDelete()" 
                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Delete
                </button>
                <button type="button" onclick="deselectAllOwners()" 
                        class="px-2.5 py-1.5 text-xs text-stone-300 hover:text-white underline">
                    Clear
                </button>
            </div>
        </div>

    </div>

    <!-- ── 5. OWNERS DATA TABLE ──────────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl border border-border shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                
                <thead>
                    <tr class="bg-surface border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" id="selectAllOwnersCheckbox" onchange="toggleSelectAllOwners(this)" 
                                   class="rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-4 px-4">Owner Profile</th>
                        <th class="py-4 px-4">Contact Details</th>
                        <th class="py-4 px-4 text-center">Hosted Listings</th>
                        <th class="py-4 px-4">Account Status</th>
                        <th class="py-4 px-4">Last Activity</th>
                        <th class="py-4 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border/60 text-sm">
                    <?php if (!empty($owners)): ?>
                        <?php foreach ($owners as $owner): 
                            $encId     = $owner['encrypted_id'];
                            $name      = $owner['name'] ?? 'Partner Host';
                            $email     = $owner['email'] ?? 'No Email';
                            $phone     = $owner['phone'] ?? 'N/A';
                            $status    = strtolower($owner['status'] ?? 'active');
                            $isActive  = ($status === 'active');
                            $isOnline  = (bool) ($owner['is_online'] ?? false);
                            $propCount = (int) ($owner['property_count'] ?? 0);
                            $cleanDial = preg_replace('/[^0-9]/', '', $phone);
                            
                            $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr(explode(' ', $name)[1], 0, 1) : ''));
                            $joinDate = !empty($owner['created_at']) ? date('d M Y', strtotime($owner['created_at'])) : '—';
                            $lastLogin = !empty($owner['last_login']) ? date('d M Y, h:i A', strtotime($owner['last_login'])) : 'Never logged in';

                            $safeOwner = $owner;
                            unset($safeOwner['password'], $safeOwner['remember_token'], $safeOwner['reset_token']);
                            $ownerJson = htmlspecialchars(json_encode($safeOwner), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="hover:bg-surface/50 transition-colors owner-row-item" data-uid="<?= $encId ?>">
                            
                            <!-- Checkbox -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="<?= $encId ?>" class="owner-checkbox rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer" onchange="onOwnerRowCheckboxChange()">
                            </td>

                            <!-- Owner Profile -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-100 to-sky-l border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs shadow-sm">
                                            <?= $initials ?>
                                        </div>
                                        <?php if ($isOnline): ?>
                                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <button type="button" onclick="inspectOwner('<?= $encId ?>')" class="font-bold text-ink hover:text-sky transition-colors truncate block text-left">
                                            <?= htmlspecialchars($name) ?>
                                        </button>
                                        <div class="flex items-center gap-2 text-[11px] text-muted mt-0.5">
                                            <span class="font-semibold text-indigo-700">Host ID #<?= $owner['id'] ?></span>
                                            <span>•</span>
                                            <span>Joined <?= $joinDate ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Details -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    <div class="text-xs text-ink font-semibold flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-muted text-[14px]">mail</span>
                                        <span class="truncate max-w-[180px]"><?= htmlspecialchars($email) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-muted font-medium flex items-center gap-1">
                                            <span class="material-symbols-outlined text-muted text-[14px]">call</span>
                                            <?= htmlspecialchars($phone) ?>
                                        </span>
                                        <?php if (!empty($cleanDial)): ?>
                                        <a href="https://wa.me/91<?= htmlspecialchars($cleanDial) ?>" target="_blank" 
                                           class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-colors shadow-xs" title="Message WhatsApp">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Hosted Listings -->
                            <td class="py-4 px-4 text-center">
                                <a href="<?= url('admin/managefarmhouses?owner_id=' . $owner['id']) ?>" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all <?= $propCount > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100' : 'bg-surface text-muted border border-border' ?>" 
                                   title="View owner's properties">
                                    <span class="material-symbols-outlined text-sm">cottage</span>
                                    <?= $propCount ?> <?= $propCount === 1 ? 'Estate' : 'Estates' ?>
                                </a>
                            </td>

                            <!-- Account Status -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    <?php if ($isActive): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                            Disabled
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Last Activity -->
                            <td class="py-4 px-4 text-xs text-muted font-medium">
                                <?= $lastLogin ?>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    
                                    <!-- Inspect Host Dossier -->
                                    <button type="button" onclick="inspectOwner('<?= $encId ?>')" 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-xs" 
                                            title="View Host Dossier & Listings">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>

                                    <!-- Edit Owner -->
                                    <button type="button" onclick='openEditOwnerModal(<?= $ownerJson ?>)' 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-xs" 
                                            title="Edit Owner Details">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>

                                    <!-- Force Logout (if online) -->
                                    <?php if ($isOnline): ?>
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Terminate this host\'s active session?')">
                                        <input type="hidden" name="action" value="force_logout">
                                        <input type="hidden" name="owner_id" value="<?= $encId ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition-colors shadow-xs" title="Force Logout Session">
                                            <span class="material-symbols-outlined text-lg">logout</span>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Toggle Active / Disabled -->
                                    <form method="POST" action="" class="inline">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="owner_id" value="<?= $encId ?>">
                                        <input type="hidden" name="status" value="<?= $isActive ? 'disabled' : 'active' ?>">
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink flex items-center justify-center transition-colors shadow-xs" 
                                                title="<?= $isActive ? 'Disable Host Account' : 'Activate Host Account' ?>">
                                            <span class="material-symbols-outlined text-lg <?= $isActive ? 'text-rose-500' : 'text-emerald-600' ?>">
                                                <?= $isActive ? 'block' : 'check_circle' ?>
                                            </span>
                                        </button>
                                    </form>

                                    <!-- Delete Permanently -->
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Permanently delete <?= htmlspecialchars(addslashes($name)) ?>? Any assigned farmhouses will become unassigned.')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="owner_id" value="<?= $encId ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-xs" title="Delete Owner">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center text-muted">
                                <span class="material-symbols-outlined text-5xl text-muted/40 mb-2 block">person_off</span>
                                <h4 class="font-bold text-ink text-base">No Owners Found</h4>
                                <p class="text-xs text-muted mt-1 max-w-sm mx-auto">
                                    No host accounts match your current filter and search criteria.
                                </p>
                                <a href="?" class="inline-flex items-center gap-1 mt-4 px-4 py-2 rounded-xl bg-surface border border-border text-xs font-bold text-ink hover:bg-sky-xl hover:text-sky transition-colors">
                                    Reset Filters
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        <!-- ── 6. PAGINATION CONTROLS ────────────────────────────────────────── -->
        <?php if ($totalPages > 1): 
            $startItem = ($currentPage - 1) * $perPageVal + 1;
            $endItem   = min($currentPage * $perPageVal, $totalCount);
        ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-surface/40 border-t border-border">
            
            <div class="text-xs text-muted font-medium order-2 sm:order-1">
                Showing <span class="text-ink font-bold"><?= $startItem ?></span> to 
                <span class="text-ink font-bold"><?= $endItem ?></span> of 
                <span class="text-ink font-bold"><?= $totalCount ?></span> owners
            </div>

            <nav class="flex items-center gap-1.5 order-1 sm:order-2">
                
                <!-- Prev Button -->
                <a href="<?= $currentPage > 1 ? $baseUrl . 'page=' . ($currentPage - 1) : '#' ?>" 
                   class="w-9 h-9 flex items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-sky-xl hover:text-sky transition-all <?= $currentPage <= 1 ? 'pointer-events-none opacity-40' : '' ?>">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </a>

                <!-- Numbered Page Pills -->
                <div class="flex items-center gap-1">
                    <?php
                    $startP = max(1, $currentPage - 2);
                    $endP   = min($totalPages, $currentPage + 2);
                    for ($p = $startP; $p <= $endP; $p++): 
                        $isPActive = ($p === $currentPage);
                    ?>
                        <a href="<?= $baseUrl ?>page=<?= $p ?>" 
                           class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all
                           <?= $isPActive ? 'bg-sky text-white shadow-md shadow-sky/25' : 'bg-white border border-border text-muted hover:border-sky/50 hover:text-sky' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Next Button -->
                <a href="<?= $currentPage < $totalPages ? $baseUrl . 'page=' . ($currentPage + 1) : '#' ?>" 
                   class="w-9 h-9 flex items-center justify-center rounded-xl border border-border bg-white text-muted hover:bg-sky-xl hover:text-sky transition-all <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </a>

            </nav>

        </div>
        <?php endif; ?>

    </div>

</div>

<!-- ── 7. ADD OWNER MODAL ────────────────────────────────────────────────── -->
<div id="addOwnerModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center pb-4 mb-6 border-b border-border">
            <div>
                <h3 class="text-xl font-bold text-ink">Register New Host</h3>
                <p class="text-xs text-muted mt-0.5">Create a partner estate owner account with listing capabilities.</p>
            </div>
            <button type="button" onclick="closeAddOwnerModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="add_owner">

            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Owner / Host Name *</label>
                <input name="name" type="text" required placeholder="e.g. Vikramaditya Singh" 
                       class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Email Address *</label>
                    <input name="email" type="email" required placeholder="vikram@farmlelo.com" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Phone Number *</label>
                    <input name="phone" type="text" required placeholder="9876543210" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Set Password *</label>
                    <div class="relative">
                        <input name="password" id="add_owner_pw" type="password" required placeholder="Min. 6 chars" 
                               class="w-full px-4 py-3 pr-10 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <button type="button" onclick="togglePw('add_owner_pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Account Status</label>
                    <select name="status" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="active" selected>Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky to-sky-d text-white font-bold rounded-xl shadow-lg shadow-sky/25 hover:shadow-sky/40 transition-all active:scale-98">
                    Register Owner Account
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ── 8. EDIT OWNER MODAL ────────────────────────────────────────────────── -->
<div id="editOwnerModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center pb-4 mb-6 border-b border-border">
            <div>
                <h3 class="text-xl font-bold text-ink">Edit Owner Details</h3>
                <p class="text-xs text-muted mt-0.5">Modify contact information or reset credentials.</p>
            </div>
            <button type="button" onclick="closeEditOwnerModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="edit_owner">
            <input type="hidden" name="owner_id" id="edit_owner_id">

            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Full Name *</label>
                <input name="name" id="edit_owner_name" type="text" required 
                       class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Email Address *</label>
                    <input name="email" id="edit_owner_email" type="email" required 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Phone Number *</label>
                    <input name="phone" id="edit_owner_phone" type="text" required 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Account Status</label>
                    <select name="status" id="edit_owner_status" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="active">Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Change Password (optional)</label>
                    <div class="relative">
                        <input name="password" id="edit_owner_pw" type="password" placeholder="Leave blank to keep" 
                               class="w-full px-4 py-3 pr-10 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <button type="button" onclick="togglePw('edit_owner_pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky to-sky-d text-white font-bold rounded-xl shadow-lg shadow-sky/25 hover:shadow-sky/40 transition-all active:scale-98">
                    Save Owner Changes
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ── 9. OWNER DOSSIER / INSPECTOR MODAL ─────────────────────────────────── -->
<div id="inspectOwnerModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-2xl p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto space-y-6">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-start pb-4 border-b border-border">
            <div class="flex items-center gap-4">
                <div id="owner_dossier_avatar" class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xl flex items-center justify-center shadow-sm">
                    H
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 id="owner_dossier_name" class="text-xl font-extrabold text-ink">Host Name</h3>
                        <span id="owner_dossier_status_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Active
                        </span>
                    </div>
                    <p id="owner_dossier_email_phone" class="text-xs text-muted mt-0.5">owner@example.com • 9876543210</p>
                </div>
            </div>
            
            <button type="button" onclick="closeInspectOwnerModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <!-- Quick Communication Action Buttons -->
        <div class="grid grid-cols-2 gap-3">
            <a id="owner_dossier_whatsapp_btn" href="#" target="_blank" 
               class="py-2.5 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-xs">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                WhatsApp Host
            </a>
            <a id="owner_dossier_call_btn" href="#" 
               class="py-2.5 px-4 rounded-xl bg-sky-l hover:bg-sky text-sky-d hover:text-white border border-sky/20 text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-xs">
                <span class="material-symbols-outlined text-sm">call</span>
                Direct Phone Call
            </a>
        </div>

        <!-- Metadata Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-surface/60 border border-border text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Partner ID</span>
                <span id="owner_dossier_id" class="font-bold text-ink mt-0.5 block">#1</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Joined Date</span>
                <span id="owner_dossier_joined" class="font-bold text-ink mt-0.5 block">21 Aug 2026</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Total Estates</span>
                <span id="owner_dossier_prop_count" class="font-bold text-indigo-700 mt-0.5 block">0 Listed</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Total Bookings</span>
                <span id="owner_dossier_booking_count" class="font-bold text-emerald-600 mt-0.5 block">0 Received</span>
            </div>
        </div>

        <!-- Listed Farmhouses Showcase -->
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-border">
                <h4 class="text-xs font-bold uppercase tracking-wider text-ink flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base text-sky">cottage</span>
                    Host's Listed Farmhouses
                </h4>
                <span id="owner_dossier_prop_badge" class="text-xs text-muted font-semibold">0 Properties</span>
            </div>

            <div id="owner_dossier_properties_list" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-1">
                <!-- Dynamic -->
            </div>
        </div>

        <!-- Footer Action -->
        <div class="pt-4 border-t border-border flex justify-end">
            <button type="button" onclick="closeInspectOwnerModal()" class="px-5 py-2.5 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                Close Dossier
            </button>
        </div>

    </div>
</div>

<!-- Hidden Bulk Action Submission Form -->
<form id="ownerBulkActionForm" method="POST" action="" class="hidden">
    <input type="hidden" name="action" id="owner_bulk_action_type">
    <input type="hidden" name="target_status" id="owner_bulk_target_status">
    <div id="owner_bulk_ids_container"></div>
</form>

<!-- ── 10. JAVASCRIPT LOGIC ──────────────────────────────────────────────── -->
<script>
    // Modal Helpers
    function openAddOwnerModal()  { document.getElementById('addOwnerModal').classList.remove('hidden'); }
    function closeAddOwnerModal() { document.getElementById('addOwnerModal').classList.add('hidden'); }
    function closeEditOwnerModal(){ document.getElementById('editOwnerModal').classList.add('hidden'); }
    function closeInspectOwnerModal() { document.getElementById('inspectOwnerModal').classList.add('hidden'); }

    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        const icon = btn.querySelector('.material-symbols-outlined');
        inp.type = inp.type === 'password' ? 'text' : 'password';
        icon.textContent = inp.type === 'password' ? 'visibility' : 'visibility_off';
    }

    function openEditOwnerModal(o) {
        document.getElementById('edit_owner_id').value = o.encrypted_id;
        document.getElementById('edit_owner_name').value = o.name ?? '';
        document.getElementById('edit_owner_email').value = o.email ?? '';
        document.getElementById('edit_owner_phone').value = o.phone ?? '';
        document.getElementById('edit_owner_status').value = o.status ?? 'active';
        document.getElementById('edit_owner_pw').value = '';

        document.getElementById('editOwnerModal').classList.remove('hidden');
    }

    // Inspect Host Dossier
    async function inspectOwner(encryptedId) {
        try {
            const res = await fetch(`<?= url('admin/owners/details') ?>?id=${encodeURIComponent(encryptedId)}`);
            const json = await res.json();
            
            if (!json.success) {
                alert(json.message || 'Failed to load host details.');
                return;
            }

            const d = json.data;
            const o = d.owner;
            const initials = o.name ? o.name.charAt(0).toUpperCase() : 'H';

            document.getElementById('owner_dossier_avatar').textContent = initials;
            document.getElementById('owner_dossier_name').textContent = o.name || 'Partner Host';
            document.getElementById('owner_dossier_email_phone').textContent = `${o.email || 'No Email'} • ${o.phone || 'No Phone'}`;
            document.getElementById('owner_dossier_id').textContent = `#${o.id}`;
            document.getElementById('owner_dossier_joined').textContent = o.formatted_joined;
            document.getElementById('owner_dossier_prop_count').textContent = `${d.properties.length} Listed`;
            document.getElementById('owner_dossier_booking_count').textContent = `${d.total_bookings} Stays`;
            document.getElementById('owner_dossier_prop_badge').textContent = `${d.properties.length} Estates`;

            // Status Badge
            const statusBadge = document.getElementById('owner_dossier_status_badge');
            if (o.status === 'active') {
                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
                statusBadge.textContent = 'Active';
            } else {
                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200';
                statusBadge.textContent = 'Disabled';
            }

            // Quick Connect Links
            const cleanPhone = (o.phone || '').replace(/[^0-9]/g, '');
            document.getElementById('owner_dossier_whatsapp_btn').href = `https://wa.me/91${cleanPhone}?text=Hello%20${encodeURIComponent(o.name || '')},%20contacting%20from%20Farmlelo%20Partner%20Support.`;
            document.getElementById('owner_dossier_call_btn').href = `tel:${cleanPhone}`;

            // Properties List
            const pList = document.getElementById('owner_dossier_properties_list');
            if (d.properties.length > 0) {
                pList.innerHTML = d.properties.map(p => `
                    <div class="p-3 rounded-2xl bg-surface border border-border flex items-center gap-3 text-xs hover:border-sky/40 transition-colors">
                        <img src="${p.thumb_url || 'https://placehold.co/100x100/E6F6FD/16A5DE?text=Farm'}" class="w-12 h-12 rounded-xl object-cover bg-stone-100 shrink-0">
                        <div class="min-w-0 flex-1">
                            <h5 class="font-bold text-ink truncate">${p.title || 'Untitled Estate'}</h5>
                            <p class="text-muted text-[11px] truncate">${p.location || 'Location not set'}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-ink font-extrabold">₹${Number(p.price || 0).toLocaleString()}<span class="text-[10px] text-muted font-normal">/nt</span></span>
                                <a href="<?= url('admin/editfarm?id=') ?>${p.id}" class="text-sky font-bold text-[11px] hover:underline flex items-center gap-0.5">Edit <span class="material-symbols-outlined text-[13px]">arrow_forward</span></a>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                pList.innerHTML = `<p class="text-center col-span-2 py-6 text-xs text-muted">No farmhouses listed under this host yet.</p>`;
            }

            document.getElementById('inspectOwnerModal').classList.remove('hidden');

        } catch (err) {
            alert('Error connecting to host details service.');
        }
    }

    // ── Bulk Selection Helpers ──
    function toggleSelectAllOwners(master) {
        const checkboxes = document.querySelectorAll('.owner-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateOwnerBulkBar();
    }

    function onOwnerRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.owner-checkbox');
        const master = document.getElementById('selectAllOwnersCheckbox');
        const checked = document.querySelectorAll('.owner-checkbox:checked');
        master.checked = (checkboxes.length > 0 && checked.length === checkboxes.length);
        updateOwnerBulkBar();
    }

    function updateOwnerBulkBar() {
        const checked = document.querySelectorAll('.owner-checkbox:checked');
        const bar = document.getElementById('ownerBulkBar');
        const countSpan = document.getElementById('ownerSelectedCount');

        if (checked.length > 0) {
            countSpan.textContent = `${checked.length} owner${checked.length === 1 ? '' : 's'} selected`;
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function deselectAllOwners() {
        document.querySelectorAll('.owner-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAllOwnersCheckbox').checked = false;
        updateOwnerBulkBar();
    }

    function submitOwnerBulkAction(targetStatus) {
        const checked = document.querySelectorAll('.owner-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Update status of ${checked.length} owner(s) to '${targetStatus}'?`)) return;

        const form = document.getElementById('ownerBulkActionForm');
        const container = document.getElementById('owner_bulk_ids_container');
        container.innerHTML = '';

        document.getElementById('owner_bulk_action_type').value = 'bulk_status';
        document.getElementById('owner_bulk_target_status').value = targetStatus;

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'owner_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }

    function submitOwnerBulkDelete() {
        const checked = document.querySelectorAll('.owner-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Permanently delete ${checked.length} owner(s)? Any assigned farmhouses will become unassigned.`)) return;

        const form = document.getElementById('ownerBulkActionForm');
        const container = document.getElementById('owner_bulk_ids_container');
        container.innerHTML = '';

        document.getElementById('owner_bulk_action_type').value = 'bulk_delete';

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'owner_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>