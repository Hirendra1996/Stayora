<?php
$pageTitle = 'Property Inquiries & Customer Leads';
$activePage = 'inquiries';
require_once __DIR__ . '/../Includes/owner_header.php';
?>

<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                    <span class="material-symbols-outlined text-sm">support_agent</span>
                    Guest Inquiries &amp; Pre-Booking Leads
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 mt-2 tracking-tight">Property Leads &amp; Direct Inquiries</h1>
            <p class="text-sm text-slate-500 mt-1">Manage inquiries from guests interested in your farmhouses, track follow-ups, and convert leads to stays.</p>
        </div>
    </div>

    <!-- Notifications -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-rose-600">error</span>
                <span class="text-sm font-semibold"><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">forum</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Leads</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5"><?= $stats['all'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-sky-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">mark_email_unread</span>
            </div>
            <div>
                <p class="text-xs font-bold text-sky-700 uppercase tracking-wider">New Inquiries</p>
                <p class="text-2xl font-black text-sky-800 mt-0.5"><?= $stats['new'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">ring_volume</span>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Contacted</p>
                <p class="text-2xl font-black text-amber-800 mt-0.5"><?= $stats['contacted'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Converted Stays</p>
                <p class="text-2xl font-black text-emerald-800 mt-0.5"><?= $stats['converted'] ?></p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex flex-wrap items-center gap-1">
            <a href="<?= url('owner/inquiries') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $statusFilter === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                All (<?= $stats['all'] ?>)
            </a>
            <a href="<?= url('owner/inquiries?status=new') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $statusFilter === 'new' ? 'bg-sky-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                New (<?= $stats['new'] ?>)
            </a>
            <a href="<?= url('owner/inquiries?status=contacted') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $statusFilter === 'contacted' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Contacted (<?= $stats['contacted'] ?>)
            </a>
            <a href="<?= url('owner/inquiries?status=converted') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $statusFilter === 'converted' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Converted (<?= $stats['converted'] ?>)
            </a>
            <a href="<?= url('owner/inquiries?status=closed') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $statusFilter === 'closed' ? 'bg-slate-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                Closed (<?= $stats['closed'] ?>)
            </a>
        </div>

        <form method="GET" action="<?= url('owner/inquiries') ?>" class="flex items-center gap-2 w-full md:w-auto">
            <?php if ($statusFilter !== 'all'): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
            <?php endif; ?>

            <select name="farmhouse_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border border-slate-200 px-3 py-2 bg-slate-50">
                <option value="">All Properties</option>
                <?php foreach ($farmhouses as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= $farmFilter == $f['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="relative">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search name, phone..."
                       class="text-xs font-semibold rounded-xl border border-slate-200 pl-8 pr-3 py-2 bg-slate-50 focus:bg-white focus:outline-hidden w-40 md:w-52">
                <span class="material-symbols-outlined text-slate-400 text-sm absolute left-2.5 top-2.5">search</span>
            </div>

            <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Inquiries Cards Grid -->
    <?php if (empty($inquiries)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center shadow-xs">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                <span class="material-symbols-outlined text-3xl">inbox</span>
            </div>
            <h3 class="text-base font-bold text-slate-700">No customer inquiries found</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">When prospective guests reach out regarding your farmhouses, their inquiries will appear here in real-time.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($inquiries as $inq): ?>
                <?php
                $cleanPhone = preg_replace('/[^0-9]/', '', $inq['phone']);
                $stat = strtolower($inq['status'] ?? 'new');
                ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Top Row: Lead contact & Status -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center font-black text-sm flex-shrink-0">
                                    <?= strtoupper(substr($inq['name'] ?? 'G', 0, 1)) ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm leading-tight"><?= htmlspecialchars($inq['name']) ?></h3>
                                    <p class="text-xs text-slate-500 font-semibold mt-0.5"><?= htmlspecialchars($inq['phone']) ?></p>
                                </div>
                            </div>

                            <!-- Status Dropdown Form -->
                            <form method="POST" action="<?= url('owner/inquiries/update-status') ?>" class="flex-shrink-0">
                                <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                <select name="status" onchange="this.form.submit()"
                                        class="text-[11px] font-bold rounded-lg px-2.5 py-1 border transition cursor-pointer
                                        <?php
                                        if ($stat === 'new') echo 'bg-sky-50 text-sky-700 border-sky-200';
                                        elseif ($stat === 'contacted') echo 'bg-amber-50 text-amber-700 border-amber-200';
                                        elseif ($stat === 'converted') echo 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        else echo 'bg-slate-100 text-slate-600 border-slate-200';
                                        ?>">
                                    <option value="new" <?= $stat === 'new' ? 'selected' : '' ?>>New</option>
                                    <option value="contacted" <?= $stat === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                    <option value="converted" <?= $stat === 'converted' ? 'selected' : '' ?>>Converted</option>
                                    <option value="closed" <?= $stat === 'closed' ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </form>
                        </div>

                        <!-- Target Farmhouse -->
                        <div class="p-2.5 bg-slate-50 rounded-xl mb-3 flex items-center gap-2 border border-slate-100">
                            <span class="material-symbols-outlined text-sky-600 text-base flex-shrink-0">villa</span>
                            <div class="truncate text-xs">
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($inq['farmhouse_title']) ?></span>
                                <span class="text-slate-400 block text-[10px]"><?= htmlspecialchars($inq['farmhouse_location'] ?? '') ?></span>
                            </div>
                        </div>

                        <!-- Guest Message -->
                        <div class="text-xs text-slate-600 leading-relaxed mb-3 bg-white p-2.5 rounded-xl border border-slate-100 min-h-[50px]">
                            <?= !empty($inq['message']) ? nl2br(htmlspecialchars($inq['message'])) : '<span class="text-slate-400 italic">Direct call / WhatsApp request</span>' ?>
                        </div>

                        <!-- Follow-up Notes if present -->
                        <?php if (!empty($inq['notes'])): ?>
                            <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 mb-3">
                                <div class="flex items-center gap-1 font-bold text-[11px] text-amber-800 mb-0.5">
                                    <span class="material-symbols-outlined text-xs">edit_note</span> Host Note:
                                </div>
                                <p><?= htmlspecialchars($inq['notes']) ?></p>
                                <?php if (!empty($inq['follow_up_date'])): ?>
                                    <p class="text-[10px] text-amber-700 mt-1 font-semibold">Follow-up Due: <?= date('d M Y', strtotime($inq['follow_up_date'])) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bottom Actions & Date -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-semibold text-slate-400">
                            <?= date('d M Y, h:i A', strtotime($inq['created_at'])) ?>
                        </span>

                        <div class="flex items-center gap-2">
                            <!-- Direct Call -->
                            <a href="tel:<?= htmlspecialchars($inq['phone']) ?>"
                               class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-500 text-sky-600 hover:text-white flex items-center justify-center transition shadow-2xs"
                               title="Call Guest">
                                <span class="material-symbols-outlined text-base">call</span>
                            </a>

                            <!-- WhatsApp Message -->
                            <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= urlencode('Hello ' . $inq['name'] . ', thank you for your inquiry about ' . $inq['farmhouse_title'] . ' on Stayora! How may I assist you with your booking?') ?>"
                               target="_blank"
                               class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white flex items-center justify-center transition shadow-2xs"
                               title="Chat on WhatsApp">
                                <span class="material-symbols-outlined text-base">chat</span>
                            </a>

                            <!-- Add / Edit Note Button -->
                            <button onclick="openNoteModal(<?= $inq['id'] ?>, '<?= addslashes($inq['notes'] ?? '') ?>', '<?= $inq['follow_up_date'] ?? '' ?>', '<?= $stat ?>')"
                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition shadow-2xs"
                                    title="Add Follow-up Note">
                                <span class="material-symbols-outlined text-base">note_add</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<!-- Note / Pipeline Update Modal -->
<div id="noteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">Update Lead &amp; Notes</h3>
            <button onclick="document.getElementById('noteModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="<?= url('owner/inquiries/update-status') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="inquiry_id" id="modalInquiryId">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pipeline Status</label>
                <select name="status" id="modalStatus" class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                    <option value="new">New (Uncontacted)</option>
                    <option value="contacted">Contacted (In Discussion)</option>
                    <option value="converted">Converted (Booking Secured)</option>
                    <option value="closed">Closed (Not Interested / Dropped)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Follow-up Notes</label>
                <textarea name="notes" id="modalNotes" rows="3" placeholder="e.g. Guest requested price for 25 people on Saturday, will confirm by evening..."
                          class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Next Follow-up Due Date</label>
                <input type="date" name="follow_up_date" id="modalFollowUp"
                       class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('noteModal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition">
                    Save Updates
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openNoteModal(id, notes, followUp, status) {
    document.getElementById('modalInquiryId').value = id;
    document.getElementById('modalNotes').value = notes;
    document.getElementById('modalFollowUp').value = followUp;
    document.getElementById('modalStatus').value = status;
    document.getElementById('noteModal').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/../Includes/owner_footer.php'; ?>
