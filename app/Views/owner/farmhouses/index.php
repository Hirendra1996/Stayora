<?php
include __DIR__ . '/../../Includes/owner_header.php';

$farmhouses          = $farmhouses          ?? [];
$stats               = $stats               ?? ['total' => 0, 'active' => 0, 'pending' => 0, 'rejected' => 0];
$currentStatusFilter = $currentStatusFilter ?? null;
$success_message     = $success_message     ?? null;
$error_message       = $error_message       ?? null;
?>

<div class="p-4 md:p-8 max-w-[1400px] mx-auto space-y-6">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 1. PAGE HEADER & ACTIONS                                    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl md:text-3xl text-slate-900 leading-tight">My Farmhouses</h1>
            <p class="text-xs md:text-sm text-slate-500 font-semibold mt-1">Manage your active listings, edit details, and track performance.</p>
        </div>

        <a href="<?= url('owner/farmhouses/add') ?>"
           class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-3 rounded-2xl text-xs font-black shadow-md shadow-primary/25 hover:shadow-lg transition-all active:scale-95 text-decoration-none">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>List New Farmhouse</span>
        </a>
    </div>

    <!-- ── Flash Messages ── -->
    <?php if ($success_message): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-2xl text-xs font-bold shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-500">check_circle</span>
            <span><?= htmlspecialchars($success_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-xs font-bold shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-red-500">error</span>
            <span><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 2. STATUS FILTER TABS                                       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <a href="<?= url('owner/farmhouses') ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all text-decoration-none <?= empty($currentStatusFilter) ? 'bg-primary text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            <span>All Properties</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] <?= empty($currentStatusFilter) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' ?>">
                <?= (int)($stats['total'] ?? 0) ?>
            </span>
        </a>

        <a href="<?= url('owner/farmhouses?status=active') ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all text-decoration-none <?= ($currentStatusFilter === 'active') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            <span class="w-2 h-2 rounded-full <?= ($currentStatusFilter === 'active') ? 'bg-white' : 'bg-emerald-500' ?>"></span>
            <span>Active Live</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($currentStatusFilter === 'active') ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700' ?>">
                <?= (int)($stats['active'] ?? 0) ?>
            </span>
        </a>

        <a href="<?= url('owner/farmhouses?status=pending') ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all text-decoration-none <?= ($currentStatusFilter === 'pending') ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            <span class="w-2 h-2 rounded-full <?= ($currentStatusFilter === 'pending') ? 'bg-white' : 'bg-amber-500' ?>"></span>
            <span>Pending Review</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($currentStatusFilter === 'pending') ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-700' ?>">
                <?= (int)($stats['pending'] ?? 0) ?>
            </span>
        </a>

        <a href="<?= url('owner/farmhouses?status=rejected') ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all text-decoration-none <?= ($currentStatusFilter === 'rejected') ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
            <span>Rejected</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($currentStatusFilter === 'rejected') ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-700' ?>">
                <?= (int)($stats['rejected'] ?? 0) ?>
            </span>
        </a>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 3. FARMHOUSES GRID / CARDS                                  -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <?php if (!empty($farmhouses)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($farmhouses as $fh): 
                $id = (int)$fh['id'];
                $encId = \App\Helpers\CryptoHelper::encrypt($id);
                $title = htmlspecialchars($fh['title'] ?? 'Farmhouse');
                $location = htmlspecialchars($fh['location'] ?? 'India');
                $price = (float)($fh['price'] ?? 0);
                $status = strtolower($fh['status'] ?? 'pending');
                $thumb = farmhouse_img_url($fh['thumb_url'] ?? null);
                $bookingCount = (int)($fh['booking_request_count'] ?? 0);
                $bedrooms = isset($fh['bedrooms']) ? (int)$fh['bedrooms'] : null;
            ?>
                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Cover Image & Badges -->
                        <div class="relative h-52 bg-slate-100 overflow-hidden">
                            <img src="<?= $thumb ?>" alt="<?= $title ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            
                            <!-- Status & Category Badges -->
                            <div class="absolute top-3.5 left-3.5 flex items-center flex-wrap gap-1.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-900/80 backdrop-blur-md text-sky-200 border border-sky-500/30 text-[11px] font-extrabold uppercase tracking-wide">
                                    <?= htmlspecialchars($fh['category'] ?? 'Farmhouse') ?>
                                </span>
                                <?php if ($status === 'active'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-900/80 backdrop-blur-md text-emerald-300 border border-emerald-500/30 text-[11px] font-extrabold uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Active Live
                                    </span>
                                <?php elseif ($status === 'pending'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-900/80 backdrop-blur-md text-amber-300 border border-amber-500/30 text-[11px] font-extrabold uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-amber-400"></span> In Review
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-900/80 backdrop-blur-md text-rose-300 border border-rose-500/30 text-[11px] font-extrabold uppercase tracking-wide">
                                        Rejected
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Inquiries Chip -->
                            <div class="absolute bottom-3.5 right-3.5 bg-slate-900/80 backdrop-blur-md text-white px-2.5 py-1 rounded-xl text-xs font-extrabold flex items-center gap-1 border border-white/10">
                                <span class="material-symbols-outlined text-xs text-primary">calendar_month</span>
                                <span><?= $bookingCount ?> Inquiries</span>
                            </div>
                        </div>

                        <!-- Content Body -->
                        <div class="p-5">
                            <h2 class="font-headline font-black text-base text-slate-900 leading-snug line-clamp-1 mb-1.5" title="<?= $title ?>">
                                <?= $title ?>
                            </h2>

                            <div class="flex items-center gap-3 text-xs text-slate-500 font-semibold mb-4">
                                <span class="flex items-center gap-1 text-slate-600 truncate">
                                    <span class="material-symbols-outlined text-[15px] text-primary">location_on</span>
                                    <span><?= $location ?></span>
                                </span>
                                <?php if ($bedrooms !== null): ?>
                                    <span>•</span>
                                    <span><?= $bedrooms ?> BHK</span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-baseline justify-between pt-3 border-t border-slate-100">
                                <div>
                                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Price / Day</span>
                                    <span class="text-lg font-black text-slate-900 font-headline">₹<?= number_format($price) ?></span>
                                </div>

                                <span class="text-xs text-slate-400 font-bold">#FL-<?= $id ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Buttons Footer -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="<?= url('owner/farmhouses/show?id=' . $encId) ?>"
                           class="flex-1 py-2.5 px-3 rounded-xl bg-white border border-slate-200 hover:border-primary text-slate-700 hover:text-primary text-xs font-black text-center transition-all text-decoration-none">
                            View Showcase
                        </a>

                        <a href="<?= url('owner/farmhouses/edit?id=' . $encId) ?>"
                           class="flex-1 py-2.5 px-3 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-black text-center shadow-xs transition-all text-decoration-none">
                            Edit Listing
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-lg mx-auto shadow-2xs">
            <div class="w-16 h-16 bg-sky-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">villa</span>
            </div>
            <h2 class="font-headline font-black text-lg text-slate-900 mb-1">No Farmhouses Found</h2>
            <p class="text-xs text-slate-500 font-semibold mb-6">
                <?= $currentStatusFilter ? 'There are no listings matching the "' . ucfirst($currentStatusFilter) . '" filter.' : 'You have not submitted any farmhouse listings yet.' ?>
            </p>
            <a href="<?= url('owner/farmhouses/add') ?>"
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-5 py-3 rounded-2xl text-xs font-black shadow-sm text-decoration-none">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>List a New Farmhouse</span>
            </a>
        </div>
    <?php endif; ?>

</div>

<?php 
include __DIR__ . '/../../Includes/owner_footer.php'; 
?>
