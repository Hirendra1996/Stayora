<?php
$pageTitle  = "User Management";
$activePage = "users";

include __DIR__ . "/../Includes/admin_header.php";

// Query parameters for pagination and filtering
$currentQuery = $_GET;
unset($currentQuery['page']);
$baseUrl = "?" . http_build_query($currentQuery) . (empty($currentQuery) ? "" : "&");

$searchVal   = htmlspecialchars($_GET['q'] ?? '');
$statusVal   = strtolower($_GET['status'] ?? 'all');
$sortVal     = strtolower($_GET['sort'] ?? 'newest');
$perPageVal  = (int) ($_GET['per_page'] ?? 10);

$totalUsersAll = (int) ($stats['total'] ?? 0);
$activeUsers   = (int) ($stats['active'] ?? 0);
$blockedUsers  = (int) ($stats['blocked'] ?? 0);
$onlineUsers   = (int) ($stats['online'] ?? 0);

$activePercent = $totalUsersAll > 0 ? round(($activeUsers / $totalUsersAll) * 100) : 0;
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
                    <span class="material-symbols-outlined text-[15px]">manage_accounts</span>
                    Access & Identity
                </span>
                <span class="text-xs font-semibold text-muted flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Sync
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                User Management
            </h1>
            <p class="text-muted text-sm mt-1">
                Monitor registered guest accounts, active sessions, booking histories, and access permissions.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <a href="<?= url('admin/users/export') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs border border-border shadow-sm hover:border-sky/40 transition-all active:scale-95">
                <span class="material-symbols-outlined text-sky text-lg">download</span>
                Export CSV
            </a>
            <button type="button" onclick="openAddModal()" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Add New User
            </button>
        </div>
    </div>

    <!-- ── 3. EXECUTIVE KPI BENTO GRID ───────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Users -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-sky/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">groups</span>
                </div>
                <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2.5 py-1 rounded-md uppercase tracking-wider">
                    <?= $activePercent ?>% Verified
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $totalUsersAll) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Total Registered Users
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-sky-l flex items-center justify-between text-xs text-muted">
                <span>Guest customer network</span>
                <span class="text-ink font-bold"><?= $totalCount ?> Listed</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-emerald-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Unrestricted
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $activeUsers) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Active Accounts
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-emerald-600 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Can book & inquire
                </span>
                <a href="?status=active" class="text-emerald-700 font-bold hover:underline">Filter</a>
            </div>
        </div>

        <!-- Blocked Users -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-rose-400/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">block</span>
                </div>
                <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Restricted
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                    <?= sprintf('%02d', $blockedUsers) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Blocked Accounts
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span class="text-rose-600 font-semibold">Access disabled</span>
                <a href="?status=blocked" class="text-rose-700 font-bold hover:underline">Filter</a>
            </div>
        </div>

        <!-- Online Users -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-sky/40 transition-all group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky group-hover:scale-110 transition-transform relative">
                    <span class="material-symbols-outlined text-2xl">sensors</span>
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Now
                </span>
            </div>
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold text-emerald-600 tracking-tight" id="kpi-online-count">
                    <?= sprintf('%02d', $onlineUsers) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Active Sessions
                </div>
            </div>
            <div class="mt-4 pt-3.5 border-t border-stone-100 flex items-center justify-between text-xs text-muted">
                <span>Real-time presence</span>
                <a href="?status=online" class="text-sky hover:text-sky-d font-bold">Filter Online</a>
            </div>
        </div>

    </div>

    <!-- ── 4. FILTER & SEARCH CONTROL BAR ────────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 md:p-5 shadow-sm space-y-4">
        
        <form method="GET" action="" id="filterForm" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Search Field -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xl pointer-events-none">search</span>
                <input type="text" 
                       name="q" 
                       value="<?= $searchVal ?>" 
                       placeholder="Search by name, email, phone number, gender..." 
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
                    All (<?= $totalUsersAll ?>)
                </button>
                <button type="submit" name="status" value="active" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'active') ? 'bg-white text-emerald-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Active (<?= $activeUsers ?>)
                </button>
                <button type="submit" name="status" value="blocked" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'blocked') ? 'bg-white text-rose-600 shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Blocked (<?= $blockedUsers ?>)
                </button>
                <button type="submit" name="status" value="online" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all <?= ($statusVal === 'online') ? 'bg-white text-sky shadow-sm' : 'text-muted hover:text-ink' ?>">
                    Online (<?= $onlineUsers ?>)
                </button>
            </div>

            <!-- Sort & Items Per Page -->
            <div class="flex items-center gap-2 shrink-0">
                <select name="sort" onchange="document.getElementById('filterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="newest" <?= $sortVal === 'newest' ? 'selected' : '' ?>>Newest Registered</option>
                    <option value="oldest" <?= $sortVal === 'oldest' ? 'selected' : '' ?>>Oldest Registered</option>
                    <option value="name_asc" <?= $sortVal === 'name_asc' ? 'selected' : '' ?>>Name (A to Z)</option>
                    <option value="name_desc" <?= $sortVal === 'name_desc' ? 'selected' : '' ?>>Name (Z to A)</option>
                    <option value="most_bookings" <?= $sortVal === 'most_bookings' ? 'selected' : '' ?>>Most Bookings</option>
                    <option value="most_wishlist" <?= $sortVal === 'most_wishlist' ? 'selected' : '' ?>>Most Wishlisted</option>
                </select>

                <select name="per_page" onchange="document.getElementById('filterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="10" <?= $perPageVal === 10 ? 'selected' : '' ?>>10 / page</option>
                    <option value="25" <?= $perPageVal === 25 ? 'selected' : '' ?>>25 / page</option>
                    <option value="50" <?= $perPageVal === 50 ? 'selected' : '' ?>>50 / page</option>
                </select>
            </div>

        </form>

        <!-- ── Bulk Action Floating Strip (Visible when items are selected) ── -->
        <div id="bulkActionBar" class="hidden items-center justify-between bg-ink text-white p-3.5 rounded-xl shadow-lg border border-sky/30 animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky animate-pulse"></span>
                <span id="selectedCount" class="text-xs font-bold text-sky-l">0 users selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitBulkAction('active')" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                    Activate
                </button>
                <button type="button" onclick="submitBulkAction('blocked')" 
                        class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors">
                    Block
                </button>
                <button type="button" onclick="submitBulkDelete()" 
                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Delete
                </button>
                <button type="button" onclick="deselectAllUsers()" 
                        class="px-2.5 py-1.5 text-xs text-stone-300 hover:text-white underline">
                    Clear
                </button>
            </div>
        </div>

    </div>

    <!-- ── 5. USERS DATA TABLE ───────────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl border border-border shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                
                <thead>
                    <tr class="bg-surface border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" 
                                   class="rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-4 px-4">User Profile</th>
                        <th class="py-4 px-4">Contact Information</th>
                        <th class="py-4 px-4 text-center">Activity Metrics</th>
                        <th class="py-4 px-4">Account Status</th>
                        <th class="py-4 px-4">Last Activity</th>
                        <th class="py-4 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border/60 text-sm">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): 
                            $encId     = $user['encrypted_id'];
                            $name      = $user['name'] ?? 'Anonymous Guest';
                            $email     = $user['email'] ?? 'No Email';
                            $phone     = $user['phone'] ?? 'N/A';
                            $gender    = ucfirst($user['gender'] ?? 'Not set');
                            $status    = strtolower($user['status'] ?? 'active');
                            $isActive  = ($status === 'active');
                            $isOnline  = (bool) ($user['is_online'] ?? false);
                            $cleanDial = preg_replace('/[^0-9]/', '', $phone);
                            
                            $initials = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr(explode(' ', $name)[1], 0, 1) : ''));
                            $joinDate = !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '—';
                            $lastLogin = !empty($user['last_login']) ? date('d M Y, h:i A', strtotime($user['last_login'])) : 'Never logged in';

                            $safeUser = $user;
                            unset($safeUser['password'], $safeUser['remember_token'], $safeUser['reset_token']);
                            $userJson = htmlspecialchars(json_encode($safeUser), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="hover:bg-surface/50 transition-colors user-row-item" data-uid="<?= $encId ?>">
                            
                            <!-- Checkbox -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="<?= $encId ?>" class="user-checkbox rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer" onchange="onRowCheckboxChange()">
                            </td>

                            <!-- User Profile -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-l to-white border border-sky/20 flex items-center justify-center text-sky font-bold text-xs shadow-sm">
                                            <?= $initials ?>
                                        </div>
                                        <?php if ($isOnline): ?>
                                            <span class="user-online-dot absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <button type="button" onclick="inspectUser('<?= $encId ?>')" class="font-bold text-ink hover:text-sky transition-colors truncate block text-left">
                                            <?= htmlspecialchars($name) ?>
                                        </button>
                                        <div class="flex items-center gap-2 text-[11px] text-muted mt-0.5">
                                            <span><?= htmlspecialchars($gender) ?></span>
                                            <span>•</span>
                                            <span>Joined <?= $joinDate ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info -->
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

                            <!-- Activity Metrics -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-l border border-sky/20 text-sky-d text-xs font-bold" title="Booking requests">
                                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                                        <?= (int)($user['booking_count'] ?? 0) ?>
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-600 text-xs font-bold" title="Wishlist saved estates">
                                        <span class="material-symbols-outlined text-sm">favorite</span>
                                        <?= (int)($user['wishlist_count'] ?? 0) ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Account Status -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    <?php if ($isOnline): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 user-badge-online">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Online
                                        </span>
                                    <?php elseif ($isActive): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-sky-l text-sky-d border border-sky/20">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                            Blocked
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
                                    
                                    <!-- Inspect User Dossier -->
                                    <button type="button" onclick="inspectUser('<?= $encId ?>')" 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-xs" 
                                            title="View Customer Dossier">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>

                                    <!-- Edit User -->
                                    <button type="button" onclick='openEditModal(<?= $userJson ?>)' 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-xs" 
                                            title="Edit User">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>

                                    <!-- Force Logout (if online) -->
                                    <?php if ($isOnline): ?>
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Terminate this user\'s active session?')">
                                        <input type="hidden" name="action" value="force_logout">
                                        <input type="hidden" name="user_id" value="<?= $encId ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition-colors shadow-xs" title="Force Logout Session">
                                            <span class="material-symbols-outlined text-lg">logout</span>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Toggle Active / Blocked -->
                                    <form method="POST" action="" class="inline">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="user_id" value="<?= $encId ?>">
                                        <input type="hidden" name="status" value="<?= $isActive ? 'blocked' : 'active' ?>">
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink flex items-center justify-center transition-colors shadow-xs" 
                                                title="<?= $isActive ? 'Block User Account' : 'Activate User Account' ?>">
                                            <span class="material-symbols-outlined text-lg <?= $isActive ? 'text-rose-500' : 'text-emerald-600' ?>">
                                                <?= $isActive ? 'block' : 'check_circle' ?>
                                            </span>
                                        </button>
                                    </form>

                                    <!-- Delete Permanently -->
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Permanently delete <?= htmlspecialchars(addslashes($name)) ?> from the system?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $encId ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-xs" title="Delete User">
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
                                <h4 class="font-bold text-ink text-base">No Users Found</h4>
                                <p class="text-xs text-muted mt-1 max-w-sm mx-auto">
                                    No guest accounts match your current filter and search criteria.
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
                <span class="text-ink font-bold"><?= $totalCount ?></span> users
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

