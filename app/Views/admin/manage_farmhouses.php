<?php
$pageTitle  = "Properties Management";
$activePage = "properties";

include __DIR__ . "/../Includes/admin_header.php";

// Query parameters for pagination and filtering
$currentQuery = $_GET;
unset($currentQuery['page']);
$baseUrl = "?" . http_build_query($currentQuery) . (empty($currentQuery) ? "" : "&");

$searchVal   = htmlspecialchars($_GET['q'] ?? '');
$statusVal   = strtolower($_GET['status'] ?? 'all');
$ownerVal    = trim($_GET['owner_id'] ?? '');
$sortVal     = strtolower($_GET['sort'] ?? 'newest');
$perPageVal  = (int) ($_GET['per_page'] ?? 12);
$viewMode    = $_GET['view'] ?? 'grid'; // 'grid' or 'table'

$totalAll    = (int) ($stats['total'] ?? 0);
$activeCount = (int) ($stats['active'] ?? 0);
$pendingCount= (int) ($stats['pending'] ?? 0);
$rejectCount = (int) ($stats['rejected'] ?? 0);

$activePercent = $totalAll > 0 ? round(($activeCount / $totalAll) * 100) : 0;
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
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-l border border-sky/20 text-sky-d text-xs font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px]">villa</span>
                    Estate Catalog
                </span>
                <span class="text-xs font-semibold text-muted flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Inventory
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                Properties Management
            </h1>
            <p class="text-muted text-sm mt-1">
                Supervise farmhouse estates, guest capacity, pricing structures, owner assignments, and visibility.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <a href="<?= url('admin/managefarmhouses/export') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs border border-border shadow-sm hover:border-sky/40 transition-all active:scale-95">
                <span class="material-symbols-outlined text-sky text-lg">download</span>
                Export CSV
            </a>
            <a href="<?= url('admin/addfarm') ?>" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add</span>
                Add New Property
            </a>
        </div>
    </div>

    <!-- ── 3. EXECUTIVE KPI BENTO GRID ───────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Properties -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-sky/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">home_work</span>
                </div>
                <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2.5 py-1 rounded-md uppercase tracking-wider">
                    <?= $activePercent ?>% Published
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $totalAll) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Total Listed Estates
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-sky-50 flex items-center justify-between text-xs text-muted">
                <span>Platform inventory</span>
                <span class="text-ink font-bold"><?= $totalCount ?> Displayed</span>
            </div>
        </div>

        <!-- Active Properties -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-emerald-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Bookable
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $activeCount) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Active & Live Online
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-emerald-600 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Visible to guests
                </span>
                <a href="?status=active" class="text-emerald-700 font-bold hover:underline">Filter</a>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-amber-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">pending_actions</span>
                </div>
                <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Needs Review
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $pendingCount) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Pending Verification
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-amber-600 font-semibold">Awaiting review</span>
                <a href="?status=pending" class="text-amber-700 font-bold hover:underline">Inspect</a>
            </div>
        </div>

        <!-- Suspended / Rejected -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-rose-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">pause_circle</span>
                </div>
                <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Paused
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $rejectCount) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Deactivated Listings
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-rose-600 font-semibold">Hidden from guests</span>
                <a href="?status=rejected" class="text-rose-700 font-bold hover:underline">Filter</a>
            </div>
        </div>

    </div>

    <!-- ── 4. FILTER, SEARCH & VIEW CONTROL BAR ──────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 md:p-5 shadow-sm space-y-4">
        
        <form method="GET" action="" id="propertyFilterForm" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <input type="hidden" name="view" id="filter_view_mode" value="<?= htmlspecialchars($viewMode) ?>">

            <!-- Search Field -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xl pointer-events-none">search</span>
                <input type="text" 
                       name="q" 
                       value="<?= $searchVal ?>" 
                       placeholder="Search by farmhouse title, location, address, owner..." 
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
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'all' || empty($statusVal)) ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    All (<?= $totalAll ?>)
                </button>
                <button type="submit" name="status" value="active" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'active') ? 'bg-white text-emerald-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Active (<?= $activeCount ?>)
                </button>
                <button type="submit" name="status" value="pending" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'pending') ? 'bg-white text-amber-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Pending (<?= $pendingCount ?>)
                </button>
                <button type="submit" name="status" value="rejected" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'rejected') ? 'bg-white text-rose-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Paused (<?= $rejectCount ?>)
                </button>
            </div>

            <!-- Category Filter -->
            <div class="shrink-0">
                <select name="category" onchange="document.getElementById('propertyFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="">All Categories</option>
                    <?php 
                    $catOptions = ['Guest House', 'Resort', 'Farmhouse', 'Villa'];
                    $selectedCat = $currentCategoryFilter ?? '';
                    foreach ($catOptions as $c): ?>
                        <option value="<?= $c ?>" <?= ($selectedCat === $c) ? 'selected' : '' ?>>
                            Category: <?= $c ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Owner Filter -->
            <?php if (!empty($owners)): ?>
            <div class="shrink-0">
                <select name="owner_id" onchange="document.getElementById('propertyFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="">All Partner Hosts</option>
                    <?php foreach ($owners as $ow): ?>
                        <option value="<?= $ow['id'] ?>" <?= ($ownerVal === (string)$ow['id']) ? 'selected' : '' ?>>
                            Host: <?= htmlspecialchars($ow['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Sort & Items Per Page -->
            <div class="flex items-center gap-2 shrink-0">
                <select name="sort" onchange="document.getElementById('propertyFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="newest" <?= $sortVal === 'newest' ? 'selected' : '' ?>>Newest Listed</option>
                    <option value="oldest" <?= $sortVal === 'oldest' ? 'selected' : '' ?>>Oldest Listed</option>
                    <option value="price_asc" <?= $sortVal === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sortVal === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="most_bookings" <?= $sortVal === 'most_bookings' ? 'selected' : '' ?>>Most Bookings</option>
                    <option value="title_asc" <?= $sortVal === 'title_asc' ? 'selected' : '' ?>>Title (A to Z)</option>
                </select>

                <!-- Grid / Table View Switcher -->
                <div class="flex items-center bg-surface border border-border rounded-xl p-1">
                    <button type="button" onclick="switchView('grid')" 
                            class="p-1.5 rounded-lg transition-colors <?= $viewMode === 'grid' ? 'bg-white text-sky shadow-xs' : 'text-muted hover:text-ink' ?>" title="Grid Card View">
                        <span class="material-symbols-outlined text-lg">grid_view</span>
                    </button>
                    <button type="button" onclick="switchView('table')" 
                            class="p-1.5 rounded-lg transition-colors <?= $viewMode === 'table' ? 'bg-white text-sky shadow-xs' : 'text-muted hover:text-ink' ?>" title="Table View">
                        <span class="material-symbols-outlined text-lg">table_rows</span>
                    </button>
                </div>
            </div>

        </form>

        <!-- ── Bulk Action Floating Strip (Visible when items are selected in Table View) ── -->
        <div id="propertyBulkBar" class="hidden items-center justify-between bg-ink text-white p-3.5 rounded-xl shadow-lg border border-sky/30 animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky animate-pulse"></span>
                <span id="propertySelectedCount" class="text-xs font-bold text-sky-l">0 properties selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitPropertyBulkAction('active')" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                    Activate
                </button>
                <button type="button" onclick="submitPropertyBulkAction('rejected')" 
                        class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors">
                    Pause / Reject
                </button>
                <button type="button" onclick="submitPropertyBulkDelete()" 
                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Delete
                </button>
                <button type="button" onclick="deselectAllProperties()" 
                        class="px-2.5 py-1.5 text-xs text-stone-300 hover:text-white underline">
                    Clear
                </button>
            </div>
        </div>

    </div>

    <!-- ── 5. PROPERTIES DISPLAY (GRID OR TABLE) ─────────────────────────────── -->

    <?php if (empty($farmhouses)): ?>
        <div class="bg-white rounded-3xl border border-border p-16 text-center shadow-sm">
            <span class="material-symbols-outlined text-6xl text-muted/40 mb-3 block">cottage</span>
            <h3 class="text-lg font-bold text-ink mb-1">No Properties Found</h3>
            <p class="text-xs text-muted max-w-md mx-auto mb-6">
                There are no property listings matching your selected search query and filter criteria.
            </p>
            <div class="flex justify-center gap-3">
                <a href="?" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-surface border border-border text-xs font-bold text-ink hover:bg-sky-xl hover:text-sky transition-colors">
                    Reset Filters
                </a>
                <a href="<?= url('admin/addfarm') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white text-xs font-bold shadow-md shadow-sky/20">
                    <span class="material-symbols-outlined text-base">add</span> Add First Property
                </a>
            </div>
        </div>

    <?php elseif ($viewMode === 'grid'): ?>
        
        <!-- ── GRID CARD VIEW ── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($farmhouses as $farm):
                $encId      = $farm['encrypted_id'];
                $status     = strtolower($farm['status'] ?? 'pending');
                $isActive   = ($status === 'active');
                $isPending  = ($status === 'pending');
                $price      = (float)($farm['price'] ?? 0);
                $thumb      = farmhouse_img_url($farm['thumb_url'] ?? null);
            ?>
            <div class="bg-white rounded-3xl border border-border overflow-hidden shadow-sm hover:shadow-xl hover:border-sky/40 transition-all duration-300 group flex flex-col justify-between">
                
                <!-- Image Header -->
                <div class="relative h-48 overflow-hidden bg-surface">
                    <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($farm['title']) ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         onerror="this.src='https://placehold.co/600x400/E6F6FD/16A5DE?text=Farmlelo+Estate'">
                    
                    <!-- Gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-ink/60 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>

                    <!-- Top Badges -->
                    <div class="absolute top-3 left-3 flex items-center flex-wrap gap-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-sky-500/90 text-white backdrop-blur-xs shadow-xs">
                            <?= htmlspecialchars($farm['category'] ?? 'Farmhouse') ?>
                        </span>

                        <?php if ($isActive): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-emerald-500/90 text-white backdrop-blur-xs shadow-xs">
                                Active
                            </span>
                        <?php elseif ($isPending): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-amber-500/90 text-white backdrop-blur-xs shadow-xs">
                                Pending
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-rose-500/90 text-white backdrop-blur-xs shadow-xs">
                                Paused
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($farm['is_negotiable'])): ?>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white/90 text-indigo-700 backdrop-blur-xs">
                                Negotiable
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Nightly Rate Badge -->
                    <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-xl shadow-md border border-white/40">
                        <span class="text-xs font-black text-ink">₹<?= number_format($price) ?></span>
                        <span class="text-[10px] font-semibold text-muted">/night</span>
                    </div>

                    <!-- Total Bookings Counter -->
                    <div class="absolute bottom-3 left-3 flex items-center gap-1 text-[11px] font-bold text-white drop-shadow">
                        <span class="material-symbols-outlined text-[14px]">event_available</span>
                        <?= (int)($farm['booking_count'] ?? 0) ?> bookings
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                    
                    <div>
                        <!-- Title & Location -->
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-bold text-ink text-base line-clamp-1 group-hover:text-sky transition-colors">
                                <?= htmlspecialchars($farm['title']) ?>
                            </h3>
                        </div>
                        
                        <p class="text-xs text-muted flex items-center gap-1 mt-1 truncate">
                            <span class="material-symbols-outlined text-[14px] text-muted shrink-0">location_on</span>
                            <?= htmlspecialchars($farm['location'] ?? 'Location not set') ?>
                        </p>

                        <!-- Key Specs Pills -->
                        <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-border/60 text-center">
                            <div class="bg-surface rounded-xl p-1.5">
                                <span class="text-xs font-bold text-ink block"><?= $farm['bedrooms'] ?? 1 ?> BHK</span>
                                <span class="text-[10px] text-muted font-medium">Beds</span>
                            </div>
                            <div class="bg-surface rounded-xl p-1.5">
                                <span class="text-xs font-bold text-ink block"><?= $farm['day_capacity'] ?? '—' ?></span>
                                <span class="text-[10px] text-muted font-medium">Day Max</span>
                            </div>
                            <div class="bg-surface rounded-xl p-1.5">
                                <span class="text-xs font-bold text-ink block"><?= $farm['night_capacity'] ?? '—' ?></span>
                                <span class="text-[10px] text-muted font-medium">Night Max</span>
                            </div>
                        </div>

                        <!-- Host Attribution -->
                        <div class="flex items-center justify-between mt-3 text-xs text-muted">
                            <span class="truncate flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">person</span>
                                Host: <strong class="text-ink truncate max-w-[110px]"><?= htmlspecialchars($farm['owner_name'] ?? 'Unassigned') ?></strong>
                            </span>
                            <span class="text-[11px] text-muted">ID #<?= $farm['id'] ?></span>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="flex items-center gap-2 pt-3 border-t border-border/60">
                        
                        <!-- Inspect Button -->
                        <button type="button" onclick="inspectProperty('<?= $encId ?>')" 
                                class="flex-1 py-2 px-3 rounded-xl bg-surface hover:bg-sky-xl text-ink hover:text-sky text-xs font-bold flex items-center justify-center gap-1 transition-colors border border-border shadow-2xs" title="Preview Estate">
                            <span class="material-symbols-outlined text-[15px]">visibility</span> Preview
                        </button>

                        <!-- Edit Button -->
                        <a href="<?= url('admin/editfarm?id=' . urlencode($encId)) ?>" 
                           class="py-2 px-3 rounded-xl bg-sky-l hover:bg-sky text-sky-d hover:text-white text-xs font-bold flex items-center justify-center gap-1 transition-colors shadow-2xs" title="Edit Listing Details">
                            <span class="material-symbols-outlined text-[15px]">edit</span> Edit
                        </a>

                        <!-- Toggle Status -->
                        <?php $nextStatus = $isActive ? 'rejected' : 'active'; ?>
                        <form method="POST" action="<?= url('admin/managefarmhouses') ?>" class="inline">
                            <input type="hidden" name="action" value="toggle_status">
                            <input type="hidden" name="farmhouse_id" value="<?= htmlspecialchars($encId) ?>">
                            <input type="hidden" name="status" value="<?= $nextStatus ?>">
                            <button type="submit" 
                                    class="w-9 h-9 rounded-xl bg-surface hover:bg-sky-xl text-ink flex items-center justify-center transition-colors border border-border shadow-2xs" 
                                    title="<?= $isActive ? 'Deactivate Listing' : 'Publish Listing Online' ?>">
                                <span class="material-symbols-outlined text-[17px] <?= $isActive ? 'text-rose-500' : 'text-emerald-600' ?>">
                                    <?= $isActive ? 'pause_circle' : 'play_circle' ?>
                                </span>
                            </button>
                        </form>

                        <!-- Delete Button -->
                        <button type="button" onclick="openDeleteFarmhouseModal('<?= htmlspecialchars($encId) ?>', '<?= addslashes(htmlspecialchars($farm['title'])) ?>')" 
                                class="w-9 h-9 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Delete Estate">
                            <span class="material-symbols-outlined text-[17px]">delete</span>
                        </button>

                    </div>

                </div>

            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <!-- ── TABLE VIEW ── -->
        <div class="bg-white rounded-3xl border border-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    
                    <thead>
                        <tr class="bg-surface border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                            <th class="py-4 px-4 w-12 text-center">
                                <input type="checkbox" id="selectAllPropertiesCheckbox" onchange="toggleSelectAllProperties(this)" 
                                       class="rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer">
                            </th>
                            <th class="py-4 px-4">Farmhouse Estate</th>
                            <th class="py-4 px-4">Partner Host</th>
                            <th class="py-4 px-4 text-center">Nightly Rate</th>
                            <th class="py-4 px-4 text-center">Capacity</th>
                            <th class="py-4 px-4">Listing Status</th>
                            <th class="py-4 px-4 text-center">Bookings</th>
                            <th class="py-4 px-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-border/60 text-sm">
                        <?php foreach ($farmhouses as $farm):
                            $encId     = $farm['encrypted_id'];
                            $status    = strtolower($farm['status'] ?? 'pending');
                            $isActive  = ($status === 'active');
                            $isPending = ($status === 'pending');
                            $price     = (float)($farm['price'] ?? 0);
                            $thumb     = farmhouse_img_url($farm['thumb_url'] ?? null);
                        ?>
                        <tr class="hover:bg-surface/50 transition-colors">
                            
                            <!-- Checkbox -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="<?= $encId ?>" class="property-checkbox rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer" onchange="onPropertyRowCheckboxChange()">
                            </td>

                            <!-- Estate Profile -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= $thumb ?>" class="w-12 h-12 rounded-xl object-cover bg-surface shrink-0 shadow-2xs" onerror="this.src='https://placehold.co/120x120/E6F6FD/16A5DE?text=Farm'">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button type="button" onclick="inspectProperty('<?= $encId ?>')" class="font-bold text-ink hover:text-sky transition-colors truncate block text-left">
                                                <?= htmlspecialchars($farm['title']) ?>
                                            </button>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                <?= htmlspecialchars($farm['category'] ?? 'Farmhouse') ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[11px] text-muted mt-0.5">
                                            <span class="material-symbols-outlined text-[13px]">location_on</span>
                                            <span class="truncate max-w-[200px]"><?= htmlspecialchars($farm['location'] ?? 'Location not set') ?></span>
                                            <span>•</span>
                                            <span>#<?= $farm['id'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Host -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-semibold text-ink"><?= htmlspecialchars($farm['owner_name'] ?? 'Unassigned') ?></div>
                                <div class="text-[11px] text-muted"><?= htmlspecialchars($farm['owner_phone'] ?? 'N/A') ?></div>
                            </td>

                            <!-- Price -->
                            <td class="py-4 px-4 text-center">
                                <div class="text-xs font-extrabold text-ink">₹<?= number_format($price) ?></div>
                                <div class="text-[10px] text-muted font-normal">per night</div>
                            </td>

                            <!-- Capacity -->
                            <td class="py-4 px-4 text-center text-xs">
                                <span class="font-bold text-ink"><?= $farm['bedrooms'] ?? 1 ?> BHK</span>
                                <div class="text-[10px] text-muted font-medium"><?= $farm['day_capacity'] ?? '—' ?> Day / <?= $farm['night_capacity'] ?? '—' ?> Nt</div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                <?php if ($isActive): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                <?php elseif ($isPending): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        Paused
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Bookings -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <span class="material-symbols-outlined text-[13px]">event</span>
                                    <?= (int)($farm['booking_count'] ?? 0) ?>
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    
                                    <!-- Inspect Preview -->
                                    <button type="button" onclick="inspectProperty('<?= $encId ?>')" 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-2xs" title="Preview Estate">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>

                                    <!-- Edit -->
                                    <a href="<?= url('admin/editfarm?id=' . urlencode($encId)) ?>" 
                                       class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-2xs" title="Edit Listing">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>

                                    <!-- Toggle Status -->
                                    <?php $nextStatus = $isActive ? 'rejected' : 'active'; ?>
                                    <form method="POST" action="<?= url('admin/managefarmhouses') ?>" class="inline">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="farmhouse_id" value="<?= htmlspecialchars($encId) ?>">
                                        <input type="hidden" name="status" value="<?= $nextStatus ?>">
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink flex items-center justify-center transition-colors shadow-2xs" 
                                                title="<?= $isActive ? 'Deactivate Listing' : 'Publish Listing' ?>">
                                            <span class="material-symbols-outlined text-lg <?= $isActive ? 'text-rose-500' : 'text-emerald-600' ?>">
                                                <?= $isActive ? 'pause_circle' : 'play_circle' ?>
                                            </span>
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <button type="button" onclick="openDeleteFarmhouseModal('<?= htmlspecialchars($encId) ?>', '<?= addslashes(htmlspecialchars($farm['title'])) ?>')" 
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Delete Estate">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>

                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

    <?php endif; ?>

    <!-- ── 6. NUMBERED PAGINATION CONTROLS ───────────────────────────────────── -->
    <?php if ($totalPages > 1): 
        $startItem = ($currentPage - 1) * $perPageVal + 1;
        $endItem   = min($currentPage * $perPageVal, $totalCount);
    ?>
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-white rounded-3xl border border-border shadow-sm">
        
        <div class="text-xs text-muted font-medium order-2 sm:order-1">
            Showing <span class="text-ink font-bold"><?= $startItem ?></span> to 
            <span class="text-ink font-bold"><?= $endItem ?></span> of 
            <span class="text-ink font-bold"><?= $totalCount ?></span> farmhouses
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

<!-- ── 7. PROPERTY INSPECTOR / PREVIEW MODAL ──────────────────────────────── -->
<div id="inspectPropertyModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-3xl p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-start pb-4 border-b border-border">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 id="prop_dossier_title" class="text-xl font-extrabold text-ink">Property Title</h3>
                    <span id="prop_dossier_category_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                        Farmhouse
                    </span>
                    <span id="prop_dossier_status_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Active
                    </span>
                </div>
                <p id="prop_dossier_location" class="text-xs text-muted flex items-center gap-1 mt-1">
                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                    Location address
                </p>
            </div>
            
            <button type="button" onclick="closeInspectPropertyModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <!-- Photos Gallery Carousel -->
        <div id="prop_dossier_gallery_container" class="space-y-2">
            <div id="prop_dossier_gallery" class="flex gap-3 overflow-x-auto pb-2">
                <!-- Dynamic Images -->
            </div>
        </div>

        <!-- Metrics Bento Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-surface/60 border border-border text-xs text-center">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Nightly Price</span>
                <span id="prop_dossier_price" class="font-extrabold text-ink text-sm mt-0.5 block">₹0</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Bedrooms</span>
                <span id="prop_dossier_bedrooms" class="font-bold text-ink mt-0.5 block">1 BHK</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Guest Capacity</span>
                <span id="prop_dossier_capacity" class="font-bold text-indigo-700 mt-0.5 block">0 Day / 0 Nt</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Stays Booked</span>
                <span id="prop_dossier_bookings" class="font-bold text-emerald-600 mt-0.5 block">0 Bookings</span>
            </div>
        </div>

        <!-- Room Types & Rates Breakdown -->
        <div id="prop_dossier_room_types_section" class="space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-muted text-[10px] flex items-center gap-1.5">
                <span class="material-symbols-outlined text-indigo-600 text-sm">meeting_room</span>
                <span>Room Types &amp; Inventory Breakdown</span>
            </h4>
            <div id="prop_dossier_room_types_list" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <!-- Dynamic Room Types Badges -->
            </div>
        </div>

        <!-- Host Attribution & 1-Click Connect -->
        <div class="p-4 rounded-2xl bg-sky-l/40 border border-sky/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Estate Host / Partner</span>
                <strong id="prop_dossier_owner_name" class="font-bold text-ink text-sm block mt-0.5">Host Name</strong>
                <span id="prop_dossier_owner_contact" class="text-muted text-[11px]">email • phone</span>
            </div>
            <div class="flex items-center gap-2">
                <a id="prop_dossier_whatsapp_btn" href="#" target="_blank" 
                   class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                    WhatsApp Host
                </a>
                <a id="prop_dossier_call_btn" href="#" 
                   class="py-2 px-3 rounded-xl bg-white hover:bg-sky-xl text-ink border border-border font-bold text-xs flex items-center gap-1 transition-colors">
                    <span class="material-symbols-outlined text-sm text-sky">call</span> Call
                </a>
            </div>
        </div>

        <!-- Description & Amenities -->
        <div class="space-y-4 text-xs">
            <div>
                <h4 class="font-bold uppercase tracking-wider text-muted text-[10px] mb-1">Estate Overview & Description</h4>
                <div id="prop_dossier_description" class="text-ink leading-relaxed bg-surface p-3.5 rounded-xl border border-border/60 text-xs">
                    Description...
                </div>
            </div>

            <div>
                <h4 class="font-bold uppercase tracking-wider text-muted text-[10px] mb-2">Equipped Amenities</h4>
                <div id="prop_dossier_amenities" class="flex flex-wrap gap-2">
                    <!-- Dynamic Amenities -->
                </div>
            </div>
        </div>

        <!-- Footer Action -->
        <div class="pt-4 border-t border-border flex items-center justify-between">
            <a id="prop_dossier_edit_link" href="#" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-sky/20">
                <span class="material-symbols-outlined text-sm">edit</span> Edit Farmhouse Configuration
            </a>
            <button type="button" onclick="closeInspectPropertyModal()" class="px-5 py-2.5 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                Close Preview
            </button>
        </div>

    </div>
</div>

<!-- ── 8. DELETE FARMHOUSE CONFIRMATION MODAL ────────────────────────────── -->
<div id="deletePropertyModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 sm:p-8 shadow-2xl border border-border text-center space-y-4">
        
        <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">delete_forever</span>
        </div>

        <div>
            <h3 class="text-xl font-bold text-ink">Delete Farmhouse Listing</h3>
            <p class="text-xs text-muted mt-1">
                Are you sure you want to permanently delete <strong id="delete_property_title" class="text-ink"></strong>? All uploaded gallery photos, amenity associations, and guest reviews will be removed.
            </p>
        </div>

        <form method="POST" action="<?= url('admin/managefarmhouses') ?>" class="flex gap-3 pt-2">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="farmhouse_id" id="delete_property_id">

            <button type="button" onclick="closeDeleteFarmhouseModal()" class="flex-1 py-3 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                Cancel
            </button>
            <button type="submit" class="flex-1 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition-all">
                Delete Permanently
            </button>
        </form>

    </div>
</div>

<!-- Hidden Bulk Action Submission Form -->
<form id="propertyBulkActionForm" method="POST" action="<?= url('admin/managefarmhouses') ?>" class="hidden">
    <input type="hidden" name="action" id="property_bulk_action_type">
    <input type="hidden" name="target_status" id="property_bulk_target_status">
    <div id="property_bulk_ids_container"></div>
</form>

<!-- ── 9. JAVASCRIPT LOGIC ──────────────────────────────────────────────── -->
<script>
    function switchView(mode) {
        document.getElementById('filter_view_mode').value = mode;
        document.getElementById('propertyFilterForm').submit();
    }

    function closeInspectPropertyModal() {
        document.getElementById('inspectPropertyModal').classList.add('hidden');
    }

    function openDeleteFarmhouseModal(encId, title) {
        document.getElementById('delete_property_id').value = encId;
        document.getElementById('delete_property_title').textContent = title;
        document.getElementById('deletePropertyModal').classList.remove('hidden');
    }

    function closeDeleteFarmhouseModal() {
        document.getElementById('deletePropertyModal').classList.add('hidden');
    }

    // Escape HTML helper
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Inspect Farmhouse Dossier Preview
    async function inspectProperty(encryptedId) {
        try {
            const res = await fetch(`<?= url('admin/managefarmhouses/details') ?>?id=${encodeURIComponent(encryptedId)}`);
            const json = await res.json();
            
            if (!json.success) {
                alert(json.message || 'Failed to load property details.');
                return;
            }

            const d = json.data;
            const f = d.farmhouse;
            const isRoomBookingEnabled = Number(f.allow_room_booking) === 1;

            document.getElementById('prop_dossier_title').textContent = f.title || 'Untitled Property';
            document.getElementById('prop_dossier_category_badge').textContent = f.category || 'Farmhouse';
            const bedCap = f.bedroom_capacity ? ` (${f.bedroom_capacity}/room)` : '';
            document.getElementById('prop_dossier_bedrooms').textContent = `${f.bedrooms || 1} BHK${bedCap}`;

            // Dynamic Location
            const locEl = document.getElementById('prop_dossier_location');
            if (locEl) {
                const locParts = [f.address, f.location, f.city, f.state].filter(Boolean);
                const uniqueLoc = locParts.filter((v, i, a) => a.indexOf(v) === i).join(', ');
                locEl.innerHTML = `<span class="material-symbols-outlined text-[14px]">location_on</span> <span>${escapeHtml(uniqueLoc || 'No address specified')}</span>`;
            }

            // Pricing: Only show room rate if room-wise booking is actively enabled
            if (isRoomBookingEnabled && f.room_price && parseFloat(f.room_price) > 0) {
                document.getElementById('prop_dossier_price').innerHTML = `₹${f.formatted_price} <span class="text-[10px] font-normal text-muted">full</span> • ₹${Number(f.room_price).toLocaleString('en-IN')} <span class="text-[10px] font-normal text-muted">/room</span>`;
            } else {
                document.getElementById('prop_dossier_price').textContent = `₹${f.formatted_price} /nt`;
            }
            document.getElementById('prop_dossier_capacity').textContent = `${f.day_capacity || '—'} Day / ${f.night_capacity || '—'} Night`;
            document.getElementById('prop_dossier_bookings').textContent = `${f.booking_count || 0} Stays`;

            // Rich Description (support HTML lists / paragraphs)
            const descEl = document.getElementById('prop_dossier_description');
            if (descEl) {
                if (f.description && f.description.trim()) {
                    if (/<[a-z][\s\S]*>/i.test(f.description)) {
                        descEl.innerHTML = f.description;
                    } else {
                        descEl.innerHTML = escapeHtml(f.description).replace(/\n/g, '<br>');
                    }
                } else {
                    descEl.innerHTML = '<span class="text-muted italic">No description available for this property.</span>';
                }
            }

            // Host Info
            const hostName = f.owner_name || f.host_name || 'Unassigned Host';
            const hostEmail = f.owner_email || f.contact_email || 'No email';
            const hostPhone = f.owner_phone || f.contact_phone || 'No phone';
            document.getElementById('prop_dossier_owner_name').textContent = hostName;
            document.getElementById('prop_dossier_owner_contact').textContent = `${hostEmail} • ${hostPhone}`;
            document.getElementById('prop_dossier_edit_link').href = `<?= url('admin/editfarm?id=') ?>${encodeURIComponent(f.encrypted_id)}`;

            // Status Badge
            const badge = document.getElementById('prop_dossier_status_badge');
            if (f.status === 'active') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
                badge.textContent = 'Active';
            } else if (f.status === 'pending') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200';
                badge.textContent = 'Pending';
            } else {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200';
                badge.textContent = 'Paused';
            }

            // Quick Connect Links
            const cleanPhone = (f.owner_phone || f.contact_phone || '').replace(/[^0-9]/g, '');
            const waBtn = document.getElementById('prop_dossier_whatsapp_btn');
            const callBtn = document.getElementById('prop_dossier_call_btn');
            if (cleanPhone) {
                waBtn.href = `https://wa.me/91${cleanPhone}?text=Hello%20${encodeURIComponent(hostName)},%20contacting%20regarding%20${encodeURIComponent(f.title || 'your farmhouse listing')}.`;
                waBtn.classList.remove('pointer-events-none', 'opacity-50');
                callBtn.href = `tel:${cleanPhone}`;
                callBtn.classList.remove('pointer-events-none', 'opacity-50');
            } else {
                waBtn.removeAttribute('href');
                waBtn.classList.add('pointer-events-none', 'opacity-50');
                callBtn.removeAttribute('href');
                callBtn.classList.add('pointer-events-none', 'opacity-50');
            }

            // Gallery Photos (with fallback to primary farmhouse cover)
            const gCont = document.getElementById('prop_dossier_gallery');
            if (d.images && d.images.length > 0) {
                gCont.innerHTML = d.images.map(img => {
                    const src = img.resolved_url || (img.image_url.startsWith('http') ? img.image_url : ('<?= asset('assets/images/uploads/farmhouses/') ?>/' + img.image_url.replace(/^.*[\\\/]/, '')));
                    return `<img src="${src}" class="h-32 w-48 object-cover rounded-2xl bg-surface shrink-0 border border-border shadow-2xs">`;
                }).join('');
            } else if (f.resolved_image_url || f.image_url) {
                const coverSrc = f.resolved_image_url || (f.image_url.startsWith('http') ? f.image_url : ('<?= asset('assets/images/uploads/farmhouses/') ?>/' + f.image_url.replace(/^.*[\\\/]/, '')));
                gCont.innerHTML = `<img src="${coverSrc}" class="h-32 w-48 object-cover rounded-2xl bg-surface shrink-0 border border-border shadow-2xs"><div class="h-32 px-4 flex items-center justify-center bg-surface/50 rounded-2xl text-[11px] text-muted border border-border/40">Primary cover shown (no additional gallery photos)</div>`;
            } else {
                gCont.innerHTML = `<div class="h-32 w-full flex items-center justify-center bg-surface rounded-2xl text-xs text-muted">No gallery photos uploaded yet.</div>`;
            }

            // Amenities
            const amCont = document.getElementById('prop_dossier_amenities');
            if (d.amenities && d.amenities.length > 0) {
                amCont.innerHTML = d.amenities.map(a => `
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-surface border border-border text-ink text-xs font-semibold">
                        <span class="material-symbols-outlined text-sm text-sky">${a.icon || 'check'}</span>
                        ${escapeHtml(a.name)}
                    </span>
                `).join('');
            } else {
                amCont.innerHTML = `<span class="text-xs text-muted">No specific amenities configured.</span>`;
            }

            // Room Types Display (Strictly prioritized by allow_room_booking)
            const rtSec  = document.getElementById('prop_dossier_room_types_section');
            const rtList = document.getElementById('prop_dossier_room_types_list');
            if (isRoomBookingEnabled && d.room_types && d.room_types.length > 0) {
                rtSec.classList.remove('hidden');
                rtList.innerHTML = d.room_types.map(rt => {
                    const priceFmt = Number(rt.price_per_room || 0).toLocaleString('en-IN');
                    const descTxt = rt.description ? `<p class="text-[10px] text-muted italic mt-1 line-clamp-1">${escapeHtml(rt.description)}</p>` : '';
                    return `
                        <div class="p-3 rounded-xl bg-surface border border-border flex items-center justify-between gap-2 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <div class="font-extrabold text-ink text-xs flex items-center gap-1.5 truncate">
                                    <span class="material-symbols-outlined text-indigo-600 text-sm">bedroom_parent</span>
                                    <span>${escapeHtml(rt.room_type_name)}</span>
                                </div>
                                <div class="text-[10.5px] text-muted font-semibold mt-0.5">
                                    <span class="text-emerald-700 font-bold">${rt.total_rooms} Room${rt.total_rooms > 1 ? 's' : ''} available</span> • Up to ${rt.capacity_per_room || 2} guests/room
                                </div>
                                ${descTxt}
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-xs text-indigo-700 block">₹${priceFmt}</span>
                                <span class="text-[9.5px] text-muted block">/ room / nt</span>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                rtSec.classList.add('hidden');
                rtList.innerHTML = '';
            }

            document.getElementById('inspectPropertyModal').classList.remove('hidden');

        } catch (err) {
            alert('Error loading property details dossier.');
        }
    }

    // ── Bulk Selection Helpers (for Table View) ──
    function toggleSelectAllProperties(master) {
        const checkboxes = document.querySelectorAll('.property-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updatePropertyBulkBar();
    }

    function onPropertyRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.property-checkbox');
        const master = document.getElementById('selectAllPropertiesCheckbox');
        const checked = document.querySelectorAll('.property-checkbox:checked');
        if (master) {
            master.checked = (checkboxes.length > 0 && checked.length === checkboxes.length);
        }
        updatePropertyBulkBar();
    }

    function updatePropertyBulkBar() {
        const checked = document.querySelectorAll('.property-checkbox:checked');
        const bar = document.getElementById('propertyBulkBar');
        const countSpan = document.getElementById('propertySelectedCount');

        if (checked.length > 0) {
            countSpan.textContent = `${checked.length} propert${checked.length === 1 ? 'y' : 'ies'} selected`;
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function deselectAllProperties() {
        document.querySelectorAll('.property-checkbox').forEach(cb => cb.checked = false);
        const master = document.getElementById('selectAllPropertiesCheckbox');
        if (master) master.checked = false;
        updatePropertyBulkBar();
    }

    function submitPropertyBulkAction(targetStatus) {
        const checked = document.querySelectorAll('.property-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Update status of ${checked.length} propert(y/ies) to '${targetStatus}'?`)) return;

        const form = document.getElementById('propertyBulkActionForm');
        const container = document.getElementById('property_bulk_ids_container');
        container.innerHTML = '';

        document.getElementById('property_bulk_action_type').value = 'bulk_status';
        document.getElementById('property_bulk_target_status').value = targetStatus;

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'farmhouse_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }

    function submitPropertyBulkDelete() {
        const checked = document.querySelectorAll('.property-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Permanently delete ${checked.length} farmhouse listing(s)? All photos and rules will be deleted.`)) return;

        const form = document.getElementById('propertyBulkActionForm');
        const container = document.getElementById('property_bulk_ids_container');
        container.innerHTML = '';

        document.getElementById('property_bulk_action_type').value = 'bulk_delete';

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'farmhouse_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>