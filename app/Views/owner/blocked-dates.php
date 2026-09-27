<?php 
include __DIR__ . "/../Includes/owner_header.php"; 

$owner             = $owner             ?? [];
$farmhouses        = $farmhouses        ?? [];
$selectedFarmhouse = $selectedFarmhouse ?? null;
$farmhouseId       = (int)($farmhouseId ?? 0);
$blockedDates      = $blockedDates      ?? [];
$allBlockedDates   = $allBlockedDates   ?? [];

$success_message = $success_message ?? null;
$error_message   = $error_message   ?? null;
?>

<div class="p-4 md:p-8 max-w-[1400px] mx-auto space-y-7">

    <!-- ── 1. FLASH NOTIFICATIONS ────────────────────────────────────────────── -->
    <?php if (!empty($success_message)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-2xs animate-fade-in">
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
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-2xs animate-fade-in">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600 text-2xl">error</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($error_message) ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- ── 2. HERO BANNER & STATS ────────────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-extrabold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px]">event_busy</span>
                    Availability &amp; Locks
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-xs font-extrabold">
                    <?= count($allBlockedDates) ?> Locked Windows
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-headline">
                Lock Dates &amp; Offline Bookings
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Manually block specific dates when you receive offline bookings or schedule maintenance. Locked dates cannot be booked online.
            </p>
        </div>
    </div>

    <!-- ── 3. PROPERTY SELECTOR BAR ───────────────────────────────────────────── -->
    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-2xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">villa</span>
            </div>
            <div>
                <label for="farmhouse_select" class="text-xs font-extrabold text-slate-400 uppercase tracking-wider block">Selected Property</label>
                <span class="text-base font-black text-slate-800 font-headline">
                    <?= htmlspecialchars($selectedFarmhouse['title'] ?? 'No Farmhouse Selected') ?>
                </span>
            </div>
        </div>

        <form method="GET" action="<?= url('owner/blocked-dates') ?>" class="w-full md:w-auto flex items-center gap-3">
            <select id="farmhouse_select" name="farmhouse_id" onchange="this.form.submit()"
                    class="w-full md:w-64 px-4 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 text-slate-800 text-xs font-bold focus:outline-none focus:border-primary focus:bg-white transition-all">
                <?php foreach ($farmhouses as $fh): ?>
                    <option value="<?= (int)$fh['id'] ?>" <?= ((int)$fh['id'] === $farmhouseId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fh['title']) ?> (<?= htmlspecialchars($fh['location']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (empty($farmhouses)): ?>
        <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-lg mx-auto shadow-2xs">
            <div class="w-16 h-16 bg-sky-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">villa</span>
            </div>
            <h2 class="font-headline font-black text-lg text-slate-900 mb-1">No Farmhouses Listed Yet</h2>
            <p class="text-xs text-slate-500 font-semibold mb-6">
                You must list at least one active property before managing date locks.
            </p>
            <a href="<?= url('owner/farmhouses/add') ?>"
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-3 rounded-2xl text-xs font-black shadow-sm text-decoration-none">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>List a New Farmhouse</span>
            </a>
        </div>
    <?php else: ?>

    <!-- ── 4. MAIN TWO-COLUMN GRID ────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">

        <!-- ── LEFT COLUMN: LOCK DATES FORM ───────────────────────────────────── -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">lock</span>
                    </div>
                    <div>
                        <h2 class="font-headline font-black text-base text-slate-900">Lock New Date Window</h2>
                        <p class="text-[11px] text-slate-400 font-semibold">Reserve dates offline or mark maintenance</p>
                    </div>
                </div>

                <form method="POST" action="<?= url('owner/blocked-dates/lock') ?>" class="space-y-4">
                    <input type="hidden" name="farmhouse_id" value="<?= $farmhouseId ?>">

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                            Start Date (Check-in) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="start_date" min="<?= date('Y-m-d') ?>" required
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-800 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                            End Date (Check-out) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="end_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-800 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                            Reason / Notes <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input type="text" name="reason" placeholder="e.g. Offline booking by Mr. Sharma / Repairs"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-md shadow-amber-500/20 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">lock</span>
                        <span>Lock Selected Dates</span>
                    </button>
                </form>

                <div class="mt-4 p-3.5 bg-amber-50/70 border border-amber-100 rounded-2xl flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-lg flex-shrink-0 mt-0.5">info</span>
                    <p class="text-[11px] text-amber-800 font-medium leading-relaxed">
                        Locking dates blocks online guest bookings immediately. You can unlock dates anytime to make them available again.
                    </p>
                </div>
            </div>
        </div>

        <!-- ── RIGHT COLUMN: CURRENT LOCKED DATES TABLE ───────────────────────── -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs">
                <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-100 flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">calendar_today</span>
                        </div>
                        <div>
                            <h2 class="font-headline font-black text-base text-slate-900">
                                Active Locked Date Windows
                            </h2>
                            <p class="text-[11px] text-slate-400 font-semibold">
                                Showing dates currently unavailable for <?= htmlspecialchars($selectedFarmhouse['title'] ?? 'this property') ?>
                            </p>
                        </div>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                        Total Locks: <?= count($blockedDates) ?>
                    </span>
                </div>

                <?php if (!empty($blockedDates)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                                    <th class="pb-3 px-3">Date Window</th>
                                    <th class="pb-3 px-3">Duration</th>
                                    <th class="pb-3 px-3">Reason / Source</th>
                                    <th class="pb-3 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                                <?php foreach ($blockedDates as $bd): 
                                    $st = strtotime($bd['start_date']);
                                    $en = strtotime($bd['end_date']);
                                    $nights = max(1, (int)ceil(($en - $st) / 86400));
                                    $isOwnerLock = ($bd['locked_by'] ?? 'admin') === 'owner';
                                ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-3 font-bold text-slate-900">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-amber-500 text-base">date_range</span>
                                                <span><?= date('d M Y', $st) ?> &rarr; <?= date('d M Y', $en) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-3 text-slate-600">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">
                                                <?= $nights ?> <?= $nights === 1 ? 'Night' : 'Nights' ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-3">
                                            <div class="space-y-0.5">
                                                <p class="text-slate-800 font-bold"><?= !empty($bd['reason']) ? htmlspecialchars($bd['reason']) : 'Manual Date Lock' ?></p>
                                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wide <?= $isOwnerLock ? 'text-amber-600' : 'text-purple-600' ?>">
                                                    <span class="w-1.5 h-1.5 rounded-full <?= $isOwnerLock ? 'bg-amber-500' : 'bg-purple-500' ?>"></span>
                                                    <?= $isOwnerLock ? 'Locked by Owner' : 'Locked by Admin' ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-3 text-right">
                                            <?php if ($isOwnerLock): ?>
                                                <form method="POST" action="<?= url('owner/blocked-dates/unlock') ?>" onsubmit="return confirm('Are you sure you want to unlock these dates? Guests will be able to book them online again.');" class="inline">
                                                    <input type="hidden" name="block_id" value="<?= (int)$bd['id'] ?>">
                                                    <input type="hidden" name="farmhouse_id" value="<?= $farmhouseId ?>">
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 hover:border-emerald-600 text-xs font-bold transition-all shadow-2xs">
                                                        <span class="material-symbols-outlined text-sm">lock_open</span>
                                                        <span>Unlock</span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-[11px] text-slate-400 font-bold italic" title="Admin locks must be managed by Admin Concierge">
                                                    Admin Locked
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-2xl">event_available</span>
                        </div>
                        <p class="text-sm font-bold text-slate-700">No dates locked for this property</p>
                        <p class="text-xs text-slate-400 mt-1">Use the form on the left to lock offline bookings or maintenance dates.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <?php endif; ?>

</div>

<?php 
include __DIR__ . "/../Includes/owner_footer.php"; 
?>
