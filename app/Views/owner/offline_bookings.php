<?php
$pageTitle = 'Offline Bookings & Guest Dossiers';
$activePage = 'offline_bookings';
require_once __DIR__ . '/../Includes/owner_header.php';
?>

<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="material-symbols-outlined text-sm">assignment_ind</span>
                    Direct Walk-In &amp; Private Bookings
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 mt-2 tracking-tight">Offline Bookings &amp; Guest Dossiers</h1>
            <p class="text-sm text-slate-500 mt-1">Record direct telephone inquiries, cash walk-ins, and synchronize calendar blocking automatically.</p>
        </div>

        <button onclick="document.getElementById('newOfflineModal').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold text-sm shadow-lg shadow-sky-500/25 transition-all">
            <span class="material-symbols-outlined text-lg">add</span>
            <span>Record Direct Guest</span>
        </button>
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
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Direct Revenue</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5">₹<?= number_format($stats['total_revenue']) ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-sky-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-xs font-bold text-sky-700 uppercase tracking-wider">Advance Collected</p>
                <p class="text-2xl font-black text-sky-800 mt-0.5">₹<?= number_format($stats['advance_total']) ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">bed</span>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Active Stays</p>
                <p class="text-2xl font-black text-amber-800 mt-0.5"><?= $stats['active'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">person_book</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Records</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5"><?= $stats['total'] ?></p>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="<?= url('owner/offline-bookings') ?>" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div>
                <select name="farmhouse_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border border-slate-200 px-3 py-2 bg-slate-50">
                    <option value="">All Properties</option>
                    <?php foreach ($farmhouses as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= $farmFilter == $f['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-xl border border-slate-200 px-3 py-2 bg-slate-50">
                    <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="checked_in" <?= $statusFilter === 'checked_in' ? 'selected' : '' ?>>Checked In</option>
                    <option value="checked_out" <?= $statusFilter === 'checked_out' ? 'selected' : '' ?>>Checked Out</option>
                    <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search guest name or phone..."
                       class="w-full text-xs font-semibold rounded-xl border border-slate-200 pl-8 pr-3 py-2 bg-slate-50 focus:bg-white focus:outline-hidden">
                <span class="material-symbols-outlined text-slate-400 text-sm absolute left-2.5 top-2.5">search</span>
            </div>

            <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition">
                Filter
            </button>
            <?php if (!empty($search) || $farmFilter || $statusFilter !== 'all'): ?>
                <a href="<?= url('owner/offline-bookings') ?>" class="text-xs text-slate-500 font-semibold hover:underline">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Offline Bookings Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">Direct Guests Ledger</h2>
            <span class="text-xs text-slate-500 font-semibold"><?= count($bookings) ?> Records</span>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <span class="material-symbols-outlined text-3xl">hotel_class</span>
                </div>
                <h3 class="text-base font-bold text-slate-700">No direct bookings logged yet</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Keep full control over walk-in guests and automatically synchronize calendar availability across platforms.</p>
                <button onclick="document.getElementById('newOfflineModal').classList.remove('hidden')"
                        class="mt-4 px-4 py-2 bg-sky-600 text-white rounded-xl text-xs font-bold hover:bg-sky-700 transition">
                    Record Direct Guest
                </button>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-6">Guest Dossier</th>
                            <th class="py-3 px-6">Property / Unit</th>
                            <th class="py-3 px-6">Stay Period</th>
                            <th class="py-3 px-6">Financials</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php foreach ($bookings as $b): ?>
                            <?php
                            $nights = max(1, (int)round((strtotime($b['check_out']) - strtotime($b['check_in'])) / 86400));
                            $balance = max(0, (float)$b['total_amount'] - (float)$b['advance_collected']);
                            ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-black">
                                            <?= strtoupper(substr($b['guest_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($b['guest_name']) ?></p>
                                            <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[12px]">call</span>
                                                <?= htmlspecialchars($b['guest_phone']) ?>
                                            </p>
                                            <?php if (!empty($b['guest_email'])): ?>
                                                <p class="text-[10px] text-slate-400"><?= htmlspecialchars($b['guest_email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($b['farmhouse_title']) ?></p>
                                    <p class="text-[11px] text-slate-400 capitalize">
                                        <?= $b['booking_type'] === 'room' ? ('Room: ' . htmlspecialchars($b['room_type_name'] ?? 'Custom')) : 'Full Property Stay' ?>
                                        • <?= $b['guests'] ?> Guests (<?= $b['rooms'] ?> Rooms)
                                    </p>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-semibold text-slate-800">
                                        <?= date('d M Y', strtotime($b['check_in'])) ?> → <?= date('d M Y', strtotime($b['check_out'])) ?>
                                    </p>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold">
                                        <?= $nights ?> Night<?= $nights > 1 ? 's' : '' ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-900">Total: ₹<?= number_format($b['total_amount'], 2) ?></p>
                                    <p class="text-[11px] text-emerald-700 font-semibold">Adv: ₹<?= number_format($b['advance_collected'], 2) ?> (<?= htmlspecialchars($b['payment_mode']) ?>)</p>
                                    <?php if ($balance > 0): ?>
                                        <p class="text-[10px] text-amber-700 font-bold">Bal: ₹<?= number_format($balance, 2) ?></p>
                                    <?php else: ?>
                                        <p class="text-[10px] text-emerald-600 font-bold">Fully Settled</p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($b['status'] === 'confirmed'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Confirmed
                                        </span>
                                    <?php elseif ($b['status'] === 'checked_in'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            Checked In
                                        </span>
                                    <?php elseif ($b['status'] === 'checked_out'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                            Checked Out
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Cancelled
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <?php if (!empty($b['notes'])): ?>
                                            <button onclick="alert('Special Notes for <?= addslashes($b['guest_name']) ?>:\n\n<?= addslashes($b['notes']) ?>')"
                                                    class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-sky-50 transition" title="View Notes">
                                                <span class="material-symbols-outlined text-base">sticky_note_2</span>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($b['status'] !== 'cancelled'): ?>
                                            <form method="POST" action="<?= url('owner/offline-bookings/cancel') ?>" onsubmit="return confirm('Cancel this direct booking and release locked calendar dates?');" class="inline">
                                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Cancel Booking">
                                                    <span class="material-symbols-outlined text-base">cancel</span>
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
        <?php endif; ?>
    </div>

</div>

<!-- Record Direct Guest Modal -->
<div id="newOfflineModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">person_add</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Record Direct Walk-In Guest</h3>
                    <p class="text-xs text-slate-500">Capture guest details, tariff, advance and sync calendar dates</p>
                </div>
            </div>
            <button onclick="document.getElementById('newOfflineModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="<?= url('owner/offline-bookings/create') ?>" method="POST" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Property <span class="text-rose-500">*</span></label>
                    <select name="farmhouse_id" required id="modalFarmhouseSelect" class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-sky-500 focus:outline-hidden">
                        <option value="">-- Select Farmhouse --</option>
                        <?php foreach ($farmhouses as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Booking Type</label>
                    <select name="booking_type" id="modalBookingType" class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-sky-500 focus:outline-hidden">
                        <option value="entire">Entire Farmhouse</option>
                        <option value="room">Individual Room / Unit</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Guest Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="guest_name" required placeholder="e.g. Rahul Sharma"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="guest_phone" required placeholder="10-digit mobile"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" name="guest_email" placeholder="Optional"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Check-In <span class="text-rose-500">*</span></label>
                    <input type="date" name="check_in" required
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Check-Out <span class="text-rose-500">*</span></label>
                    <input type="date" name="check_out" required
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Guests Count</label>
                    <input type="number" name="guests" min="1" value="2"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rooms Count</label>
                    <input type="number" name="rooms" min="1" value="1"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Total Tariff (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" name="total_amount" required step="0.01" placeholder="0.00"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Advance Collected (₹)</label>
                    <input type="number" name="advance_collected" step="0.01" value="0.00"
                           class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Payment Mode</label>
                    <select name="payment_mode" class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden">
                        <option value="Cash">Cash at Counter</option>
                        <option value="UPI / QR">UPI (GPay / PhonePe / Paytm)</option>
                        <option value="Bank Transfer">Direct Bank Transfer / NEFT</option>
                        <option value="POS Card">Debit / Credit Card</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Guest Notes / ID Reference</label>
                <textarea name="notes" rows="2" placeholder="e.g. Aadhaar #, ID details, or special arrangement requests..."
                          class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden"></textarea>
            </div>

            <!-- Auto lock sync checkbox -->
            <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl flex items-center gap-3">
                <input type="checkbox" name="auto_lock_dates" value="1" checked id="autoLockCb" class="rounded text-sky-600 focus:ring-sky-500 h-4 w-4">
                <label for="autoLockCb" class="text-xs font-bold text-sky-900 cursor-pointer">
                    Synchronize &amp; Lock Property Calendar on Website
                    <span class="block text-[11px] font-normal text-sky-700">Prevents public website users from double-booking these dates during the guest's stay.</span>
                </label>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('newOfflineModal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition">
                    Save Direct Booking
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../Includes/owner_footer.php'; ?>
