<?php
$pageTitle  = "Booking Requests";
$activePage = "bookings";

include __DIR__ . "/../Includes/admin_header.php";

// Query parameters for pagination and filtering
$currentQuery = $_GET;
unset($currentQuery['page']);
$baseUrl = "?" . http_build_query($currentQuery) . (empty($currentQuery) ? "" : "&");

$searchVal   = htmlspecialchars($_GET['q'] ?? '');
$statusVal   = strtolower($_GET['status'] ?? 'all');
$farmVal     = trim($_GET['farmhouse_id'] ?? '');
$sortVal     = strtolower($_GET['sort'] ?? 'newest');
$perPageVal  = (int) ($_GET['per_page'] ?? 10);

$totalAll     = (int) ($stats['total'] ?? 0);
$pendingCount = (int) ($stats['pending'] ?? 0);
$approvedCount= (int) ($stats['approved'] ?? 0);
$rejectCount  = (int) ($stats['rejected'] ?? 0);
$pipelineRev  = (float) ($stats['pipeline_revenue'] ?? 0);
$approvedRev  = (float) ($stats['approved_revenue'] ?? 0);

$approvalPercent = $totalAll > 0 ? round(($approvedCount / $totalAll) * 100) : 0;
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
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    Guest Reservations Console
                </span>
                <?php if ($pendingCount > 0): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <?= $pendingCount ?> Needs Action
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                Booking Requests
            </h1>
            <p class="text-muted text-sm mt-1">
                Review, verify, approve and manage guest reservations across all partner farmhouses.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <a href="<?= url('admin/blocked-dates') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold text-xs border border-amber-200 shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-amber-600 text-lg">event_busy</span>
                Date Locks
            </a>
            <a href="<?= url('admin/booking-requests/export') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs border border-border shadow-sm hover:border-sky/40 transition-all active:scale-95">
                <span class="material-symbols-outlined text-sky text-lg">download</span>
                Export CSV
            </a>
            <a href="<?= url('admin/managefarmhouses') ?>" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">cottage</span>
                Manage Estates
            </a>
        </div>
    </div>

    <!-- ── 3. COMPACT KPI METRIC BAR ───────────────────────────────────────── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        
        <!-- Total Inquiries -->
        <a href="?status=all" class="bg-white rounded-xl border border-border p-3 flex items-center justify-between shadow-2xs hover:border-indigo-400 hover:shadow-xs transition-all group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-105 transition-transform shrink-0">
                    <span class="material-symbols-outlined text-lg">calendar_month</span>
                </div>
                <div class="min-w-0">
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $totalAll) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider truncate mt-0.5">Total Inquiries</div>
                </div>
            </div>
            <span class="text-[10px] font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md shrink-0">
                <?= $approvalPercent ?>% Confirmed
            </span>
        </a>

        <!-- Pending Review -->
        <a href="?status=pending" class="bg-white rounded-xl border <?= $pendingCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-border' ?> p-3 flex items-center justify-between shadow-2xs hover:border-amber-400 hover:shadow-xs transition-all group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 group-hover:scale-105 transition-transform shrink-0">
                    <span class="material-symbols-outlined text-lg">pending_actions</span>
                </div>
                <div class="min-w-0">
                    <div class="text-xl font-black text-ink leading-none flex items-center gap-1.5">
                        <?= sprintf('%02d', $pendingCount) ?>
                        <?php if ($pendingCount > 0): ?>
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider truncate mt-0.5">Needs Review</div>
                </div>
            </div>
            <span class="text-[10px] font-extrabold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md shrink-0">
                ₹<?= number_format($pipelineRev) ?>
            </span>
        </a>

        <!-- Approved Stays -->
        <a href="?status=approved" class="bg-white rounded-xl border border-border p-3 flex items-center justify-between shadow-2xs hover:border-emerald-400 hover:shadow-xs transition-all group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition-transform shrink-0">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                </div>
                <div class="min-w-0">
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $approvedCount) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider truncate mt-0.5">Approved Stays</div>
                </div>
            </div>
            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md shrink-0">
                ₹<?= number_format($approvedRev) ?>
            </span>
        </a>

        <!-- Rejected / Cancelled -->
        <a href="?status=rejected" class="bg-white rounded-xl border border-border p-3 flex items-center justify-between shadow-2xs hover:border-rose-400 hover:shadow-xs transition-all group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 group-hover:scale-105 transition-transform shrink-0">
                    <span class="material-symbols-outlined text-lg">cancel</span>
                </div>
                <div class="min-w-0">
                    <div class="text-xl font-black text-ink leading-none"><?= sprintf('%02d', $rejectCount) ?></div>
                    <div class="text-[10px] font-bold text-muted uppercase tracking-wider truncate mt-0.5">Rejected / Closed</div>
                </div>
            </div>
            <span class="text-[10px] font-extrabold text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md shrink-0">
                Unavailable
            </span>
        </a>

    </div>

    <!-- ── 4. FILTER, SEARCH & SORT CONTROL BAR ──────────────────────────────── -->
    <div class="bg-white rounded-2xl border border-border p-4 md:p-5 shadow-sm space-y-4">
        
        <form method="GET" action="" id="bookingFilterForm" class="space-y-4">
            
            <!-- Top Row: Search & Dropdown Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                
                <!-- Search Field -->
                <div class="<?= !empty($farmhouses) ? 'lg:col-span-5' : 'lg:col-span-8' ?> relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-xl pointer-events-none">search</span>
                    <input type="text" 
                           name="q" 
                           value="<?= $searchVal ?>" 
                           placeholder="Search by guest name, phone, email, farm title, booking ID..." 
                           class="w-full bg-surface border border-border rounded-xl py-2.5 pl-10 pr-10 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                    <?php if (!empty($searchVal)): ?>
                    <a href="?<?= http_build_query(array_diff_key($_GET, ['q' => ''])) ?>" 
                       class="absolute right-3.5 top-1/2 -translate-y-1/2 text-muted hover:text-ink text-sm">
                        <span class="material-symbols-outlined text-base">close</span>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Farmhouse Filter -->
                <?php if (!empty($farmhouses)): ?>
                <div class="lg:col-span-3">
                    <select name="farmhouse_id" onchange="document.getElementById('bookingFilterForm').submit()" 
                            class="w-full bg-surface border border-border rounded-xl py-2.5 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 truncate">
                        <option value="">All Farmhouse Estates</option>
                        <?php foreach ($farmhouses as $fh): ?>
                            <option value="<?= $fh['id'] ?>" <?= ($farmVal === (string)$fh['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fh['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Sort Dropdown -->
                <div class="lg:col-span-2">
                    <select name="sort" onchange="document.getElementById('bookingFilterForm').submit()" 
                            class="w-full bg-surface border border-border rounded-xl py-2.5 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="newest" <?= $sortVal === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= $sortVal === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="stay_date" <?= $sortVal === 'stay_date' ? 'selected' : '' ?>>Stay Date (Upcoming)</option>
                        <option value="price_high" <?= $sortVal === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="price_low" <?= $sortVal === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="guests_high" <?= $sortVal === 'guests_high' ? 'selected' : '' ?>>Most Guests</option>
                    </select>
                </div>

                <!-- Per Page Dropdown -->
                <div class="lg:col-span-2">
                    <select name="per_page" onchange="document.getElementById('bookingFilterForm').submit()" 
                            class="w-full bg-surface border border-border rounded-xl py-2.5 px-3 text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="10" <?= $perPageVal === 10 ? 'selected' : '' ?>>10 / page</option>
                        <option value="25" <?= $perPageVal === 25 ? 'selected' : '' ?>>25 / page</option>
                        <option value="50" <?= $perPageVal === 50 ? 'selected' : '' ?>>50 / page</option>
                    </select>
                </div>

            </div>

            <!-- Bottom Row: Scrollable Status Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none pt-1 border-t border-border/40">
                <button type="submit" name="status" value="all" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all <?= ($statusVal === 'all' || empty($statusVal)) ? 'bg-sky text-white shadow-xs' : 'bg-surface text-muted hover:text-ink border border-border/60' ?>">
                    All (<?= $totalAll ?>)
                </button>
                <button type="submit" name="status" value="pending" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all <?= ($statusVal === 'pending') ? 'bg-amber-500 text-white shadow-xs' : 'bg-surface text-muted hover:text-ink border border-border/60' ?>">
                    Pending (<?= $pendingCount ?>)
                </button>
                <button type="submit" name="status" value="approved" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all <?= ($statusVal === 'approved') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-surface text-muted hover:text-ink border border-border/60' ?>">
                    Approved (<?= $approvedCount ?>)
                </button>
                <button type="submit" name="status" value="completed" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all <?= ($statusVal === 'completed') ? 'bg-sky text-white shadow-xs' : 'bg-surface text-muted hover:text-ink border border-border/60' ?>">
                    Completed (<?= (int)($stats['completed'] ?? 0) ?>)
                </button>
                <button type="submit" name="status" value="cancelled" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all <?= ($statusVal === 'cancelled') ? 'bg-rose-600 text-white shadow-xs' : 'bg-surface text-muted hover:text-ink border border-border/60' ?>">
                    Cancelled (<?= (int)($stats['cancelled'] ?? 0) ?>)
                </button>
                <button type="submit" name="status" value="rejected" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-all <?= ($statusVal === 'rejected') ? 'bg-rose-600 text-white shadow-xs' : 'bg-surface text-muted hover:text-ink border border-border/60' ?>">
                    Rejected (<?= $rejectCount ?>)
                </button>
            </div>

        </form>

        <!-- ── Bulk Action Floating Strip ── -->
        <div id="bookingBulkBar" class="hidden items-center justify-between bg-ink text-white p-3.5 rounded-xl shadow-lg border border-sky/30 animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky animate-pulse"></span>
                <span id="bookingSelectedCount" class="text-xs font-bold text-sky-l">0 bookings selected</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitBookingBulkAction('approved')" 
                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                    Approve Selected
                </button>
                <button type="button" onclick="submitBookingBulkAction('rejected')" 
                        class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-colors">
                    Reject Selected
                </button>
                <button type="button" onclick="submitBookingBulkDelete()" 
                        class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                    Delete
                </button>
                <button type="button" onclick="deselectAllBookings()" 
                        class="px-2.5 py-1.5 text-xs text-stone-300 hover:text-white underline">
                    Clear
                </button>
            </div>
        </div>

    </div>

    <!-- ── 5. BOOKINGS DATA TABLE ────────────────────────────────────────────── -->
    <div class="bg-white rounded-3xl border border-border shadow-sm overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                
                <thead>
                    <tr class="bg-surface border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-4 px-4 w-12 text-center">
                            <input type="checkbox" id="selectAllBookingsCheckbox" onchange="toggleSelectAllBookings(this)" 
                                   class="rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-4 px-4">Guest & Inquiry</th>
                        <th class="py-4 px-4">Farmhouse Estate</th>
                        <th class="py-4 px-4">Stay Schedule</th>
                        <th class="py-4 px-4 text-center">Total Price</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border/60 text-sm">
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $bk): 
                            $encId      = $bk['encrypted_id'];
                            $name       = $bk['cust_name'] ?? 'Guest User';
                            $email      = $bk['cust_email'] ?? 'No Email';
                            $phone      = $bk['cust_phone'] ?? 'N/A';
                            $status     = strtolower($bk['req_status'] ?? 'pending');
                            $isPending  = ($status === 'pending');
                            $isApproved = ($status === 'approved');
                            $isRejected = ($status === 'rejected');
                            $cleanDial  = preg_replace('/[^0-9]/', '', $phone);
                            
                            $initials   = strtoupper(substr($name, 0, 1) . (strpos($name, ' ') !== false ? substr(explode(' ', $name)[1], 0, 1) : ''));
                            
                            $checkIn    = !empty($bk['check_in']) ? $bk['check_in'] : ($bk['start_date'] ?? null);
                            $checkOut   = !empty($bk['check_out']) ? $bk['check_out'] : ($bk['end_date'] ?? null);
                            $checkInFmt = !empty($checkIn) ? date('d M Y', strtotime($checkIn)) : '—';
                            $checkOutFmt= !empty($checkOut) ? date('d M Y', strtotime($checkOut)) : '—';

                            $nights = 1;
                            if (!empty($checkIn) && !empty($checkOut)) {
                                $diff = (strtotime($checkOut) - strtotime($checkIn)) / 86400;
                                $nights = max(1, (int) ceil($diff));
                            }

                            $price = (float) ($bk['price'] ?? 0);
                            $reqTime = !empty($bk['req_date']) ? date('d M, h:i A', strtotime($bk['req_date'])) : '—';
                            
                            $thumb = farmhouse_img_url($bk['farm_thumb'] ?? null);
                        ?>
                        <tr class="hover:bg-surface/50 transition-colors">
                            
                            <!-- Checkbox -->
                            <td class="py-4 px-4 text-center">
                                <input type="checkbox" value="<?= $encId ?>" class="booking-checkbox rounded border-border text-sky focus:ring-sky/30 w-4 h-4 cursor-pointer" onchange="onBookingRowCheckboxChange()">
                            </td>

                            <!-- Guest Profile -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-100 to-sky-l border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0 shadow-sm">
                                        <?= $initials ?>
                                    </div>
                                    <div class="min-w-0">
                                        <button type="button" onclick="inspectBooking('<?= $encId ?>')" class="font-bold text-ink hover:text-sky transition-colors truncate block text-left">
                                            <?= htmlspecialchars($name) ?>
                                        </button>
                                        <div class="flex items-center gap-2 text-[11px] text-muted mt-0.5">
                                            <span class="font-semibold text-indigo-700">Req #<?= $bk['req_id'] ?></span>
                                            <span>•</span>
                                            <span><?= $reqTime ?></span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-muted font-medium"><?= htmlspecialchars($phone) ?></span>
                                            <?php if (!empty($cleanDial)): ?>
                                                <a href="https://wa.me/91<?= htmlspecialchars($cleanDial) ?>?text=Hello%20<?= urlencode($name) ?>,%20regarding%20your%20booking%20request%20on%20Farmlelo." target="_blank" 
                                                   class="w-5 h-5 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Message on WhatsApp">
                                                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Farmhouse Estate -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2.5">
                                    <img src="<?= $thumb ?>" class="w-10 h-10 rounded-xl object-cover bg-surface shrink-0 border border-border shadow-2xs" onerror="this.src='https://placehold.co/100x100/E6F6FD/16A5DE?text=Farm'">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <a href="<?= url('admin/editfarm?id=' . \App\Helpers\CryptoHelper::encrypt((string)$bk['farmhouse_id'])) ?>" class="font-bold text-ink hover:text-sky transition-colors text-xs truncate block max-w-[180px]">
                                                <?= htmlspecialchars($bk['farm_title'] ?? ('Farmhouse #' . ($bk['farmhouse_id'] ?? ''))) ?>
                                            </a>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                <?= htmlspecialchars($bk['farm_category'] ?? 'Farmhouse') ?>
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-muted truncate max-w-[180px]"><?= htmlspecialchars($bk['farm_location'] ?? 'Location') ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Stay Schedule -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    <div class="text-xs font-bold text-ink flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[14px] text-sky">event</span>
                                        <?= $checkInFmt ?> → <?= $checkOutFmt ?>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
                                        <?php 
                                            $bType = strtolower($bk['booking_type'] ?? 'complete');
                                            $rCount = max(1, (int)($bk['rooms'] ?? 1));
                                            $rtName = !empty($bk['room_type_name']) ? $bk['room_type_name'] : null;
                                        ?>
                                        <?php if ($bType === 'per_room'): ?>
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 font-bold text-emerald-700 border border-emerald-200 inline-flex items-center gap-1" title="<?= $rtName ? htmlspecialchars($rtName) : 'Per Room Booking' ?>">
                                                <span class="material-symbols-outlined text-[12px]">bed</span> <?= $rCount ?> <?= $rCount === 1 ? 'Room' : 'Rooms' ?><?= $rtName ? ' (' . htmlspecialchars($rtName) . ')' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-md bg-sky-50 font-bold text-sky-700 border border-sky-200 inline-flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">villa</span> Complete Farm
                                            </span>
                                        <?php endif; ?>
                                        <span class="px-2 py-0.5 rounded-md bg-surface font-semibold text-muted border border-border">
                                            <?= $nights ?> <?= $nights === 1 ? 'Night' : 'Nights' ?>
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 font-bold text-indigo-700 border border-indigo-200">
                                            <?= $bk['guests'] ?? 1 ?> Guests
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Total Price -->
                            <td class="py-4 px-4 text-center">
                                <div class="text-xs font-extrabold text-ink">₹<?= number_format($price) ?></div>
                                <div class="text-[10px] text-muted font-normal">total stay</div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                <?php if ($isPending): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                <?php elseif ($isApproved): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                    </span>
                                <?php elseif ($status === 'completed'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Completed
                                    </span>
                                <?php elseif ($status === 'cancelled'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rejected
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-1 justify-end">
                                    
                                    <!-- Inspect Dossier -->
                                    <button type="button" onclick="inspectBooking('<?= $encId ?>')" 
                                            class="w-8 h-8 rounded-lg bg-surface hover:bg-sky-xl text-ink hover:text-sky flex items-center justify-center transition-colors shadow-2xs" title="View Booking Details">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>

                                    <!-- Quick Approve (if not approved) -->
                                    <?php if (!$isApproved): ?>
                                    <form method="POST" action="<?= url('admin/booking-requests') ?>" class="inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($encId) ?>">
                                        <input type="hidden" name="new_status" value="approved">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Approve Request">
                                            <span class="material-symbols-outlined text-lg">check</span>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Quick Reject (if not rejected) -->
                                    <?php if (!$isRejected): ?>
                                    <form method="POST" action="<?= url('admin/booking-requests') ?>" class="inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($encId) ?>">
                                        <input type="hidden" name="new_status" value="rejected">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Reject Request">
                                            <span class="material-symbols-outlined text-lg">close</span>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Delete Request -->
                                    <form method="POST" action="<?= url('admin/booking-requests') ?>" class="inline" onsubmit="return confirm('Permanently remove booking request #<?= $bk['req_id'] ?>?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($encId) ?>">
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors shadow-2xs" title="Delete Record">
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
                                <span class="material-symbols-outlined text-5xl text-muted/40 mb-2 block">event_busy</span>
                                <h4 class="font-bold text-ink text-base">No Booking Requests Found</h4>
                                <p class="text-xs text-muted mt-1 max-w-sm mx-auto">
                                    No reservation inquiries match your selected filters.
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

        <!-- ── 6. NUMBERED PAGINATION CONTROLS ───────────────────────────────────── -->
        <?php if ($totalPages > 1): 
            $startItem = ($currentPage - 1) * $perPageVal + 1;
            $endItem   = min($currentPage * $perPageVal, $totalCount);
        ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-surface/40 border-t border-border">
            
            <div class="text-xs text-muted font-medium order-2 sm:order-1">
                Showing <span class="text-ink font-bold"><?= $startItem ?></span> to 
                <span class="text-ink font-bold"><?= $endItem ?></span> of 
                <span class="text-ink font-bold"><?= $totalCount ?></span> requests
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

<!-- ── 7. BOOKING DOSSIER INSPECTOR MODAL ─────────────────────────────────── -->
<div id="inspectBookingModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-ink/70 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-3xl w-full max-w-2xl p-6 sm:p-8 shadow-2xl border border-border max-h-[90vh] overflow-y-auto space-y-6">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-start pb-4 border-b border-border">
            <div>
                <div class="flex items-center gap-2">
                    <h3 id="bk_dossier_title" class="text-xl font-extrabold text-ink">Reservation Request</h3>
                    <span id="bk_dossier_status_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                        Pending
                    </span>
                </div>
                <p id="bk_dossier_timestamp" class="text-xs text-muted mt-0.5">Submitted on ...</p>
            </div>
            
            <button type="button" onclick="closeInspectBookingModal()" class="w-8 h-8 rounded-full bg-surface hover:bg-sky-xl text-muted hover:text-ink flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <!-- Guest Profile & Direct Connect -->
        <div class="p-5 rounded-2xl bg-surface border border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div id="bk_dossier_avatar" class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-base flex items-center justify-center shadow-xs">
                    G
                </div>
                <div>
                    <strong id="bk_dossier_cust_name" class="text-sm font-bold text-ink block">Guest Name</strong>
                    <span id="bk_dossier_cust_contact" class="text-xs text-muted">guest@example.com • 9876543210</span>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a id="bk_dossier_whatsapp_btn" href="#" target="_blank" 
                   class="flex-1 sm:flex-none py-2 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-colors">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                    WhatsApp Guest
                </a>
                <a id="bk_dossier_call_btn" href="#" 
                   class="flex-1 sm:flex-none py-2 px-3.5 rounded-xl bg-white hover:bg-sky-xl text-ink border border-border text-xs font-bold flex items-center justify-center gap-1 transition-colors">
                    <span class="material-symbols-outlined text-sm text-sky">call</span> Call
                </a>
            </div>
        </div>

        <!-- Stay Schedule Itinerary Bento -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 p-4 rounded-2xl bg-sky-l/30 border border-sky/20 text-xs text-center">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Booking Mode</span>
                <span id="bk_dossier_type" class="font-extrabold text-sky-700 text-xs mt-0.5 block">Complete Farm</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Check-In</span>
                <span id="bk_dossier_checkin" class="font-extrabold text-ink text-sm mt-0.5 block">—</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Check-Out</span>
                <span id="bk_dossier_checkout" class="font-extrabold text-ink text-sm mt-0.5 block">—</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Duration</span>
                <span id="bk_dossier_nights" class="font-bold text-indigo-700 mt-0.5 block">1 Night</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Guests &amp; Allocation</span>
                <span id="bk_dossier_guests" class="font-bold text-emerald-600 mt-0.5 block">1 Guest</span>
            </div>
        </div>

        <!-- Farmhouse Info Card -->
        <div class="p-4 rounded-2xl bg-surface border border-border flex items-center gap-4">
            <img id="bk_dossier_farm_thumb" src="" class="w-16 h-16 rounded-2xl object-cover bg-stone-100 shrink-0 border border-border shadow-2xs">
            <div class="min-w-0 flex-1">
                <h4 id="bk_dossier_farm_title" class="font-bold text-ink text-sm truncate">Farmhouse Title</h4>
                <p id="bk_dossier_farm_location" class="text-xs text-muted truncate mt-0.5">Location</p>
                <div class="flex items-center justify-between mt-1 pt-1 border-t border-border/60 text-xs">
                    <span class="text-muted">Host: <strong id="bk_dossier_host_name" class="text-ink">Partner Host</strong></span>
                    <span class="font-extrabold text-ink">Total: <strong id="bk_dossier_total_price" class="text-sky font-black">₹0</strong></span>
                </div>
            </div>
        </div>

        <!-- Guest Message / Special Notes -->
        <div>
            <h4 class="text-[10px] font-bold uppercase tracking-wider text-muted mb-1">Customer Special Request / Notes</h4>
            <div id="bk_dossier_message" class="p-4 rounded-2xl bg-surface border border-border text-xs text-ink leading-relaxed">
                No special requests provided.
            </div>
        </div>

        <!-- Actions Inside Modal -->
        <div class="pt-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-3">
            <form id="bk_modal_action_form" method="POST" action="<?= url('admin/booking-requests') ?>" class="flex items-center flex-wrap gap-2 w-full sm:w-auto">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="request_id" id="bk_modal_req_id">
                
                <button type="submit" name="new_status" value="approved" 
                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition-all">
                    <span class="material-symbols-outlined text-sm">check</span> Confirm &amp; Approve
                </button>
                <button type="submit" name="new_status" value="completed" 
                        class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition-all">
                    <span class="material-symbols-outlined text-sm">task_alt</span> Complete Stay
                </button>
                <button type="submit" name="new_status" value="cancelled" 
                        class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition-all">
                    <span class="material-symbols-outlined text-sm">block</span> Cancel Stay
                </button>
                <button type="submit" name="new_status" value="rejected" 
                        class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition-all">
                    <span class="material-symbols-outlined text-sm">close</span> Reject
                </button>
            </form>

            <button type="button" onclick="closeInspectBookingModal()" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-surface hover:bg-stone-200 text-ink font-bold text-xs transition-colors">
                Dismiss
            </button>
        </div>

    </div>
</div>

<!-- Hidden Bulk Action Submission Form -->
<form id="bookingBulkActionForm" method="POST" action="<?= url('admin/booking-requests') ?>" class="hidden">
    <input type="hidden" name="action" id="booking_bulk_action_type">
    <input type="hidden" name="target_status" id="booking_bulk_target_status">
    <div id="booking_bulk_ids_container"></div>
</form>

<!-- ── 8. JAVASCRIPT LOGIC ──────────────────────────────────────────────── -->
<script>
    function closeInspectBookingModal() {
        document.getElementById('inspectBookingModal').classList.add('hidden');
    }

    // Inspect Booking Dossier
    async function inspectBooking(encryptedId) {
        try {
            const res = await fetch(`<?= url('admin/booking-requests/details') ?>?id=${encodeURIComponent(encryptedId)}`);
            const json = await res.json();

            if (!json.success) {
                alert(json.message || 'Failed to load booking details.');
                return;
            }

            const d = json.data;
            const initials = d.cust_name ? d.cust_name.charAt(0).toUpperCase() : 'G';

            document.getElementById('bk_dossier_title').textContent = `Reservation Request #${d.req_id}`;
            document.getElementById('bk_dossier_timestamp').textContent = `Submitted on ${d.formatted_created}`;
            document.getElementById('bk_dossier_avatar').textContent = initials;
            document.getElementById('bk_dossier_cust_name').textContent = d.cust_name || 'Guest User';
            document.getElementById('bk_dossier_cust_contact').textContent = `${d.cust_email || 'No email'} • ${d.cust_phone || 'No phone'}`;
            
            const isPerRoom = (d.booking_type === 'per_room');
            const roomNum = d.rooms || 1;
            const rtName = d.room_type_name ? d.room_type_name : 'Per Room';
            const pricePerRoom = d.price_per_room ? ` (₹${Number(d.price_per_room).toLocaleString('en-IN')}/rm)` : '';
            
            document.getElementById('bk_dossier_type').innerHTML = isPerRoom 
                ? `<span class="text-emerald-700 font-extrabold">🛏️ ${rtName}${pricePerRoom}</span>`
                : `<span class="text-sky-700 font-extrabold">🏡 Complete Farm</span>`;

            document.getElementById('bk_dossier_checkin').textContent = d.formatted_check_in;
            document.getElementById('bk_dossier_checkout').textContent = d.formatted_check_out;
            document.getElementById('bk_dossier_nights').textContent = `${d.stay_nights} ${d.stay_nights === 1 ? 'Night' : 'Nights'}`;
            document.getElementById('bk_dossier_guests').textContent = isPerRoom 
                ? `${d.guests || 1} Guests (${roomNum} × ${rtName})`
                : `${d.guests || 1} Guests (Full Estate)`;

            document.getElementById('bk_dossier_farm_title').textContent = d.farm_title || 'Untitled Estate';
            document.getElementById('bk_dossier_farm_location').textContent = `${d.farm_location || ''} • ${d.farm_address || ''}`;
            document.getElementById('bk_dossier_host_name').textContent = d.host_name || 'Partner Host';
            document.getElementById('bk_dossier_total_price').textContent = `₹${d.formatted_price}`;
            
            const thumbUrl = d.farm_thumb 
                ? (d.farm_thumb.startsWith('http') ? d.farm_thumb : `<?= asset('assets/images/uploads/') ?>/${d.farm_thumb}`)
                : 'https://placehold.co/100x100/E6F6FD/16A5DE?text=Farm';
            document.getElementById('bk_dossier_farm_thumb').src = thumbUrl;

            document.getElementById('bk_dossier_message').textContent = d.message || 'No special requests or messages provided by the guest.';

            // Status Badge
            const badge = document.getElementById('bk_dossier_status_badge');
            if (d.req_status === 'approved') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
                badge.textContent = 'Approved';
            } else if (d.req_status === 'rejected') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200';
                badge.textContent = 'Rejected';
            } else {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200';
                badge.textContent = 'Pending Review';
            }

            // Quick Connect Links
            const cleanPhone = (d.cust_phone || '').replace(/[^0-9]/g, '');
            document.getElementById('bk_dossier_whatsapp_btn').href = `https://wa.me/91${cleanPhone}?text=Hello%20${encodeURIComponent(d.cust_name || '')},%20contacting%20regarding%20your%20stay%20reservation%20at%20${encodeURIComponent(d.farm_title || '')}.`;
            document.getElementById('bk_dossier_call_btn').href = `tel:${cleanPhone}`;

            // Modal Action Form ID
            document.getElementById('bk_modal_req_id').value = d.encrypted_id;

            document.getElementById('inspectBookingModal').classList.remove('hidden');

        } catch (err) {
            alert('Error loading reservation dossier.');
        }
    }

    // ── Bulk Selection Helpers ──
    function toggleSelectAllBookings(master) {
        const checkboxes = document.querySelectorAll('.booking-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateBookingBulkBar();
    }

    function onBookingRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.booking-checkbox');
        const master = document.getElementById('selectAllBookingsCheckbox');
        const checked = document.querySelectorAll('.booking-checkbox:checked');
        if (master) {
            master.checked = (checkboxes.length > 0 && checked.length === checkboxes.length);
        }
        updateBookingBulkBar();
    }

    function updateBookingBulkBar() {
        const checked = document.querySelectorAll('.booking-checkbox:checked');
        const bar = document.getElementById('bookingBulkBar');
        const countSpan = document.getElementById('bookingSelectedCount');

        if (checked.length > 0) {
            countSpan.textContent = `${checked.length} reservation${checked.length === 1 ? '' : 's'} selected`;
            bar.classList.remove('hidden');
            bar.classList.add('flex');
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }

    function deselectAllBookings() {
        document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = false);
        const master = document.getElementById('selectAllBookingsCheckbox');
        if (master) master.checked = false;
        updateBookingBulkBar();
    }

    function submitBookingBulkAction(targetStatus) {
        const checked = document.querySelectorAll('.booking-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Update status of ${checked.length} reservation(s) to '${targetStatus}'?`)) return;

        const form = document.getElementById('bookingBulkActionForm');
        const container = document.getElementById('booking_bulk_ids_container');
        container.innerHTML = '';

        document.getElementById('booking_bulk_action_type').value = 'bulk_status';
        document.getElementById('booking_bulk_target_status').value = targetStatus;

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'request_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }

    function submitBookingBulkDelete() {
        const checked = document.querySelectorAll('.booking-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Permanently delete ${checked.length} reservation request(s)?`)) return;

        const form = document.getElementById('bookingBulkActionForm');
        const container = document.getElementById('booking_bulk_ids_container');
        container.innerHTML = '';

        document.getElementById('booking_bulk_action_type').value = 'bulk_delete';

        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'request_ids[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });

        form.submit();
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>