<!-- ── 7. ADD USER MODAL ─────────────────────────────────────────────────── -->
<div id="addUserModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center pb-4 mb-6 border-b border-border">
            <div>
                <h3 class="text-xl font-bold text-ink">Register New User</h3>
                <p class="text-xs text-muted mt-0.5">Create a customer profile with system access credentials.</p>
            </div>
            <button type="button" onclick="closeAddModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="add_user">

            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Full Name *</label>
                <input name="name" type="text" required placeholder="e.g. Ramesh Patel" 
                       class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Email Address *</label>
                    <input name="email" type="email" required placeholder="ramesh@example.com" 
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
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Gender</label>
                    <select name="gender" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Date of Birth</label>
                    <input name="date_of_birth" type="date" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Initial Password *</label>
                    <div class="relative">
                        <input name="password" id="add_pw" type="password" required placeholder="Min. 6 chars" 
                               class="w-full px-4 py-3 pr-10 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <button type="button" onclick="togglePw('add_pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Account Status</label>
                    <select name="status" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="active" selected>Active</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky to-sky-d text-white font-bold rounded-xl shadow-lg shadow-sky/25 hover:shadow-sky/40 transition-all active:scale-98">
                    Create User Account
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ── 8. EDIT USER MODAL ────────────────────────────────────────────────── -->
<div id="editUserModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-ink/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center pb-4 mb-6 border-b border-border">
            <div>
                <h3 class="text-xl font-bold text-ink">Edit User Account</h3>
                <p class="text-xs text-muted mt-0.5">Modify personal details or reset credentials.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Full Name *</label>
                <input name="name" id="edit_name" type="text" required 
                       class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Email Address *</label>
                    <input name="email" id="edit_email" type="email" required 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Phone Number *</label>
                    <input name="phone" id="edit_phone" type="text" required 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Gender</label>
                    <select name="gender" id="edit_gender" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="">Not set</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Date of Birth</label>
                    <input name="date_of_birth" id="edit_dob" type="date" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Account Status</label>
                    <select name="status" id="edit_status" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="active">Active</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">New Password (optional)</label>
                    <div class="relative">
                        <input name="password" id="edit_pw" type="password" placeholder="Leave blank to keep" 
                               class="w-full px-4 py-3 pr-10 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <button type="button" onclick="togglePw('edit_pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Online Indicator Banner in Modal -->
            <div id="edit_online_banner" class="hidden items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-xl text-xs font-semibold">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    User currently has an active session.
                </span>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky to-sky-d text-white font-bold rounded-xl shadow-lg shadow-sky/25 hover:shadow-sky/40 transition-all active:scale-98">
                    Save Account Changes
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ── 9. CUSTOMER DOSSIER / USER INSPECTOR MODAL ───────────────────────── -->
<div id="inspectModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-2xl p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto space-y-6">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-start pb-4 border-b border-border">
            <div class="flex items-center gap-4">
                <div id="dossier_avatar" class="w-14 h-14 rounded-2xl bg-sky-l border border-sky/30 text-sky font-extrabold text-xl flex items-center justify-center shadow-sm">
                    U
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 id="dossier_name" class="text-xl font-extrabold text-ink">User Name</h3>
                        <span id="dossier_status_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Active
                        </span>
                    </div>
                    <p id="dossier_email_phone" class="text-xs text-muted mt-0.5">user@example.com • 9876543210</p>
                </div>
            </div>
            
            <button type="button" onclick="closeInspectModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <!-- Quick Communication Action Buttons -->
        <div class="grid grid-cols-2 gap-3">
            <a id="dossier_whatsapp_btn" href="#" target="_blank" 
               class="py-2.5 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-xs">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                WhatsApp Direct
            </a>
            <a id="dossier_call_btn" href="#" 
               class="py-2.5 px-4 rounded-xl bg-sky-l hover:bg-sky text-sky-d hover:text-white border border-sky/20 text-xs font-bold flex items-center justify-center gap-2 transition-all shadow-xs">
                <span class="material-symbols-outlined text-sm">call</span>
                Direct Phone Call
            </a>
        </div>

        <!-- Metadata Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-surface/60 border border-border text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Gender</span>
                <span id="dossier_gender" class="font-bold text-ink mt-0.5 block">Male</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Date of Birth</span>
                <span id="dossier_dob" class="font-bold text-ink mt-0.5 block">15 Aug 1995</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Registered On</span>
                <span id="dossier_joined" class="font-bold text-ink mt-0.5 block">21 Aug 2026</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Last Active Login</span>
                <span id="dossier_last_login" class="font-bold text-ink mt-0.5 block">21 Aug 2026, 12:45 PM</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Presence</span>
                <span id="dossier_presence" class="font-bold text-emerald-600 mt-0.5 block">Online</span>
            </div>
        </div>

        <!-- Tabbed User Activity Section -->
        <div class="space-y-4">
            
            <div class="flex border-b border-border">
                <button type="button" onclick="switchDossierTab('bookings')" id="tab_btn_bookings" 
                        class="pb-2.5 px-4 text-xs font-bold border-b-2 border-sky text-sky transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">receipt_long</span>
                    Bookings (<span id="dossier_booking_count">0</span>)
                </button>
                <button type="button" onclick="switchDossierTab('wishlist')" id="tab_btn_wishlist" 
                        class="pb-2.5 px-4 text-xs font-bold border-b-2 border-transparent text-muted hover:text-ink transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">favorite</span>
                    Wishlist (<span id="dossier_wishlist_count">0</span>)
                </button>
                <button type="button" onclick="switchDossierTab('inquiries')" id="tab_btn_inquiries" 
                        class="pb-2.5 px-4 text-xs font-bold border-b-2 border-transparent text-muted hover:text-ink transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">forum</span>
                    Inquiries (<span id="dossier_inquiry_count">0</span>)
                </button>
            </div>

            <!-- Tab Content: Bookings -->
            <div id="tab_content_bookings" class="space-y-3 max-h-60 overflow-y-auto pr-1">
                <div id="dossier_bookings_list" class="space-y-2">
                    <!-- Dynamic -->
                </div>
            </div>

            <!-- Tab Content: Wishlist -->
            <div id="tab_content_wishlist" class="hidden space-y-3 max-h-60 overflow-y-auto pr-1">
                <div id="dossier_wishlist_list" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Dynamic -->
                </div>
            </div>

            <!-- Tab Content: Inquiries -->
            <div id="tab_content_inquiries" class="hidden space-y-3 max-h-60 overflow-y-auto pr-1">
                <div id="dossier_inquiries_list" class="space-y-2">
                    <!-- Dynamic -->
                </div>
            </div>

        </div>

        <!-- Footer Action -->
        <div class="pt-4 border-t border-border flex justify-end">
            <button type="button" onclick="closeInspectModal()" class="px-5 py-2.5 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                Close Dossier
            </button>
        </div>

    </div>
</div>

<!-- Hidden Bulk Action Submission Form -->
<form id="bulkActionForm" method="POST" action="" class="hidden">
    <input type="hidden" name="action" id="bulk_action_type">
    <input type="hidden" name="target_status" id="bulk_target_status">
    <div id="bulk_user_ids_container"></div>
</form>

<!-- ── 10. JAVASCRIPT LOGIC ──────────────────────────────────────────────── -->
<script>
    // Modal Helpers
    function openAddModal()  { document.getElementById('addUserModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addUserModal').classList.add('hidden'); }
    function closeEditModal(){ document.getElementById('editUserModal').classList.add('hidden'); }
    function closeInspectModal() { document.getElementById('inspectModal').classList.add('hidden'); }

    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        const icon = btn.querySelector('.material-symbols-outlined');
        inp.type = inp.type === 'password' ? 'text' : 'password';
        icon.textContent = inp.type === 'password' ? 'visibility' : 'visibility_off';
    }

    function openEditModal(u) {
        document.getElementById('edit_user_id').value = u.encrypted_id;
        document.getElementById('edit_name').value = u.name ?? '';
        document.getElementById('edit_email').value = u.email ?? '';
        document.getElementById('edit_phone').value = u.phone ?? '';
        document.getElementById('edit_gender').value = u.gender ?? '';
        document.getElementById('edit_dob').value = u.date_of_birth ?? '';
        document.getElementById('edit_status').value = u.status ?? 'active';
        document.getElementById('edit_pw').value = '';
        
        const banner = document.getElementById('edit_online_banner');
        if (u.is_online == 1) {
            banner.classList.remove('hidden');
            banner.classList.add('flex');
        } else {
            banner.classList.add('hidden');
            banner.classList.remove('flex');
        }

        document.getElementById('editUserModal').classList.remove('hidden');
    }

    // Inspect User Dossier
    async function inspectUser(encryptedId) {
        try {
            const res = await fetch(`<?= url('admin/users/details') ?>?id=${encodeURIComponent(encryptedId)}`);
            const json = await res.json();
            
            if (!json.success) {
                alert(json.message || 'Failed to load user details.');
                return;
            }

            const d = json.data;
            const u = d.user;
            const initials = u.name ? u.name.charAt(0).toUpperCase() : 'U';

            document.getElementById('dossier_avatar').textContent = initials;
            document.getElementById('dossier_name').textContent = u.name || 'Anonymous Guest';
            document.getElementById('dossier_email_phone').textContent = `${u.email || 'No Email'} • ${u.phone || 'No Phone'}`;
            document.getElementById('dossier_gender').textContent = u.gender ? (u.gender.charAt(0).toUpperCase() + u.gender.slice(1)) : 'Not specified';
            document.getElementById('dossier_dob').textContent = u.formatted_dob;
            document.getElementById('dossier_joined').textContent = u.formatted_joined;
            document.getElementById('dossier_last_login').textContent = u.formatted_last_login;
            document.getElementById('dossier_presence').textContent = u.is_online ? '🟢 Online Now' : '⚪ Offline';

            // Status Badge
            const statusBadge = document.getElementById('dossier_status_badge');
            if (u.status === 'active') {
                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
                statusBadge.textContent = 'Active';
            } else {
                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200';
                statusBadge.textContent = 'Blocked';
            }

            // Quick Connect Links
            const cleanPhone = (u.phone || '').replace(/[^0-9]/g, '');
            document.getElementById('dossier_whatsapp_btn').href = `https://wa.me/91${cleanPhone}?text=Hello%20${encodeURIComponent(u.name || '')},%20contacting%20from%20Farmlelo%20support.`;
            document.getElementById('dossier_call_btn').href = `tel:${cleanPhone}`;

            // Bookings tab
            const bList = document.getElementById('dossier_bookings_list');
            document.getElementById('dossier_booking_count').textContent = d.bookings.length;
            if (d.bookings.length > 0) {
                bList.innerHTML = d.bookings.map(b => `
                    <div class="p-3 rounded-xl bg-surface border border-border flex items-center justify-between gap-3 text-xs">
                        <div>
                            <h5 class="font-bold text-ink">${b.farmhouse_title || 'Farmhouse Stay'}</h5>
                            <p class="text-muted text-[11px] mt-0.5">${b.check_in || b.start_date || 'Date TBD'} ➔ ${b.check_out || b.end_date || 'Date TBD'} • ${b.guests || 1} Guests</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-ink">₹${Number(b.price || 0).toLocaleString()}</span>
                            <span class="block text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded ${b.status === 'approved' ? 'bg-emerald-100 text-emerald-800' : (b.status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')}">${b.status || 'Pending'}</span>
                        </div>
                    </div>
                `).join('');
            } else {
                bList.innerHTML = `<p class="text-center py-6 text-xs text-muted">No booking reservations found.</p>`;
            }

            // Wishlist tab
            const wList = document.getElementById('dossier_wishlist_list');
            document.getElementById('dossier_wishlist_count').textContent = d.wishlist.length;
            if (d.wishlist.length > 0) {
                wList.innerHTML = d.wishlist.map(w => `
                    <div class="p-3 rounded-xl bg-surface border border-border flex items-center gap-3 text-xs">
                        <img src="${w.thumb_url || 'https://placehold.co/100x100/E6F6FD/16A5DE?text=Farm'}" class="w-12 h-12 rounded-lg object-cover bg-stone-100 shrink-0">
                        <div class="min-w-0 flex-1">
                            <h5 class="font-bold text-ink truncate">${w.farmhouse_title || 'Farmhouse'}</h5>
                            <p class="text-muted text-[11px] truncate">${w.farmhouse_location || 'Location'}</p>
                            <span class="text-sky font-bold text-[11px]">₹${Number(w.farmhouse_price || 0).toLocaleString()}</span>
                        </div>
                    </div>
                `).join('');
            } else {
                wList.innerHTML = `<p class="text-center col-span-2 py-6 text-xs text-muted">No wishlist items saved.</p>`;
            }

            // Inquiries tab
            const iList = document.getElementById('dossier_inquiries_list');
            document.getElementById('dossier_inquiry_count').textContent = d.inquiries.length;
            if (d.inquiries.length > 0) {
                iList.innerHTML = d.inquiries.map(i => `
                    <div class="p-3 rounded-xl bg-surface border border-border text-xs space-y-1">
                        <div class="flex justify-between font-semibold">
                            <span>${i.farmhouse_title || 'General Inquiry'}</span>
                            <span class="text-muted text-[10px]">${i.created_at || ''}</span>
                        </div>
                        <p class="text-muted italic">"${i.message || 'No message'}"</p>
                    </div>
                `).join('');
            } else {
                iList.innerHTML = `<p class="text-center py-6 text-xs text-muted">No inquiries submitted.</p>`;
            }

            // Default to bookings tab
            switchDossierTab('bookings');
            document.getElementById('inspectModal').classList.remove('hidden');

        } catch (err) {
            alert('Error connecting to user details service.');
        }
    }

    function switchDossierTab(tab) {
        ['bookings', 'wishlist', 'inquiries'].forEach(t => {
            const btn = document.getElementById(`tab_btn_${t}`);
            const content = document.getElementById(`tab_content_${t}`);
            if (t === tab) {
                btn.className = 'pb-2.5 px-4 text-xs font-bold border-b-2 border-sky text-sky transition-colors flex items-center gap-1.5';
                content.classList.remove('hidden');
            } else {
                btn.className = 'pb-2.5 px-4 text-xs font-bold border-b-2 border-transparent text-muted hover:text-ink transition-colors flex items-center gap-1.5';
                content.classList.add('hidden');
            }
        });
    }

    // ── Bulk Selection Helpers ──
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateBulkBar();
    }

    function onRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        const master = document.getElementById('selectAllCheckbox');
        const checked = document.querySelectorAll('.user-checkbox:checked');
        master.checked = (checkboxes.length > 0 && checked.length === checkboxes.length);
        updateBulkBar();
    }

    function updateBulkBar() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const bar = document.getElementById('bulkActionBar');
        const countSpan = document.getElementById('selectedCount');

        if (checked.length > 0) {
            countSpan.textContent = `${checked.length} user${checked.length === 1 ? '' : 's'} selected`;
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function deselectAllUsers() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAllCheckbox').checked = false;
        updateBulkBar();
    }

    function submitBulkAction(targetStatus) {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Update status of ${checked.length} user(s) to '${targetStatus}'?`)) return;

        const form = document.getElementById('bulkActionForm');
        const container = document.getElementById('bulk_user_ids_container');
        container.innerHTML = '';

        document.getElementById('bulk_action_type').value = 'bulk_status';
        document.getElementById('bulk_target_status').value = targetStatus;

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'user_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }

    function submitBulkDelete() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Permanently delete ${checked.length} user(s)? This action cannot be undone.`)) return;

        const form = document.getElementById('bulkActionForm');
        const container = document.getElementById('bulk_user_ids_container');
        container.innerHTML = '';

        document.getElementById('bulk_action_type').value = 'bulk_delete';

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'user_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }

    // ── Live Presence Status Poller (every 20s) ──
    setInterval(async () => {
        try {
            const res = await fetch('<?= url('admin/users/online-status') ?>');
            if (!res.ok) return;
            const data = await res.json();
            if (data.success) {
                const kpi = document.getElementById('kpi-online-count');
                if (kpi) kpi.textContent = String(data.online_count).padStart(2, '0');

                // Update individual online badges & avatar dots
                document.querySelectorAll('.user-row-item').forEach(row => {
                    const uid = row.dataset.uid;
                    const isOnline = data.user_status[uid] || false;
                    const dot = row.querySelector('.user-online-dot');
                    const badge = row.querySelector('.user-badge-online');

                    if (isOnline) {
                        if (!dot) {
                            const avatarWrap = row.querySelector('.relative.shrink-0');
                            if (avatarWrap) {
                                const newDot = document.createElement('span');
                                newDot.className = 'user-online-dot absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full';
                                avatarWrap.appendChild(newDot);
                            }
                        }
                    } else {
                        if (dot) dot.remove();
                    }
                });
            }
        } catch (e) {
            // Silently ignore background poll errors
        }
    }, 20000);
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>