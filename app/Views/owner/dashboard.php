<?php 
include __DIR__ . "/../Includes/owner_header.php"; 

$owner    = $owner    ?? [];
$stats    = $stats    ?? [];
$recent   = $recent   ?? [];
$bookings = $bookings ?? [];

$firstName = htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'Host Partner')[0]);
$totalFarms = (int)($stats['total_farmhouses'] ?? ($stats['total'] ?? 0));
$activeFarms = (int)($stats['active_farmhouses'] ?? ($stats['active'] ?? ($stats['approved'] ?? 0)));
$pendingFarms = (int)($stats['pending_farmhouses'] ?? ($stats['pending'] ?? 0));
$totalBookings = (int)($stats['total_bookings'] ?? 0);
?>

<div class="p-4 md:p-8 max-w-[1400px] mx-auto space-y-7">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 1. LUXURY HERO BANNER                                       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="relative rounded-3xl p-7 md:p-9 overflow-hidden shadow-sm border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white">
        <!-- Ambient mesh glow -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary/20 rounded-full blur-[70px] pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-60 h-60 bg-sky-500/15 rounded-full blur-[60px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/15 border border-primary/30 text-sky-400 text-xs font-extrabold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-sm">verified</span>
                    <span>Host Partner Dashboard</span>
                </div>
                <h1 class="font-headline font-black text-2xl md:text-3xl text-white tracking-tight leading-tight">
                    Welcome back, <span class="text-primary"><?= $firstName ?>!</span>
                </h1>
                <p class="text-slate-300 text-sm max-w-xl leading-relaxed">
                    Here is what is happening across your farmhouse portfolio today. Track live bookings, manage weekend rates, and review pending property inquiries.
                </p>
            </div>

            <!-- Hero Action Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                <a href="<?= url('owner/farmhouses/add') ?>"
                   class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-3 rounded-2xl text-xs font-black shadow-md shadow-primary/25 hover:shadow-lg transition-all active:scale-95 text-decoration-none">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>List New Farmhouse</span>
                </a>

                <a href="https://wa.me/919876543210?text=Hello%20Farmlelo,%20I%20am%20a%20host%20and%20need%20assistance" target="_blank"
                   class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-2xl text-xs font-black shadow-sm transition-all text-decoration-none">
                    <svg style="width:16px;height:16px;fill:currentColor;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                    <span>Host Concierge</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 2. KPI METRIC CARDS                                         -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Total Farmhouses -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Portfolio</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-50 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">villa</span>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 font-headline leading-tight"><?= $totalFarms ?></div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-semibold text-slate-500">
                <span class="text-primary font-bold"><?= $activeFarms ?> Live</span>
                <span>•</span>
                <span><?= $pendingFarms ?> In Review</span>
            </div>
        </div>

        <!-- Active Live Listings -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Active Listings</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 font-headline leading-tight"><?= $activeFarms ?></div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-semibold text-emerald-600">
                <span class="material-symbols-outlined text-[14px]">trending_up</span>
                <span>Receiving Guest Inquiries</span>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Pending Review</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 font-headline leading-tight"><?= $pendingFarms ?></div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-semibold text-amber-600">
                <span>Under verification audit</span>
            </div>
        </div>

        <!-- Total Inquiries & Bookings -->
        <a href="<?= url('owner/bookings') ?>" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs hover:shadow-md hover:border-primary transition-all text-decoration-none block group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Bookings</span>
                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-all">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 font-headline leading-tight"><?= $totalBookings ?></div>
            <div class="flex items-center gap-1.5 mt-2 text-xs font-semibold text-purple-600">
                <span class="material-symbols-outlined text-[14px]">group</span>
                <span>Guest reservations &amp; stays</span>
            </div>
        </a>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 3. RECENT FARMHOUSES & BOOKINGS GRID                        -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">

        <!-- ── Left 2 Cols: My Farmhouses Table ── -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 md:p-7 shadow-2xs">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="font-headline font-black text-lg text-slate-900 leading-tight">My Farmhouses</h2>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Manage status, photos &amp; pricing</p>
                </div>

                <a href="<?= url('owner/farmhouses') ?>" class="text-xs font-extrabold text-primary hover:text-primary-hover flex items-center gap-1 text-decoration-none">
                    <span>View All</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <?php if (!empty($recent)): ?>
                <div class="space-y-3.5">
                    <?php foreach ($recent as $fh): 
                        $fhId = (int)$fh['id'];
                        $encId = \App\Helpers\CryptoHelper::encrypt($fhId);
                        $thumb = farmhouse_img_url($fh['thumb_url'] ?? null);
                        $status = strtolower($fh['status'] ?? 'pending');
                    ?>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-300 transition-all">
                            <!-- Thumbnail & Info -->
                            <div class="flex items-center gap-3.5 min-w-0">
                                <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($fh['title'] ?? '') ?>" class="w-16 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-black text-slate-900 truncate leading-tight"><?= htmlspecialchars($fh['title'] ?? 'Farmhouse') ?></h3>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 font-semibold">
                                        <span class="flex items-center gap-0.5"><span class="material-symbols-outlined text-xs text-slate-400">location_on</span><?= htmlspecialchars($fh['location'] ?? 'India') ?></span>
                                        <span>•</span>
                                        <span class="text-primary font-bold">₹<?= number_format((float)($fh['price'] ?? 0)) ?>/night</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Badges & Actions -->
                            <div class="flex items-center gap-3 self-end sm:self-center shrink-0">
                                <?php if ($status === 'active'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-extrabold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                <?php elseif ($status === 'pending'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-extrabold uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> In Review
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-extrabold uppercase">
                                        Rejected
                                    </span>
                                <?php endif; ?>

                                <!-- Quick Links -->
                                <div class="flex items-center gap-1.5">
                                    <a href="<?= url('owner/farmhouses/show?id=' . $encId) ?>" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-primary text-slate-600 hover:text-primary transition-all text-decoration-none" title="View Detail">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </a>
                                    <a href="<?= url('owner/farmhouses/edit?id=' . $encId) ?>" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-primary text-slate-600 hover:text-primary transition-all text-decoration-none" title="Edit Listing">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 px-4 rounded-2xl border-2 border-dashed border-slate-200">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">villa</span>
                    <h3 class="text-sm font-extrabold text-slate-700">No Farmhouses Listed Yet</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 mb-4">Start earning by listing your private pool villa or estate retreat.</p>
                    <a href="<?= url('owner/farmhouses/add') ?>" class="inline-flex items-center gap-1.5 bg-primary text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm text-decoration-none">
                        <span class="material-symbols-outlined text-sm">add</span>
                        <span>List Your First Farm</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── Right 1 Col: Recent Bookings Feed ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-7 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="font-headline font-black text-lg text-slate-900 leading-tight">Recent Bookings</h2>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Live guest reservation activity</p>
                    </div>

                    <a href="<?= url('owner/bookings') ?>" class="text-xs font-extrabold text-primary hover:text-primary-hover flex items-center gap-1 text-decoration-none">
                        <span>View All</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <?php if (!empty($bookings)): ?>
                    <div class="space-y-3">
                        <?php foreach ($bookings as $b): 
                            $bStatus = strtolower($b['status'] ?? 'pending');
                            $bTitle  = $b['farmhouse_title'] ?? $b['title'] ?? 'Farmhouse Stay';
                            $startD  = !empty($b['check_in']) ? $b['check_in'] : ($b['start_date'] ?? 'now');
                            $endD    = !empty($b['check_out']) ? $b['check_out'] : ($b['end_date'] ?? 'now');
                        ?>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-300 transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-black text-slate-900 truncate leading-tight"><?= htmlspecialchars($bTitle) ?></h4>
                                        <div class="text-[11px] text-slate-500 font-semibold mt-1">
                                            <span><?= date('d M', strtotime($startD)) ?> - <?= date('d M Y', strtotime($endD)) ?></span>
                                            <span>•</span>
                                            <span><?= (int)($b['guests'] ?? 2) ?> Guests</span>
                                        </div>
                                    </div>

                                    <?php if ($bStatus === 'approved'): ?>
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-extrabold uppercase">Confirmed</span>
                                    <?php elseif ($bStatus === 'completed'): ?>
                                        <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-700 text-[10px] font-extrabold uppercase">Completed</span>
                                    <?php elseif ($bStatus === 'pending'): ?>
                                        <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 text-[10px] font-extrabold uppercase">Pending</span>
                                    <?php elseif ($bStatus === 'cancelled'): ?>
                                        <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 text-[10px] font-extrabold uppercase">Cancelled</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-200 text-slate-600 text-[10px] font-extrabold uppercase"><?= $bStatus ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-10 px-4 rounded-2xl border-2 border-dashed border-slate-200">
                        <span class="material-symbols-outlined text-3xl text-slate-300 mb-1">calendar_today</span>
                        <p class="text-xs text-slate-500 font-bold">No Bookings Yet</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">As soon as reservations are approved, confirmed stays will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Host Perks Widget -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <div class="p-4 rounded-2xl bg-gradient-to-br from-sky-50 to-indigo-50 border border-sky-100">
                    <div class="flex items-center gap-2 text-primary font-black text-xs uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-sm">photo_camera</span>
                        <span>Free HD Photoshoot</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Need updated drone footage or HD villa photos? Contact your concierge manager to schedule a shoot.
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>

<?php 
include __DIR__ . "/../Includes/owner_footer.php"; 
?>