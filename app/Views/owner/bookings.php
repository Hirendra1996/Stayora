<?php 
include __DIR__ . "/../Includes/owner_header.php"; 

$owner        = $owner        ?? [];
$bookings     = $bookings     ?? [];
$stats        = $stats        ?? [];
$farmhouses   = $farmhouses   ?? [];
$totalCount   = (int)($totalCount ?? 0);
$totalPages   = max(1, (int)($totalPages ?? 1));
$currentPage  = max(1, (int)($_GET['page'] ?? 1));
$perPageVal   = max(5, min(100, (int)($_GET['per_page'] ?? 10)));
$statusVal    = strtolower($_GET['status'] ?? 'all');
$farmVal      = trim($_GET['farmhouse_id'] ?? '');
$searchVal    = htmlspecialchars($_GET['q'] ?? '');
$sortVal      = strtolower($_GET['sort'] ?? 'newest');

$totalAll     = (int)($stats['total'] ?? 0);
$approvedCount= (int)($stats['approved'] ?? 0);
$pendingCount = (int)($stats['pending'] ?? 0);
$completedCount=(int)($stats['completed'] ?? 0);
$cancelledCount=(int)($stats['cancelled'] ?? 0);
$revenue      = (float)($stats['revenue'] ?? 0);

// Base URL for pagination
$currentParams = $_GET;
unset($currentParams['page']);
$paginationBase = '?' . http_build_query($currentParams) . (!empty($currentParams) ? '&' : '');
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
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-primary text-xs font-extrabold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px]">calendar_month</span>
                    Reservations Hub
                </span>
                <?php if ($approvedCount > 0): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <?= $approvedCount ?> Confirmed
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-headline">
                Guest Bookings &amp; Stays
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Monitor guest schedules, check-in timelines, and stay fulfillment across your farmhouses.
            </p>
        </div>

        <!-- Privacy Assurance Pill -->
        <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-indigo-50/80 border border-indigo-100 text-indigo-900 text-xs font-semibold shadow-2xs">
            <span class="material-symbols-outlined text-indigo-600 text-[18px]">verified_user</span>
            <span>Guest communications &amp; verification managed via <strong>FarmLelo Admin Concierge</strong></span>
        </div>
    </div>

    <!-- ── 3. KPI METRIC CARDS ───────────────────────────────────────────────── -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Bookings -->
        <a href="?status=all" class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center justify-between shadow-2xs hover:border-primary hover:shadow-xs transition-all text-decoration-none group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 group-hover:bg-sky-50 group-hover:text-primary transition-all">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 font-headline leading-tight"><?= $totalAll ?></div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Bookings</div>
                </div>
            </div>
        </a>

        <!-- Confirmed / Approved -->
        <a href="?status=approved" class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center justify-between shadow-2xs hover:border-emerald-400 hover:shadow-xs transition-all text-decoration-none group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition-all">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 font-headline leading-tight"><?= $approvedCount ?></div>
                    <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Confirmed Stays</div>
                </div>
            </div>
            <span class="text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Live</span>
        </a>

        <!-- Completed Stays -->
        <a href="?status=completed" class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center justify-between shadow-2xs hover:border-sky-400 hover:shadow-xs transition-all text-decoration-none group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 group-hover:scale-105 transition-all">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 font-headline leading-tight"><?= $completedCount ?></div>
                    <div class="text-[11px] font-bold text-sky-600 uppercase tracking-wider">Completed Stays</div>
                </div>
            </div>
        </a>

        <!-- Pending Review -->
        <a href="?status=pending" class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center justify-between shadow-2xs hover:border-amber-400 hover:shadow-xs transition-all text-decoration-none group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:scale-105 transition-all">
                    <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 font-headline leading-tight"><?= $pendingCount ?></div>
                    <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Pending Admin</div>
                </div>
            </div>
        </a>
    </div>

    <!-- ── 4. FILTER CONTROLS & SEARCH BAR ───────────────────────────────────── -->
    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-2xs space-y-4">
        
        <!-- Status Tabs Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <a href="?status=all<?= !empty($searchVal) ? '&q=' . urlencode($searchVal) : '' ?><?= !empty($farmVal) ? '&farmhouse_id=' . urlencode($farmVal) : '' ?>" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-decoration-none <?= ($statusVal === 'all') ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' ?>">
                All Bookings (<?= $totalAll ?>)
            </a>
            <a href="?status=approved<?= !empty($searchVal) ? '&q=' . urlencode($searchVal) : '' ?><?= !empty($farmVal) ? '&farmhouse_id=' . urlencode($farmVal) : '' ?>" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-decoration-none <?= ($statusVal === 'approved') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' ?>">
                Confirmed (<?= $approvedCount ?>)
            </a>
            <a href="?status=completed<?= !empty($searchVal) ? '&q=' . urlencode($searchVal) : '' ?><?= !empty($farmVal) ? '&farmhouse_id=' . urlencode($farmVal) : '' ?>" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-decoration-none <?= ($statusVal === 'completed') ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' ?>">
                Completed (<?= $completedCount ?>)
            </a>
            <a href="?status=pending<?= !empty($searchVal) ? '&q=' . urlencode($searchVal) : '' ?><?= !empty($farmVal) ? '&farmhouse_id=' . urlencode($farmVal) : '' ?>" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-decoration-none <?= ($statusVal === 'pending') ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' ?>">
                Pending Review (<?= $pendingCount ?>)
            </a>
            <a href="?status=cancelled<?= !empty($searchVal) ? '&q=' . urlencode($searchVal) : '' ?><?= !empty($farmVal) ? '&farmhouse_id=' . urlencode($farmVal) : '' ?>" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all text-decoration-none <?= ($statusVal === 'cancelled') ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' ?>">
                Cancelled (<?= $cancelledCount ?>)
            </a>
        </div>

        <!-- Search & Property Dropdown Filter -->
        <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <?php if ($statusVal !== 'all'): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusVal) ?>">
            <?php endif; ?>

            <!-- Search input -->
            <div class="sm:col-span-5 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="q" value="<?= $searchVal ?>" placeholder="Search by property, guest name, or ID…" 
                       class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:border-primary focus:outline-none transition-all">
            </div>

            <!-- Farmhouse Filter -->
            <div class="sm:col-span-4">
                <select name="farmhouse_id" onchange="this.form.submit()" 
                        class="w-full h-11 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:border-primary focus:outline-none transition-all">
                    <option value="">All My Farmhouses</option>
                    <?php foreach ($farmhouses as $fh): ?>
                        <option value="<?= $fh['id'] ?>" <?= ((string)$farmVal === (string)$fh['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fh['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sorting -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <select name="sort" onchange="this.form.submit()" 
                        class="w-full h-11 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:border-primary focus:outline-none transition-all">
                    <option value="newest" <?= ($sortVal === 'newest') ? 'selected' : '' ?>>Newest Bookings</option>
                    <option value="stay_date" <?= ($sortVal === 'stay_date') ? 'selected' : '' ?>>Upcoming Check-in</option>
                    <option value="oldest" <?= ($sortVal === 'oldest') ? 'selected' : '' ?>>Oldest First</option>
                    <option value="price_high" <?= ($sortVal === 'price_high') ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
                <?php if (!empty($searchVal) || !empty($farmVal) || $statusVal !== 'all'): ?>
                    <a href="<?= url('owner/bookings') ?>" class="h-11 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold text-decoration-none shrink-0" title="Reset Filters">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- ── 5. BOOKINGS LISTING / TABLE ──────────────────────────────────────── -->
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-2xs">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">event_busy</span>
                </div>
                <h3 class="text-base font-black text-slate-800 font-headline">No Bookings Found</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-5">
                    <?= ($statusVal !== 'all' || !empty($searchVal)) ? 'No reservation records match your active search filters.' : 'When guests book your farmhouses, confirmed stays will appear here automatically.' ?>
                </p>
                <a href="<?= url('owner/bookings') ?>" class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm text-decoration-none">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                    <span>View All Bookings</span>
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="py-4 px-5">Ref / Property</th>
                            <th class="py-4 px-4">Guest Details</th>
                            <th class="py-4 px-4">Stay Schedule</th>
                            <th class="py-4 px-4">Party Size</th>
                            <th class="py-4 px-4">Total Amount</th>
                            <th class="py-4 px-4">Status</th>
                            <th class="py-4 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php foreach ($bookings as $b): 
                            $st = strtolower($b['status'] ?? 'pending');
                            $bId = (int)$b['booking_id'];
                            $encId = htmlspecialchars($b['encrypted_id'] ?? '');
                            $thumb = farmhouse_img_url($b['farmhouse_thumb'] ?? null);
                            $ci = !empty($b['check_in']) ? date('d M Y', strtotime($b['check_in'])) : 'N/A';
                            $co = !empty($b['check_out']) ? date('d M Y', strtotime($b['check_out'])) : 'N/A';
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Property Info -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= $thumb ?>" alt="" class="w-12 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                        <div class="min-w-0">
                                            <span class="text-[10px] font-extrabold text-primary uppercase tracking-wider">#<?= $bId ?></span>
                                            <h4 class="font-black text-slate-900 text-xs truncate leading-tight mt-0.5">
                                                <?= htmlspecialchars($b['farmhouse_title'] ?? 'Farmhouse') ?>
                                            </h4>
                                            <p class="text-[11px] text-slate-400 font-semibold truncate">
                                                <?= htmlspecialchars($b['farmhouse_location'] ?? 'India') ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Guest Info (Redacted Contact) -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-400 to-primary text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            <?= strtoupper(substr($b['guest_name'] ?? 'G', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 leading-tight"><?= htmlspecialchars($b['guest_name'] ?? 'Guest') ?></p>
                                            <span class="text-[10px] text-slate-400 flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-[12px] text-emerald-500">lock</span>
                                                <span>Verified Guest</span>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Schedule -->
                                <td class="py-4 px-4">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                            <span><?= $ci ?></span>
                                            <span class="text-slate-300">→</span>
                                            <span><?= $co ?></span>
                                        </div>
                                        <p class="text-[10.5px] text-slate-400 font-semibold">
                                            <?= (int)($b['nights'] ?? 1) ?> Night(s) Stay
                                        </p>
                                    </div>
                                </td>

                                <!-- Booking Type & Party Size -->
                                <td class="py-4 px-4">
                                    <div class="space-y-1">
                                        <?php 
                                            $bType = strtolower($b['booking_type'] ?? 'complete');
                                            $rCount = max(1, (int)($b['rooms'] ?? 1));
                                            $rtName = !empty($b['room_type_name']) ? $b['room_type_name'] : null;
                                        ?>
                                        <?php if ($bType === 'per_room'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]" title="<?= $rtName ? htmlspecialchars($rtName) : 'Per Room Booking' ?>">
                                                <span class="material-symbols-outlined text-[12px]">bed</span> <?= $rCount ?> <?= $rCount === 1 ? 'Room' : 'Rooms' ?><?= $rtName ? ' (' . htmlspecialchars($rtName) . ')' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200 font-bold text-[10px]">
                                                <span class="material-symbols-outlined text-[12px]">villa</span> Complete Farm
                                            </span>
                                        <?php endif; ?>
                                        <div class="text-[11px] text-slate-600 font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs text-slate-400">group</span>
                                            <span><?= (int)($b['guests'] ?? 1) ?> Guests</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total Amount -->
                                <td class="py-4 px-4">
                                    <div class="font-black text-slate-900 text-xs font-headline">
                                        ₹<?= number_format((float)($b['price'] ?? 0)) ?>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-4">
                                    <?php if ($st === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Confirmed
                                        </span>
                                    <?php elseif ($st === 'completed'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-extrabold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Completed
                                        </span>
                                    <?php elseif ($st === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-extrabold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                        </span>
                                    <?php elseif ($st === 'cancelled'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-extrabold uppercase tracking-wider">
                                            Cancelled
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-extrabold uppercase tracking-wider">
                                            <?= ucfirst($st) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- View Dossier -->
                                        <button onclick="openBookingModal('<?= $encId ?>')" 
                                                class="p-2 rounded-xl bg-white border border-slate-200 hover:border-primary text-slate-600 hover:text-primary transition-all shadow-2xs" 
                                                title="View Booking Dossier">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        </button>

                                        <!-- Quick Status Change Actions -->
                                        <?php if ($st === 'approved'): ?>
                                            <form method="POST" action="<?= url('owner/bookings/update-status') ?>" onsubmit="return confirm('Mark booking #<?= $bId ?> as Completed?')" class="inline-block">
                                                <input type="hidden" name="booking_id" value="<?= $encId ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white border border-emerald-200 hover:border-emerald-500 transition-all shadow-2xs" title="Mark as Completed">
                                                    <span class="material-symbols-outlined text-[16px]">task_alt</span>
                                                </button>
                                            </form>

                                            <form method="POST" action="<?= url('owner/bookings/update-status') ?>" onsubmit="return confirm('Are you sure you want to cancel booking #<?= $bId ?>? This will free the calendar dates.')" class="inline-block">
                                                <input type="hidden" name="booking_id" value="<?= $encId ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-200 hover:border-rose-500 transition-all shadow-2xs" title="Cancel Booking">
                                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <p>Showing page <strong><?= $currentPage ?></strong> of <strong><?= $totalPages ?></strong> (<?= $totalCount ?> total records)</p>
                    <div class="flex items-center gap-1.5">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= $paginationBase ?>page=<?= $currentPage - 1 ?>" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-decoration-none">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </a>
                        <?php endif; ?>

                        <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                            <a href="<?= $paginationBase ?>page=<?= $p ?>" class="px-3 py-1.5 rounded-xl font-bold text-decoration-none <?= ($p === $currentPage) ? 'bg-primary text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= $paginationBase ?>page=<?= $currentPage + 1 ?>" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-decoration-none">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!--  BOOKING DOSSIER INSPECTOR MODAL                              -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="bookingModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200 p-6 md:p-7 relative animate-scale-up">
        
        <!-- Close Button -->
        <button onclick="closeBookingModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>

        <div id="modalLoading" class="text-center py-12">
            <div class="w-8 h-8 border-3 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-xs font-bold text-slate-400">Loading booking dossier…</p>
        </div>

        <div id="modalContent" class="hidden space-y-5">
            <!-- Header -->
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span id="mStatusBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase"></span>
                    <span class="text-[11px] font-extrabold text-slate-400" id="mBookingRef"></span>
                </div>
                <h3 class="text-xl font-black text-slate-900 font-headline" id="mFarmTitle"></h3>
                <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                    <span class="material-symbols-outlined text-xs text-slate-400">location_on</span>
                    <span id="mFarmLocation"></span>
                </p>
            </div>

            <!-- Privacy Notice Banner -->
            <div class="p-3 rounded-2xl bg-sky-50 border border-sky-100 flex items-start gap-2.5 text-xs text-sky-900">
                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">security</span>
                <p class="text-[11.5px] leading-relaxed">
                    <strong>Guest Privacy Safeguard:</strong> Direct contact details (phone, email) are kept confidential. The FarmLelo concierge is actively coordinating with this guest for smooth check-in.
                </p>
            </div>

            <!-- Key Details Grid -->
            <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Booking Option</span>
                    <span class="font-bold text-slate-800" id="mBookingType">Complete Farmhouse</span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Guest Name</span>
                    <span class="font-bold text-slate-800" id="mGuestName"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Guests &amp; Allocation</span>
                    <span class="font-bold text-slate-800" id="mPartySize"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Stay Duration</span>
                    <span class="font-bold text-slate-800" id="mStayNights"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Check-In</span>
                    <span class="font-bold text-slate-800" id="mCheckIn"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Check-Out</span>
                    <span class="font-bold text-slate-800" id="mCheckOut"></span>
                </div>
                <div class="col-span-2 pt-2 border-t border-slate-200/60 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Total Booking Amount:</span>
                    <span class="font-black text-primary text-base font-headline" id="mPrice"></span>
                </div>
            </div>

            <!-- Guest Special Request Notes -->
            <div id="mNotesWrap" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Guest Message / Special Notes</span>
                <p class="text-xs text-slate-600 italic" id="mNotes">No special requests noted.</p>
            </div>

            <!-- Actions inside Modal -->
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeBookingModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Close
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function openBookingModal(encId) {
    const modal = document.getElementById('bookingModal');
    const loading = document.getElementById('modalLoading');
    const content = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    fetch(`<?= url('owner/bookings/details') ?>?id=${encodeURIComponent(encId)}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success || !res.data) {
                alert(res.message || 'Unable to load booking details.');
                closeBookingModal();
                return;
            }
            const d = res.data;
            const isPerRoom = (d.booking_type === 'per_room');
            const roomCount = d.rooms_allocated || d.rooms || 1;

            document.getElementById('mBookingRef').textContent = `Ref #${d.booking_id}`;
            document.getElementById('mFarmTitle').textContent = d.farmhouse_title || 'Farmhouse';
            const rtName = d.room_type_name ? d.room_type_name : 'Per Room';
            const pricePerRoom = d.price_per_room ? ` (₹${Number(d.price_per_room).toLocaleString('en-IN')}/rm)` : '';

            document.getElementById('mBookingType').innerHTML = isPerRoom 
                ? `<span class="text-emerald-700 font-extrabold">🛏️ ${rtName}${pricePerRoom}</span>` 
                : `<span class="text-sky-700 font-extrabold">🏡 Complete Farm</span>`;
            document.getElementById('mGuestName').textContent = d.guest_name || 'Guest User';
            document.getElementById('mPartySize').textContent = isPerRoom
                ? `${d.guests || 1} Guests (${roomCount} × ${rtName})`
                : `${d.guests || 1} Guests (Full Estate)`;
            document.getElementById('mCheckIn').textContent = `${d.formatted_check_in || 'N/A'} ${d.check_in_time ? `(${d.check_in_time})` : ''}`;
            document.getElementById('mCheckOut').textContent = `${d.formatted_check_out || 'N/A'} ${d.check_out_time ? `(${d.check_out_time})` : ''}`;
            document.getElementById('mStayNights').textContent = `${d.stay_nights || 1} Night(s)`;
            document.getElementById('mPrice').textContent = `₹${d.formatted_price || '0'}`;
            document.getElementById('mNotes').textContent = d.message ? `"${d.message}"` : 'No special requests submitted.';

            const badge = document.getElementById('mStatusBadge');
            const st = (d.status || 'pending').toLowerCase();
            if (st === 'approved') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800';
                badge.textContent = 'Confirmed Stay';
            } else if (st === 'completed') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-800';
                badge.textContent = 'Completed';
            } else if (st === 'pending') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800';
                badge.textContent = 'Pending Admin';
            } else if (st === 'cancelled') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800';
                badge.textContent = 'Cancelled';
            } else {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-800';
                badge.textContent = st;
            }

            loading.classList.add('hidden');
            content.classList.remove('hidden');
        })
        .catch(err => {
            alert('Failed to connect to server.');
            closeBookingModal();
        });
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}
</script>

<?php include __DIR__ . "/../Includes/owner_footer.php"; ?>
