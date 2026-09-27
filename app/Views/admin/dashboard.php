<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// Dynamic Time-aware Greeting
$hour = (int) date('G');
if ($hour >= 5 && $hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}

// Dynamic Stats & Calculations
$totalFarmhouses   = (int) ($stats['total_farmhouses'] ?? 0);
$activeFarmhouses  = (int) ($stats['active_farmhouses'] ?? 0);
$inactiveFarmhouses = max(0, $totalFarmhouses - $activeFarmhouses);
$activeRatio       = $totalFarmhouses > 0 ? round(($activeFarmhouses / $totalFarmhouses) * 100) : 0;

$pendingBookings   = (int) ($stats['pending_booking_requests'] ?? 0);
$activeBookings    = (int) ($stats['active_bookings'] ?? 0);
$totalOwners       = (int) ($stats['total_owners'] ?? 0);
$newInquiriesCount = (int) ($stats['new_inquiries'] ?? 0);

// Avatar Colors palette
$avatarGradients = [
    'from-sky-500 to-blue-600 text-white',
    'from-emerald-500 to-teal-600 text-white',
    'from-amber-500 to-orange-600 text-white',
    'from-indigo-500 to-purple-600 text-white',
    'from-rose-500 to-pink-600 text-white',
];

include __DIR__ . "/../Includes/admin_header.php";
?>

