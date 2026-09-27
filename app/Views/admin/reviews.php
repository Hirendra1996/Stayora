<?php
$pageTitle = 'Guest Reviews Moderation Desk';
$activePage = 'reviews';
require_once __DIR__ . '/../Includes/admin_header.php';
?>

<main class="min-h-screen bg-[#F7F3EA] pb-16">
    <div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="material-symbols-outlined text-sm">stars</span>
                        Social Proof &amp; Moderation
                    </span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-slate-900 mt-2 tracking-tight">Guest Reviews Moderation Desk</h1>
                <p class="text-sm text-slate-500 mt-1">Audit customer ratings, multi-criteria scores (Cleanliness, Location, Value, Hospitality), and publish verified reviews.</p>
            </div>
        </div>

        <!-- Success Toast -->
        <?php if (!empty($_SESSION['success_msg'])): ?>
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['success_msg']) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <!-- KPI Metric Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-[#E2DBD0] shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">rate_review</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Submitted</p>
                    <p class="text-2xl font-black text-slate-900 mt-0.5"><?= $stats['all'] ?></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">hourglass_empty</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Pending Audit</p>
                    <p class="text-2xl font-black text-amber-800 mt-0.5"><?= $stats['pending'] ?></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Approved Live</p>
                    <p class="text-2xl font-black text-emerald-800 mt-0.5"><?= $stats['approved'] ?></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">thumb_down</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Rejected</p>
                    <p class="text-2xl font-black text-rose-800 mt-0.5"><?= $stats['rejected'] ?></p>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl p-4 border border-[#E2DBD0] shadow-xs flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="<?= url('admin/reviews') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= empty($validFilter) ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    All Reviews (<?= $stats['all'] ?>)
                </a>
                <a href="<?= url('admin/reviews?status=pending') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $validFilter === 'pending' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    Pending Audit (<?= $stats['pending'] ?>)
                </a>
                <a href="<?= url('admin/reviews?status=approved') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $validFilter === 'approved' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    Approved Live (<?= $stats['approved'] ?>)
                </a>
                <a href="<?= url('admin/reviews?status=rejected') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $validFilter === 'rejected' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    Rejected (<?= $stats['rejected'] ?>)
                </a>
            </div>
        </div>

        <!-- Reviews Roster -->
        <div class="bg-white rounded-2xl border border-[#E2DBD0] overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900">Submitted Reviews</h2>
                <span class="text-xs text-slate-500 font-semibold"><?= count($reviews) ?> Records</span>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="text-center py-16 px-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                        <span class="material-symbols-outlined text-3xl">rate_review</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">No reviews found</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">There are no customer reviews matching the current status filter.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($reviews as $rev): ?>
                        <div class="p-6 hover:bg-slate-50/60 transition flex flex-col md:flex-row md:items-start justify-between gap-6">
                            <div class="space-y-3 flex-1">
                                <!-- Top row: Guest & Property -->
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 font-black flex items-center justify-center text-sm flex-shrink-0">
                                        <?= strtoupper(substr($rev['guest_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($rev['guest_name']) ?></h3>
                                            <?php if (!empty($rev['is_verified_stay'])): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="material-symbols-outlined text-xs">verified</span> Verified Stay
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-xs text-sky-500">villa</span>
                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($rev['farmhouse_title']) ?></span>
                                            • <?= date('d M Y, h:i A', strtotime($rev['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Star Rating & Multi-Criteria -->
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex items-center gap-1 px-3 py-1 rounded-xl bg-amber-50 text-amber-800 font-black text-sm border border-amber-200">
                                        <span class="material-symbols-outlined text-amber-500 text-base" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span><?= number_format($rev['overall_rating'], 1) ?></span>
                                    </div>

                                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100">Cleanliness: <strong><?= $rev['cleanliness_rating'] ?>/5</strong></span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100">Location: <strong><?= $rev['location_rating'] ?>/5</strong></span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100">Value: <strong><?= $rev['value_rating'] ?>/5</strong></span>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100">Hospitality: <strong><?= $rev['hospitality_rating'] ?>/5</strong></span>
                                    </div>
                                </div>

                                <!-- Review Title & Body -->
                                <div>
                                    <?php if (!empty($rev['review_title'])): ?>
                                        <h4 class="font-bold text-slate-900 text-sm mb-1"><?= htmlspecialchars($rev['review_title']) ?></h4>
                                    <?php endif; ?>
                                    <p class="text-xs text-slate-600 leading-relaxed max-w-3xl"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                                </div>

                                <?php if (!empty($rev['admin_notes'])): ?>
                                    <div class="p-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs max-w-xl">
                                        <strong>Audit Log:</strong> <?= htmlspecialchars($rev['admin_notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Right Column: Status & Moderation Actions -->
                            <div class="flex flex-col items-end gap-3 flex-shrink-0">
                                <div>
                                    <?php if ($rev['status'] === 'approved'): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-xs">check_circle</span> Published Live
                                        </span>
                                    <?php elseif ($rev['status'] === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="material-symbols-outlined text-xs">cancel</span> Rejected
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="material-symbols-outlined text-xs">schedule</span> Pending Review
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center gap-2">
                                    <?php if ($rev['status'] !== 'approved'): ?>
                                        <form method="POST" action="<?= url('admin/reviews/approve') ?>" onsubmit="return confirm('Publish this review live on the property page?');">
                                            <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">check</span>
                                                <span>Approve</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($rev['status'] !== 'rejected'): ?>
                                        <form method="POST" action="<?= url('admin/reviews/reject') ?>" onsubmit="return confirm('Flag this review as rejected?');">
                                            <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">close</span>
                                                <span>Reject</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" action="<?= url('admin/reviews/delete') ?>" onsubmit="return confirm('Permanently delete this review record?');">
                                        <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Delete Permanently">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../Includes/admin_footer.php'; ?>
