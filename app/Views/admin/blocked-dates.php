<?php
$pageTitle  = "Date Locks & Availability";
$activePage = "blocked_dates";

include __DIR__ . "/../Includes/admin_header.php";

$blockedDates    = $blockedDates    ?? [];
$farmhouses      = $farmhouses      ?? [];
$success_message = $success_message ?? null;
$error_message   = $error_message   ?? null;

$searchVal  = htmlspecialchars($_GET['q'] ?? '');
$farmVal    = trim($_GET['farmhouse_id'] ?? '');
$lockedBy   = strtolower(trim($_GET['locked_by'] ?? 'all'));

$totalCount = count($blockedDates);
$ownerLocks = 0;
$adminLocks = 0;
foreach ($blockedDates as $bd) {
    if (($bd['locked_by'] ?? 'admin') === 'owner') {
        $ownerLocks++;
    } else {
        $adminLocks++;
    }
}
?>

<div class="space-y-6 pb-12">

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
                    <span class="material-symbols-outlined text-[15px]">event_busy</span>
                    Calendar Locks Console
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                Date Locks &amp; Blocked Periods
            </h1>
            <p class="text-muted text-sm mt-1">
                Monitor and manage dates locked by Owners (offline bookings) and Admin across all listed properties.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button onclick="document.getElementById('addLockModal').classList.remove('hidden')" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">lock</span>
                Lock New Dates
            </button>
        </div>
    </div>

    <!-- ── 3. STATS BAR ─────────────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-border p-4 flex items-center gap-4 shadow-2xs">
            <div class="w-12 h-12 rounded-xl bg-sky-l text-sky flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">date_range</span>
            </div>
            <div>
                <p class="text-2xl font-black text-ink leading-tight"><?= $totalCount ?></p>
                <p class="text-xs font-bold text-muted uppercase tracking-wider">Total Blocked Windows</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-4 flex items-center gap-4 shadow-2xs">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">person</span>
            </div>
            <div>
                <p class="text-2xl font-black text-ink leading-tight"><?= $ownerLocks ?></p>
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Owner-Locked (Offline)</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-4 flex items-center gap-4 shadow-2xs">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
            </div>
            <div>
                <p class="text-2xl font-black text-ink leading-tight"><?= $adminLocks ?></p>
                <p class="text-xs font-bold text-purple-600 uppercase tracking-wider">Admin-Locked</p>
            </div>
        </div>
    </div>

    <!-- ── 4. FILTER BAR ───────────────────────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 shadow-sm">
        <form method="GET" action="" id="adminLockFilterForm" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xl pointer-events-none">search</span>
                <input type="text" name="q" value="<?= $searchVal ?>" placeholder="Search by property title, owner name, or reason..." 
                       class="w-full bg-surface border border-border rounded-xl py-2.5 pl-10 pr-10 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                <?php if (!empty($searchVal)): ?>
                <a href="?<?= http_build_query(array_diff_key($_GET, ['q' => ''])) ?>" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-ink text-sm">
                    <span class="material-symbols-outlined text-base">close</span>
                </a>
                <?php endif; ?>
            </div>

            <div class="flex items-center flex-wrap gap-3 shrink-0">
                <select name="locked_by" onchange="document.getElementById('adminLockFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="all" <?= $lockedBy === 'all' ? 'selected' : '' ?>>All Lock Sources</option>
                    <option value="owner" <?= $lockedBy === 'owner' ? 'selected' : '' ?>>Locked by Owner</option>
                    <option value="admin" <?= $lockedBy === 'admin' ? 'selected' : '' ?>>Locked by Admin</option>
                </select>

                <select name="farmhouse_id" onchange="document.getElementById('adminLockFilterForm').submit()" 
                        class="bg-surface border border-border rounded-xl py-2 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="">All Farmhouse Estates</option>
                    <?php foreach ($farmhouses as $fh): ?>
                        <option value="<?= $fh['id'] ?>" <?= ($farmVal === (string)$fh['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fh['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- ── 5. BLOCKED DATES TABLE ──────────────────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
        <?php if (!empty($blockedDates)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface border-b border-border text-[11px] font-extrabold text-muted uppercase tracking-wider">
                            <th class="py-3.5 px-4">Property</th>
                            <th class="py-3.5 px-4">Date Range</th>
                            <th class="py-3.5 px-4">Duration</th>
                            <th class="py-3.5 px-4">Source / Locked By</th>
                            <th class="py-3.5 px-4">Reason / Notes</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60 text-xs font-semibold">
                        <?php foreach ($blockedDates as $bd): 
                            $st = strtotime($bd['start_date']);
                            $en = strtotime($bd['end_date']);
                            $nights = max(1, (int)ceil(($en - $st) / 86400));
                            $isOwnerLock = ($bd['locked_by'] ?? 'admin') === 'owner';
                        ?>
                            <tr class="hover:bg-sky-xl/40 transition-colors">
                                <td class="py-4 px-4 font-bold text-ink">
                                    <div>
                                        <p class="text-sm font-extrabold text-ink"><?= htmlspecialchars($bd['farm_title']) ?></p>
                                        <p class="text-[11px] text-muted font-normal flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[13px] text-sky">location_on</span>
                                            <?= htmlspecialchars($bd['farm_location']) ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-bold text-ink">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-500 text-base">date_range</span>
                                        <span><?= date('d M Y', $st) ?> &rarr; <?= date('d M Y', $en) ?></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full bg-sky-l text-sky-d font-extrabold text-[11px]">
                                        <?= $nights ?> <?= $nights === 1 ? 'Night' : 'Nights' ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase <?= $isOwnerLock ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $isOwnerLock ? 'bg-amber-500' : 'bg-purple-500' ?>"></span>
                                            <?= $isOwnerLock ? 'Owner Lock' : 'Admin Lock' ?>
                                        </span>
                                        <?php if (!empty($bd['owner_name'])): ?>
                                            <p class="text-[11px] text-muted">Owner: <?= htmlspecialchars($bd['owner_name']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-ink">
                                    <?= !empty($bd['reason']) ? htmlspecialchars($bd['reason']) : '<span class="text-muted italic">No reason provided</span>' ?>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <form method="POST" action="<?= url('admin/blocked-dates/unlock') ?>" onsubmit="return confirm('Are you sure you want to remove this date lock? The property will become available for online bookings during these dates.');" class="inline">
                                        <input type="hidden" name="block_id" value="<?= (int)$bd['id'] ?>">
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 hover:border-rose-600 text-xs font-bold transition-all shadow-2xs">
                                            <span class="material-symbols-outlined text-sm">lock_open</span>
                                            <span>Unlock / Delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-sky-l text-sky flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-2xl">event_available</span>
                </div>
                <h3 class="text-base font-bold text-ink mb-1">No Locked Dates Found</h3>
                <p class="text-xs text-muted">There are no locked date ranges matching your active filters.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- ── LOCK NEW DATES MODAL ───────────────────────────────────────────────── -->
<div id="addLockModal" class="hidden fixed inset-0 z-50 bg-ink/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-border p-6 max-w-md w-full shadow-2xl space-y-5 animate-fade-in">
        <div class="flex items-center justify-between border-b border-border pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-sky-l text-sky flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">lock</span>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-ink">Lock Property Dates</h3>
                    <p class="text-xs text-muted">Prevent bookings for selected dates</p>
                </div>
            </div>
            <button onclick="document.getElementById('addLockModal').classList.add('hidden')" class="text-muted hover:text-ink">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form method="POST" action="<?= url('admin/blocked-dates/lock') ?>" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-muted mb-1">Select Property <span class="text-rose-500">*</span></label>
                <select name="farmhouse_id" required class="w-full bg-surface border border-border rounded-xl p-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <option value="">-- Choose Farmhouse --</option>
                    <?php foreach ($farmhouses as $fh): ?>
                        <option value="<?= $fh['id'] ?>"><?= htmlspecialchars($fh['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-muted mb-1">Start Date <span class="text-rose-500">*</span></label>
                <input type="date" name="start_date" min="<?= date('Y-m-d') ?>" required class="w-full bg-surface border border-border rounded-xl p-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-muted mb-1">End Date <span class="text-rose-500">*</span></label>
                <input type="date" name="end_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required class="w-full bg-surface border border-border rounded-xl p-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-muted mb-1">Reason / Notes</label>
                <input type="text" name="reason" placeholder="e.g. Maintenance / Offline Reservation" class="w-full bg-surface border border-border rounded-xl p-3 text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addLockModal').classList.add('hidden')" class="flex-1 py-3 rounded-xl border border-border font-bold text-xs text-muted hover:bg-surface transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/20 transition">
                    Lock Dates
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>