<div class="space-y-8 pb-12">

    <!-- ── 1. WELCOME HERO BANNER ────────────────────────────────────────────── -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-white via-sky-xl to-sky-l/50 border border-border p-6 md:p-8 shadow-sm">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Control Center
                    </span>
                    <span class="text-xs font-semibold text-muted flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                        <?= date('l, d F Y') ?>
                    </span>
                </div>
                
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-['Open_Sans',sans-serif] text-ink tracking-tight">
                    <?= $greeting ?>, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrator') ?> 👋
                </h1>
                
                <p class="text-muted text-sm md:text-[15px] leading-relaxed">
                    Here is what's happening across Farmlelo today. You have <strong class="text-ink font-bold"><?= $pendingBookings ?> pending reservation<?= $pendingBookings === 1 ? '' : 's' ?></strong> and <strong class="text-ink font-bold"><?= count($inquiries ?? []) ?> recent customer lead<?= count($inquiries ?? []) === 1 ? '' : 's' ?></strong> to review.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center flex-wrap gap-3 shrink-0">
                <a href="<?= url('admin/addfarm') ?>" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs md:text-sm uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-lg">add_home_work</span>
                    Add New Estate
                </a>
                <a href="<?= url('admin/booking-requests') ?>" 
                   class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs md:text-sm border border-border shadow-sm hover:border-sky/40 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-sky text-lg">calendar_month</span>
                    Booking Requests
                </a>
            </div>

        </div>

        <!-- Decorative background subtle geometry -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-sky/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- ── 2. EXECUTIVE KPI BENTO GRID ───────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Metric 1: Total Estates -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-sky/40 transition-all group relative overflow-hidden flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">home_work</span>
                </div>
                <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2.5 py-1 rounded-md uppercase tracking-wider">
                    <?= $activeRatio ?>% Live
                </span>
            </div>
            
            <div>
                <div class="text-3xl lg:text-4xl font-extrabold font-['Open_Sans',sans-serif] text-ink tracking-tight">
                    <?= sprintf('%02d', $totalFarmhouses) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Listed Farmhouses
                </div>
            </div>

            <!-- Mini Progress Indicator -->
            <div class="mt-4 pt-4 border-t border-sky-l flex items-center justify-between text-xs font-medium text-muted">
                <span class="flex items-center gap-1.5 text-emerald-600 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <?= $activeFarmhouses ?> Active
                </span>
                <span class="text-muted/80">
                    <?= $inactiveFarmhouses ?> Inactive
                </span>
                <a href="<?= url('admin/managefarmhouses') ?>" class="text-sky hover:text-sky-d font-bold text-xs flex items-center" title="Manage Properties">
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Metric 2: Pending Bookings -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-amber-400/40 transition-all group relative overflow-hidden flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">event_available</span>
                </div>
                <?php if ($pendingBookings > 0): ?>
                <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse">
                    Action Needed
                </span>
                <?php else: ?>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    All Caught Up
                </span>
                <?php endif; ?>
            </div>

            <div>
                <div class="text-3xl lg:text-4xl font-extrabold font-['Open_Sans',sans-serif] text-ink tracking-tight">
                    <?= sprintf('%02d', $pendingBookings) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Pending Bookings
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-medium text-muted">
                <span>Active stays: <strong class="text-ink font-bold"><?= $activeBookings ?></strong></span>
                <a href="<?= url('admin/booking-requests') ?>" class="text-amber-600 hover:text-amber-700 font-bold flex items-center gap-0.5">
                    Review <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Metric 3: Inquiries & Leads -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-emerald-400/40 transition-all group relative overflow-hidden flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">mark_chat_unread</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Customer Leads
                </span>
            </div>

            <div>
                <div class="text-3xl lg:text-4xl font-extrabold font-['Open_Sans',sans-serif] text-ink tracking-tight">
                    <?= sprintf('%02d', count($inquiries ?? [])) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Recent Contact Leads
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-medium text-muted">
                <span class="text-emerald-700 font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">bolt</span> Instant Actions
                </span>
                <a href="<?= url('admin/contact-inquiries') ?>" class="text-emerald-600 hover:text-emerald-700 font-bold flex items-center gap-0.5">
                    View Leads <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Metric 4: Estate Owners -->
        <div class="bg-white rounded-2xl border border-border p-6 shadow-sm hover:shadow-md hover:border-indigo-400/40 transition-all group relative overflow-hidden flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">groups</span>
                </div>
                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-md uppercase tracking-wider">
                    Partner Network
                </span>
            </div>

            <div>
                <div class="text-3xl lg:text-4xl font-extrabold font-['Open_Sans',sans-serif] text-ink tracking-tight">
                    <?= sprintf('%02d', $totalOwners) ?>
                </div>
                <div class="text-xs font-semibold text-muted uppercase tracking-wider mt-1">
                    Registered Owners
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-medium text-muted">
                <span class="text-muted">Verified Partners</span>
                <a href="<?= url('admin/owners') ?>" class="text-indigo-600 hover:text-indigo-700 font-bold flex items-center gap-0.5">
                    Directory <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

    </div>

    <!-- ── 3. MAIN DASHBOARD CONTENT GRID (LEFT 8 / RIGHT 4) ────────────────── -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ── LEFT COLUMN (8 COLS): BOOKINGS + PROPERTIES SHOWCASE ─────────── -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- SECTION 1: RECENT BOOKING REQUESTS -->
            <div class="bg-white rounded-3xl border border-border p-6 md:p-8 shadow-sm">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-sky-l">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sky text-2xl">receipt_long</span>
                            <h3 class="text-xl md:text-2xl font-bold font-['Open_Sans',sans-serif] text-ink">
                                Recent Booking Requests
                            </h3>
                        </div>
                        <p class="text-xs md:text-sm text-muted mt-1">Review, confirm, or coordinate upcoming guest reservations.</p>
                    </div>

                    <a href="<?= url('admin/booking-requests') ?>" 
                       class="inline-flex items-center gap-1.5 text-xs md:text-sm font-bold text-sky hover:text-sky-d transition-colors w-fit">
                        View All Requests
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>

                <?php if (!empty($recentBookings)): ?>
                <div class="divide-y divide-border/60">
                    <?php foreach ($recentBookings as $booking): 
                        $status = strtolower($booking['status'] ?? 'pending');
                        
                        $statusBadge = match($status) {
                            'approved', 'active', 'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected', 'cancelled'           => 'bg-rose-50 text-rose-700 border-rose-200',
                            default                           => 'bg-amber-50 text-amber-700 border-amber-200',
                        };

                        $checkIn  = !empty($booking['check_in']) ? strtotime($booking['check_in']) : (!empty($booking['start_date']) ? strtotime($booking['start_date']) : null);
                        $checkOut = !empty($booking['check_out']) ? strtotime($booking['check_out']) : (!empty($booking['end_date']) ? strtotime($booking['end_date']) : null);
                        
                        $nightCount = ($checkIn && $checkOut) ? max(1, round(($checkOut - $checkIn) / 86400)) : 1;
                        $cleanPhone = preg_replace('/[^0-9]/', '', $booking['guest_phone'] ?? '');
                    ?>
                    <div class="py-4.5 first:pt-0 last:pb-0 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-surface/50 rounded-2xl p-3 transition-colors">
                        
                        <!-- Guest & Property Details -->
                        <div class="flex items-start gap-3.5 min-w-0 flex-1">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-sky-l to-white border border-sky/20 flex items-center justify-center text-sky font-bold shrink-0 shadow-sm text-sm">
                                <?= strtoupper(substr($booking['guest_name'] ?? 'G', 0, 1)) ?>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-ink text-sm md:text-[15px] truncate">
                                        <?= htmlspecialchars($booking['guest_name'] ?? 'Guest Request') ?>
                                    </h4>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border <?= $statusBadge ?>">
                                        <?= htmlspecialchars(ucfirst($status)) ?>
                                    </span>
                                </div>

                                <p class="text-xs text-muted font-medium mt-0.5 truncate flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-sky">holiday_village</span>
                                    <span class="text-ink font-semibold"><?= htmlspecialchars($booking['farmhouse_title'] ?? 'Farmhouse Stay') ?></span>
                                    <?php if (!empty($booking['farmhouse_location'])): ?>
                                        <span class="text-muted/60">•</span>
                                        <span><?= htmlspecialchars($booking['farmhouse_location']) ?></span>
                                    <?php endif; ?>
                                </p>

                                <!-- Date & Stay tags -->
                                <div class="mt-2 flex items-center gap-2 flex-wrap text-[11px] font-semibold text-muted">
                                    <span class="bg-surface border border-border px-2 py-0.5 rounded-md flex items-center gap-1 text-ink">
                                        <span class="material-symbols-outlined text-[13px] text-sky">login</span>
                                        <?= $checkIn ? date('d M Y', $checkIn) : 'Date TBD' ?>
                                    </span>
                                    <span class="text-muted/60">➔</span>
                                    <span class="bg-surface border border-border px-2 py-0.5 rounded-md flex items-center gap-1 text-ink">
                                        <span class="material-symbols-outlined text-[13px] text-sky">logout</span>
                                        <?= $checkOut ? date('d M Y', $checkOut) : 'Date TBD' ?>
                                    </span>
                                    <span class="bg-stone-100 text-stone-700 px-2 py-0.5 rounded-md font-bold text-[10px] uppercase tracking-wider">
                                        <?= $nightCount ?> Night<?= $nightCount === 1 ? '' : 's' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                            <?php if (!empty($cleanPhone)): ?>
                            <a href="https://wa.me/91<?= htmlspecialchars($cleanPhone) ?>?text=Hello%20<?= urlencode($booking['guest_name'] ?? '') ?>,%20regarding%20your%20booking%20inquiry%20for%20<?= urlencode($booking['farmhouse_title'] ?? 'Farmhouse') ?>"
                               target="_blank"
                               class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-200 flex items-center justify-center transition-all shadow-sm"
                               title="Message Guest on WhatsApp">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                            </a>
                            <?php endif; ?>

                            <a href="<?= url('admin/booking-requests') ?>" 
                               class="px-3.5 py-2 rounded-xl bg-surface hover:bg-sky-xl text-ink hover:text-sky font-bold text-xs border border-border transition-all flex items-center gap-1 shadow-sm">
                                <span>Manage</span>
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </a>
                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="py-12 text-center rounded-2xl border-2 border-dashed border-border/80 bg-surface/30">
                    <span class="material-symbols-outlined text-4xl text-muted/40 mb-2 block">event_busy</span>
                    <h4 class="font-bold text-ink text-sm">No Pending Booking Requests</h4>
                    <p class="text-xs text-muted mt-1 max-w-sm mx-auto">When customers submit reservation requests from property pages, they will appear here in real-time.</p>
                </div>
                <?php endif; ?>

            </div>

            <!-- SECTION 2: RECENT PROPERTIES SHOWCASE -->
            <div class="bg-white rounded-3xl border border-border p-6 md:p-8 shadow-sm">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-sky-l">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sky text-2xl">villa</span>
                            <h3 class="text-xl md:text-2xl font-bold font-['Open_Sans',sans-serif] text-ink">
                                Recently Listed Properties
                            </h3>
                        </div>
                        <p class="text-xs md:text-sm text-muted mt-1">Live overview of estates, inventory pricing, and host assignments.</p>
                    </div>

                    <a href="<?= url('admin/managefarmhouses') ?>" 
                       class="inline-flex items-center gap-1.5 text-xs md:text-sm font-bold text-sky hover:text-sky-d transition-colors w-fit">
                        Browse All Properties
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>

                <?php if (!empty($recentProperties)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <?php foreach ($recentProperties as $farm): 
                        $thumbImg = farmhouse_img_url($farm['thumb_url'] ?? null);
                        $isLive = strtolower($farm['status'] ?? '') === 'active';
                    ?>
                    <div class="rounded-2xl border border-border bg-surface/30 p-4 hover:bg-white hover:border-sky/40 hover:shadow-md transition-all flex flex-col justify-between group">
                        
                        <div>
                            <!-- Thumbnail Banner -->
                            <div class="relative h-40 w-full rounded-xl overflow-hidden bg-stone-100 mb-3.5">
                                <img src="<?= htmlspecialchars($thumbImg) ?>" 
                                     alt="<?= htmlspecialchars($farm['title'] ?? 'Estate') ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     onerror="this.src='https://placehold.co/600x400/E6F6FD/16A5DE?text=Estate+Preview'">
                                
                                <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider backdrop-blur-md shadow-sm bg-sky-500/90 text-white border border-sky-400/50">
                                        <?= htmlspecialchars($farm['category'] ?? 'Farmhouse') ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider backdrop-blur-md shadow-sm border <?= $isLive ? 'bg-emerald-500/90 text-white border-emerald-400/50' : 'bg-stone-800/80 text-stone-200 border-stone-600/50' ?>">
                                        <?= $isLive ? 'Active Live' : 'Off-Market' ?>
                                    </span>
                                </div>

                                <?php if (!empty($farm['is_negotiable'])): ?>
                                <div class="absolute bottom-2.5 right-2.5">
                                    <span class="px-2 py-0.5 rounded-md bg-amber-500/90 text-white text-[9px] font-extrabold uppercase tracking-wider backdrop-blur-md">
                                        Negotiable
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Title & Location -->
                            <h4 class="font-bold text-ink text-base line-clamp-1 group-hover:text-sky transition-colors">
                                <?= htmlspecialchars($farm['title'] ?? 'Untitled Estate') ?>
                            </h4>

                            <p class="text-xs text-muted font-medium mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px] text-sky shrink-0">location_on</span>
                                <span class="truncate"><?= htmlspecialchars($farm['location'] ?? 'Location not specified') ?></span>
                            </p>
                        </div>

                        <!-- Pricing & Action Bar -->
                        <div class="mt-4 pt-3.5 border-t border-border flex items-center justify-between">
                            <div>
                                <span class="text-base font-extrabold font-['Open_Sans',sans-serif] text-ink">
                                    ₹<?= number_format((float)($farm['price'] ?? 0)) ?>
                                </span>
                                <span class="text-[11px] text-muted font-medium">/ night</span>
                            </div>

                            <a href="<?= url('admin/editfarm?id=' . ($farm['id'] ?? 0)) ?>" 
                               class="px-3 py-1.5 rounded-lg bg-white hover:bg-sky text-ink hover:text-white border border-border hover:border-sky text-xs font-bold transition-all flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-sm">edit</span>
                                Edit
                            </a>
                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="py-12 text-center rounded-2xl border-2 border-dashed border-border/80 bg-surface/30">
                    <span class="material-symbols-outlined text-4xl text-muted/40 mb-2 block">cottage</span>
                    <h4 class="font-bold text-ink text-sm">No Properties Listed Yet</h4>
                    <p class="text-xs text-muted mt-1 mb-4">Start by adding your first luxury agrarian estate to the system.</p>
                    <a href="<?= url('admin/addfarm') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky text-white text-xs font-bold shadow hover:bg-sky-d transition-all">
                        <span class="material-symbols-outlined text-sm">add</span> Add Farmhouse
                    </a>
                </div>
                <?php endif; ?>

            </div>

        </div>

        <!-- ── RIGHT COLUMN (4 COLS): LIVE INQUIRIES + QUICK SHORTCUTS ────── -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- SECTION 1: LIVE INQUIRIES & LEADS FEED -->
            <div class="bg-white rounded-3xl border border-border p-6 shadow-sm">
                
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-sky-l">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-xl">forum</span>
                            <h3 class="text-lg font-bold font-['Open_Sans',sans-serif] text-ink">
                                Live Inquiries
                            </h3>
                        </div>
                        <p class="text-[11px] text-muted mt-0.5">Recent direct inquiries from website</p>
                    </div>

                    <a href="<?= url('admin/contact-inquiries') ?>" 
                       class="text-xs font-bold text-sky hover:text-sky-d flex items-center gap-0.5">
                        All <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                </div>

                <?php if (!empty($inquiries)): ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($inquiries, 0, 5) as $idx => $lead): 
                        $leadName  = !empty($lead['full_name']) ? $lead['full_name'] : 'Guest Visitor';
                        $leadPhone = !empty($lead['phone']) ? $lead['phone'] : '';
                        $leadMsg   = !empty($lead['Message']) ? $lead['Message'] : 'Customer inquired about farmhouse booking & availability.';
                        $cleanDial = preg_replace('/[^0-9]/', '', $leadPhone);
                        $gradient  = $avatarGradients[$idx % count($avatarGradients)];
                        $timeStr   = !empty($lead['created_at']) ? date('d M, h:i A', strtotime($lead['created_at'])) : 'Recent';
                    ?>
                    <div class="p-4 rounded-2xl bg-surface/60 border border-border hover:border-emerald-300 hover:bg-white transition-all space-y-3">
                        
                        <!-- Top Info -->
                        <div class="flex items-start gap-3 justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr <?= $gradient ?> flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                    <?= strtoupper(substr($leadName, 0, 1)) ?>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="font-bold text-ink text-xs md:text-sm truncate">
                                        <?= htmlspecialchars($leadName) ?>
                                    </h5>
                                    <p class="text-[11px] text-muted font-medium truncate">
                                        <?= htmlspecialchars($leadPhone ?: 'No Phone Number') ?>
                                    </p>
                                </div>
                            </div>
                            
                            <span class="text-[10px] text-muted font-semibold bg-white border border-border px-2 py-0.5 rounded-full shrink-0">
                                <?= $timeStr ?>
                            </span>
                        </div>

                        <!-- Message snippet -->
                        <p class="text-xs text-muted/90 bg-white p-2.5 rounded-xl border border-stone-100 italic leading-relaxed line-clamp-2">
                            "<?= htmlspecialchars($leadMsg) ?>"
                        </p>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <?php if (!empty($cleanDial)): ?>
                            <a href="https://wa.me/91<?= htmlspecialchars($cleanDial) ?>" 
                               target="_blank"
                               class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                                <span>WhatsApp</span>
                            </a>
                            <a href="tel:<?= htmlspecialchars($cleanDial) ?>" 
                               class="w-full py-2 px-3 rounded-xl bg-sky-l hover:bg-sky text-sky-d hover:text-white border border-sky/20 font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-sm">call</span>
                                <span>Call</span>
                            </a>
                            <?php else: ?>
                            <a href="<?= url('admin/contact-inquiries') ?>" 
                               class="col-span-2 py-2 px-3 rounded-xl bg-surface hover:bg-sky-xl text-ink font-semibold text-xs border border-border text-center">
                                View Details
                            </a>
                            <?php endif; ?>
                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="py-8 text-center rounded-2xl border-2 border-dashed border-border/80 bg-surface/30">
                    <span class="material-symbols-outlined text-3xl text-muted/40 mb-1 block">inbox</span>
                    <p class="text-xs text-muted font-medium">No recent inquiries received.</p>
                </div>
                <?php endif; ?>

            </div>

            <!-- SECTION 2: OPERATIONS CENTER SHORTCUTS -->
            <div class="bg-white rounded-3xl border border-border p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-sky-l">
                    <span class="material-symbols-outlined text-sky text-xl">tune</span>
                    <h4 class="text-base font-bold font-['Open_Sans',sans-serif] text-ink">
                        Operations & Management
                    </h4>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="<?= url('admin/manage-amenities') ?>" 
                       class="p-3.5 rounded-2xl bg-surface/60 hover:bg-sky-xl border border-border hover:border-sky/40 transition-all flex flex-col items-start gap-1.5 group">
                        <span class="material-symbols-outlined text-sky text-2xl group-hover:scale-110 transition-transform">pool</span>
                        <span class="text-xs font-bold text-ink">Amenities</span>
                        <span class="text-[10px] text-muted font-medium">Facility library</span>
                    </a>

                    <a href="<?= url('admin/manage-rules') ?>" 
                       class="p-3.5 rounded-2xl bg-surface/60 hover:bg-sky-xl border border-border hover:border-sky/40 transition-all flex flex-col items-start gap-1.5 group">
                        <span class="material-symbols-outlined text-amber-600 text-2xl group-hover:scale-110 transition-transform">policy</span>
                        <span class="text-xs font-bold text-ink">Estate Rules</span>
                        <span class="text-[10px] text-muted font-medium">House policies</span>
                    </a>

                    <a href="<?= url('admin/availability') ?>" 
                       class="p-3.5 rounded-2xl bg-surface/60 hover:bg-sky-xl border border-border hover:border-sky/40 transition-all flex flex-col items-start gap-1.5 group">
                        <span class="material-symbols-outlined text-emerald-600 text-2xl group-hover:scale-110 transition-transform">date_range</span>
                        <span class="text-xs font-bold text-ink">Availability</span>
                        <span class="text-[10px] text-muted font-medium">Block & book dates</span>
                    </a>

                    <a href="<?= url('admin/users') ?>" 
                       class="p-3.5 rounded-2xl bg-surface/60 hover:bg-sky-xl border border-border hover:border-sky/40 transition-all flex flex-col items-start gap-1.5 group">
                        <span class="material-symbols-outlined text-indigo-600 text-2xl group-hover:scale-110 transition-transform">person</span>
                        <span class="text-xs font-bold text-ink">Customers</span>
                        <span class="text-[10px] text-muted font-medium">User accounts</span>
                    </a>
                </div>
            </div>

            <!-- SECTION 3: SYSTEM HEALTH STATUS CARD -->
            <div class="rounded-3xl bg-gradient-to-br from-ink to-[#071520] text-white p-6 shadow-lg border border-sky/20 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-sky-l">System Health</span>
                    </div>
                    <span class="text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded text-white/80">
                        v2.4.0
                    </span>
                </div>

                <div class="space-y-2.5 text-xs text-stone-300">
                    <div class="flex items-center justify-between pb-2 border-b border-white/10">
                        <span class="opacity-80">Database Connection</span>
                        <span class="text-emerald-400 font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span> Operational
                        </span>
                    </div>
                    <div class="flex items-center justify-between pb-2 border-b border-white/10">
                        <span class="opacity-80">Active Admin Session</span>
                        <span class="text-white font-bold truncate max-w-[120px]">
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="opacity-80">System Timezone</span>
                        <span class="text-sky-l font-semibold"><?= date_default_timezone_get() ?></span>
                    </div>
                </div>

                <a href="<?= url('admin/profile') ?>" 
                   class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold flex items-center justify-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-sm">settings</span>
                    Admin Account Settings
                </a>
            </div>

        </div>

    </div>

</div>

<?php
include __DIR__ . "/../Includes/admin_footer.php";
?>