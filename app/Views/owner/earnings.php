<?php
$pageTitle = 'Owner Earnings & Payouts Ledger';
$activePage = 'earnings';
require_once __DIR__ . '/../Includes/owner_header.php';
?>

<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="material-symbols-outlined text-sm">account_balance_wallet</span>
                    Financial Accounting &amp; Settlement
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 mt-2 tracking-tight">Host Earnings &amp; Payouts Ledger</h1>
            <p class="text-sm text-slate-500 mt-1">Real-time revenue accounting, transparent commission breakdown, and bank payout requests.</p>
        </div>

        <button onclick="document.getElementById('payoutModal').classList.remove('hidden')"
                <?= $stats['available_balance'] < 1000 ? 'disabled title="Minimum balance of ₹1,000 required"' : '' ?>
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
            <span class="material-symbols-outlined text-lg">payments</span>
            <span>Request Payout (₹<?= number_format($stats['available_balance'], 2) ?>)</span>
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
        <!-- Gross Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">point_of_sale</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gross Bookings</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5">₹<?= number_format($stats['gross_revenue'], 2) ?></p>
            </div>
        </div>

        <!-- Platform Commission -->
        <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">percent</span>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Platform Share</p>
                <p class="text-2xl font-black text-amber-800 mt-0.5">₹<?= number_format($stats['total_commission'], 2) ?></p>
            </div>
        </div>

        <!-- Net Host Earnings -->
        <div class="bg-white rounded-2xl p-5 border border-sky-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">trending_up</span>
            </div>
            <div>
                <p class="text-xs font-bold text-sky-700 uppercase tracking-wider">Net Host Earnings</p>
                <p class="text-2xl font-black text-sky-800 mt-0.5">₹<?= number_format($stats['net_earnings'], 2) ?></p>
            </div>
        </div>

        <!-- Available for Payout -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-2xl p-5 shadow-lg shadow-emerald-500/20 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">account_balance</span>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Available Balance</p>
                <p class="text-2xl font-black text-white mt-0.5">₹<?= number_format($stats['available_balance'], 2) ?></p>
            </div>
        </div>
    </div>

    <!-- Settlement Secondary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 font-semibold">Total Disbursed to Bank</p>
                <p class="text-lg font-black text-slate-900 mt-0.5">₹<?= number_format($stats['total_paid_out'], 2) ?></p>
            </div>
            <span class="material-symbols-outlined text-emerald-600 text-2xl">task_alt</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 font-semibold">In-Transit / Processing</p>
                <p class="text-lg font-black text-amber-700 mt-0.5">₹<?= number_format($stats['total_processing'], 2) ?></p>
            </div>
            <span class="material-symbols-outlined text-amber-600 text-2xl">pending</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 font-semibold">Settled Booking Units</p>
                <p class="text-lg font-black text-slate-900 mt-0.5"><?= $stats['bookings_count'] ?> Stays</p>
            </div>
            <span class="material-symbols-outlined text-sky-600 text-2xl">receipt_long</span>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
        <button onclick="switchTab('bookingsTab', this)"
                class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold transition bg-slate-900 text-white">
            Booking Revenue &amp; Commission Breakdown
        </button>
        <button onclick="switchTab('payoutsTab', this)"
                class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition text-slate-600 hover:bg-slate-100">
            Disbursement &amp; Payouts History (<?= count($payouts) ?>)
        </button>
    </div>

    <!-- Tab 1: Bookings Breakdown -->
    <div id="bookingsTab" class="tab-panel bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">Per-Booking Revenue Itemization</h2>
            <span class="text-xs text-slate-500 font-semibold"><?= count($bookings) ?> Approved Bookings</span>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <span class="material-symbols-outlined text-3xl">receipt</span>
                </div>
                <h3 class="text-base font-bold text-slate-700">No approved stays recorded yet</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">When guest reservations are approved or settled on Stayora, your revenue ledger will populate automatically.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-6">Booking Reference</th>
                            <th class="py-3 px-6">Property</th>
                            <th class="py-3 px-6">Stay Dates</th>
                            <th class="py-3 px-6 text-right">Gross Tariff</th>
                            <th class="py-3 px-6 text-right">Platform Fee</th>
                            <th class="py-3 px-6 text-right">Net Payable</th>
                            <th class="py-3 px-6 text-center">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                    #BK-<?= str_pad($b['id'], 5, '0', STR_PAD_LEFT) ?>
                                    <span class="block text-[11px] font-sans font-normal text-slate-400">
                                        <?= date('d M Y', strtotime($b['created_at'])) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-800"><?= htmlspecialchars($b['farmhouse_title']) ?></p>
                                    <p class="text-[11px] text-slate-400">Guest: <?= htmlspecialchars($b['customer_name'] ?? 'Guest User') ?></p>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    <?= date('d M Y', strtotime($b['check_in'])) ?> → <?= date('d M Y', strtotime($b['check_out'])) ?>
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-slate-900">
                                    ₹<?= number_format($b['calculated_gross'], 2) ?>
                                </td>
                                <td class="py-4 px-6 text-right font-semibold text-rose-600">
                                    - ₹<?= number_format($b['calculated_comm'], 2) ?>
                                </td>
                                <td class="py-4 px-6 text-right font-black text-emerald-700">
                                    ₹<?= number_format($b['calculated_net'], 2) ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($b['payment_status'] === 'paid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-xs">verified</span> Paid
                                        </span>
                                    <?php elseif ($b['payment_status'] === 'pending_verification'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Verifying
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            Unpaid
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab 2: Payouts Ledger -->
    <div id="payoutsTab" class="tab-panel hidden bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">Bank Disbursement Records</h2>
            <span class="text-xs text-slate-500 font-semibold"><?= count($payouts) ?> Transactions</span>
        </div>

        <?php if (empty($payouts)): ?>
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <span class="material-symbols-outlined text-3xl">account_balance_wallet</span>
                </div>
                <h3 class="text-base font-bold text-slate-700">No payout withdrawals requested yet</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Once you have an available balance over ₹1,000, you can request direct NEFT / RTGS transfers to your account.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-6">Payout ID</th>
                            <th class="py-3 px-6">Requested Date</th>
                            <th class="py-3 px-6 text-right">Net Amount</th>
                            <th class="py-3 px-6">Payment Reference / UTR</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6">Bank Account Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php foreach ($payouts as $p): ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                    #PAY-<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    <?= date('d M Y, h:i A', strtotime($p['created_at'])) ?>
                                </td>
                                <td class="py-4 px-6 text-right font-black text-slate-900 text-sm">
                                    ₹<?= number_format($p['net_payout'], 2) ?>
                                </td>
                                <td class="py-4 px-6 font-mono text-slate-700">
                                    <?= !empty($p['payment_reference']) ? htmlspecialchars($p['payment_reference']) : '<span class="text-slate-400 italic">Pending Bank Transfer</span>' ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($p['payout_status'] === 'completed'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-xs">done_all</span> Completed
                                        </span>
                                    <?php elseif ($p['payout_status'] === 'processing'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            <span class="material-symbols-outlined text-xs">sync</span> In-Transit
                                        </span>
                                    <?php elseif ($p['payout_status'] === 'failed'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="material-symbols-outlined text-xs">error</span> Failed
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="material-symbols-outlined text-xs">schedule</span> Pending Review
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-slate-500 max-w-xs truncate">
                                    <?= !empty($p['notes']) ? htmlspecialchars($p['notes']) : 'Registered Host Bank Account' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Request Payout Modal -->
<div id="payoutModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Request Payout Withdrawal</h3>
                    <p class="text-xs text-slate-500">Available: ₹<?= number_format($stats['available_balance'], 2) ?></p>
                </div>
            </div>
            <button onclick="document.getElementById('payoutModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="<?= url('owner/earnings/request-payout') ?>" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Withdrawal Amount (₹) <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-slate-400 font-bold text-sm">₹</span>
                    <input type="number" name="amount" min="1000" max="<?= $stats['available_balance'] ?>" step="0.01"
                           value="<?= $stats['available_balance'] ?>" required
                           class="w-full text-sm font-bold rounded-xl border border-slate-200 pl-8 pr-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:outline-hidden">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Minimum withdrawal threshold: ₹1,000</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bank Account / UPI Disbursement Details <span class="text-rose-500">*</span></label>
                <textarea name="payout_details" rows="3" required placeholder="Bank Name, Account Holder Name, Account #, IFSC Code or UPI ID"
                          class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:outline-hidden"></textarea>
            </div>

            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-600 text-lg">info</span>
                <p class="text-[11px] text-emerald-800 leading-tight">
                    Disbursements are initiated via direct NEFT / RTGS / IMPS within 24 to 48 business hours upon compliance approval.
                </p>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('payoutModal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition">
                    Submit Payout Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById(tabId).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-slate-900', 'text-white');
        b.classList.add('text-slate-600', 'hover:bg-slate-100');
    });

    btn.classList.add('bg-slate-900', 'text-white');
    btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
}
</script>

<?php require_once __DIR__ . '/../Includes/owner_footer.php'; ?>
