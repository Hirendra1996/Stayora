<?php
$pageTitle  = "Payment & Verification Desk";
$activePage = "payments";

include __DIR__ . "/../Includes/admin_header.php";

$totalVerifiedAmount = (float)($stats['total_verified_amount'] ?? 0);
$pendingProofsCount  = (int)($stats['pending_proofs_count'] ?? 0);
$unpaidCount         = (int)($stats['unpaid_count'] ?? 0);
$withProofCount      = (int)($stats['with_proof_count'] ?? 0);
?>

<div class="space-y-6 pb-16">

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

    <!-- ── 2. PAGE HEADER ─────────────────────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1 text-xs text-muted mb-1 font-semibold">
                <a href="<?= url('admin/dashboard') ?>" class="hover:text-sky transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink font-bold">Payments &amp; Proofs Desk (F41)</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-sky text-3xl">verified</span>
                Payment Verification &amp; Proof Inspection Desk
            </h1>
            <p class="text-xs sm:text-sm text-muted mt-1">
                Audit transaction UTR numbers, inspect payment screenshots, reconcile bank credits, and mark bookings Paid.
            </p>
        </div>
    </div>

    <!-- ── 3. KPI STAT CARDS ────────────────────────────────────────────────── -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Verified Amount -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted">Verified Collections</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
            </div>
            <div class="text-2xl font-black text-ink">₹<?= number_format($totalVerifiedAmount) ?></div>
            <p class="text-xs text-emerald-600 font-semibold mt-1">Confirmed bank &amp; UPI credits</p>
        </div>

        <!-- Pending Proofs -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted">Needs Verification</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">pending_actions</span>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600"><?= $pendingProofsCount ?></div>
            <p class="text-xs text-muted font-medium mt-1">Awaiting screenshot / UTR review</p>
        </div>

        <!-- Pay at Property / Unpaid -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted">Pay at Check-In</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">pin_drop</span>
                </div>
            </div>
            <div class="text-2xl font-black text-ink"><?= $unpaidCount ?></div>
            <p class="text-xs text-muted font-medium mt-1">Collect on guest arrival</p>
        </div>

        <!-- Total Uploaded Proofs -->
        <div class="bg-surface border border-border rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted">Uploaded Proofs</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
            </div>
            <div class="text-2xl font-black text-ink"><?= $withProofCount ?></div>
            <p class="text-xs text-muted font-medium mt-1">UTR or screenshot on file</p>
        </div>
    </div>

    <!-- ── 4. FILTER CONTROLS ────────────────────────────────────────────────── -->
    <div class="bg-surface border border-border rounded-2xl p-4 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <form method="GET" action="<?= url('admin/payments') ?>" class="flex flex-wrap items-center gap-3 w-full">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[220px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-muted text-[18px]">search</span>
                <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Search UTR, Request ID, Guest Name, Phone..." 
                       class="w-full pl-9 pr-3 py-2 bg-surface-alt border border-border rounded-xl text-xs font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" 
                    class="px-3 py-2 bg-surface-alt border border-border rounded-xl text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                <option value="all" <?= ($statusFilter ?? '') === 'all' ? 'selected' : '' ?>>All Reconciliation Statuses</option>
                <option value="pending_verification" <?= ($statusFilter ?? '') === 'pending_verification' ? 'selected' : '' ?>>⏳ Pending Verification (Proof Submitted)</option>
                <option value="paid" <?= ($statusFilter ?? '') === 'paid' ? 'selected' : '' ?>>✓ Paid &amp; Verified</option>
                <option value="unpaid" <?= ($statusFilter ?? '') === 'unpaid' ? 'selected' : '' ?>>● Unpaid / Settle on Arrival</option>
                <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>✕ Proof Rejected</option>
            </select>

            <!-- Payment Method Filter -->
            <select name="method" onchange="this.form.submit()" 
                    class="px-3 py-2 bg-surface-alt border border-border rounded-xl text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                <option value="all" <?= ($methodFilter ?? '') === 'all' ? 'selected' : '' ?>>All Methods</option>
                <option value="upi" <?= ($methodFilter ?? '') === 'upi' ? 'selected' : '' ?>>⚡ UPI Scan &amp; Pay</option>
                <option value="bank_transfer" <?= ($methodFilter ?? '') === 'bank_transfer' ? 'selected' : '' ?>>🏦 Direct Bank Transfer</option>
                <option value="pay_at_property" <?= ($methodFilter ?? '') === 'pay_at_property' ? 'selected' : '' ?>>🏨 Pay at Property</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                Filter
            </button>
            <?php if (!empty($_GET['q']) || ($statusFilter ?? 'all') !== 'all' || ($methodFilter ?? 'all') !== 'all'): ?>
                <a href="<?= url('admin/payments') ?>" class="text-xs text-muted hover:text-ink font-semibold">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- ── 5. PAYMENTS & PROOFS TABLE ────────────────────────────────────────── -->
    <div class="bg-surface border border-border rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-surface-alt border-b border-border text-[11px] font-bold uppercase tracking-wider text-muted">
                        <th class="py-3.5 px-4">Booking Ref</th>
                        <th class="py-3.5 px-4">Guest Information</th>
                        <th class="py-3.5 px-4">Property &amp; Stay</th>
                        <th class="py-3.5 px-4">Total Tariff</th>
                        <th class="py-3.5 px-4">Payment Method</th>
                        <th class="py-3.5 px-4">UTR &amp; Proof (F39)</th>
                        <th class="py-3.5 px-4">Status (F40)</th>
                        <th class="py-3.5 px-4 text-right">Verification Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-muted">
                                <span class="material-symbols-outlined text-4xl block mb-2 opacity-40">receipt_long</span>
                                <p class="text-sm font-semibold">No payment verification records found matching your filters.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $p): 
                            $reqId = (int)$p['req_id'];
                            $pStatus = strtolower($p['payment_status'] ?? 'unpaid');
                            $pMethod = strtolower($p['payment_method'] ?? 'upi');
                            $hasProof = !empty($p['payment_proof']);
                            $proofUrl = $hasProof ? asset($p['payment_proof']) : '';
                        ?>
                        <tr class="hover:bg-surface-alt/60 transition-colors">
                            <!-- Ref ID -->
                            <td class="py-3.5 px-4 font-mono font-bold text-ink">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sky">#REQ-<?= $reqId ?></span>
                                    <a href="<?= url('booking/voucher?id=' . $reqId) ?>" target="_blank" title="View Official Voucher" class="text-muted hover:text-sky">
                                        <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                    </a>
                                </div>
                                <span class="text-[10px] text-muted block font-sans font-normal mt-0.5">
                                    <?= date('d M Y, h:i A', strtotime($p['req_date'])) ?>
                                </span>
                            </td>

                            <!-- Guest Info -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-ink"><?= htmlspecialchars($p['cust_name']) ?></div>
                                <div class="text-[11px] text-muted font-medium"><?= htmlspecialchars($p['cust_phone']) ?></div>
                                <div class="text-[10.5px] text-muted truncate max-w-[160px]"><?= htmlspecialchars($p['cust_email']) ?></div>
                            </td>

                            <!-- Property -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-ink truncate max-w-[180px]"><?= htmlspecialchars($p['farm_title']) ?></div>
                                <div class="text-[11px] text-muted"><?= htmlspecialchars($p['farm_location']) ?></div>
                                <div class="text-[10.5px] text-slate-500 mt-0.5">
                                    <?= date('d M', strtotime($p['check_in'])) ?> → <?= date('d M Y', strtotime($p['check_out'])) ?>
                                </div>
                            </td>

                            <!-- Tariff -->
                            <td class="py-3.5 px-4 font-bold text-ink text-sm">
                                ₹<?= number_format((float)$p['price']) ?>
                            </td>

                            <!-- Method -->
                            <td class="py-3.5 px-4">
                                <?php if ($pMethod === 'upi'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky text-[11px] font-bold border border-sky/20">
                                        <span class="material-symbols-outlined text-[14px]">qr_code_scanner</span>
                                        <span>UPI Pay</span>
                                    </span>
                                <?php elseif ($pMethod === 'bank_transfer'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-[11px] font-bold border border-purple-200">
                                        <span class="material-symbols-outlined text-[14px]">account_balance</span>
                                        <span>Bank Wire</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                        <span class="material-symbols-outlined text-[14px]">payments</span>
                                        <span>At Property</span>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- UTR & Proof Inspection -->
                            <td class="py-3.5 px-4">
                                <?php if (!empty($p['utr_number'])): ?>
                                    <div class="font-mono text-[11px] font-bold text-ink bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block mb-1">
                                        UTR: <?= htmlspecialchars($p['utr_number']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[10.5px] text-muted italic block">No UTR logged</span>
                                <?php endif; ?>

                                <?php if ($hasProof): ?>
                                    <div>
                                        <button type="button" onclick="openProofModal('<?= $proofUrl ?>', '<?= $reqId ?>', '<?= htmlspecialchars($p['utr_number'] ?? '') ?>')" 
                                                class="inline-flex items-center gap-1 text-[11px] text-sky hover:text-sky-hover font-bold hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            <span>Inspect Proof</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Payment Status -->
                            <td class="py-3.5 px-4">
                                <?php if ($pStatus === 'paid'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10.5px] font-extrabold border border-emerald-300">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                        <span>Paid &amp; Verified</span>
                                    </span>
                                    <?php if (!empty($p['payment_verified_at'])): ?>
                                        <span class="text-[9.5px] text-muted block mt-0.5"><?= date('d M, h:i A', strtotime($p['payment_verified_at'])) ?></span>
                                    <?php endif; ?>
                                <?php elseif ($pStatus === 'pending_verification'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10.5px] font-extrabold border border-amber-300 animate-pulse">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        <span>Pending Review</span>
                                    </span>
                                <?php elseif ($pStatus === 'rejected'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10.5px] font-extrabold border border-rose-300">
                                        <span class="material-symbols-outlined text-[13px]">cancel</span>
                                        <span>Rejected</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[10.5px] font-bold border border-slate-300">
                                        <span>Unpaid</span>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <?php if ($pStatus !== 'paid'): ?>
                                        <button type="button" onclick="openVerifyModal('<?= $reqId ?>', '<?= htmlspecialchars($p['cust_name']) ?>', '<?= number_format((float)$p['price']) ?>')" 
                                                class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1" title="Verify Payment & Mark Paid">
                                            <span class="material-symbols-outlined text-[15px]">done</span>
                                            <span>Mark Paid</span>
                                        </button>

                                        <?php if ($pStatus === 'pending_verification'): ?>
                                            <button type="button" onclick="openRejectModal('<?= $reqId ?>')" 
                                                    class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition-colors border border-rose-200" title="Reject Proof">
                                                <span class="material-symbols-outlined text-[15px]">close</span>
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1 justify-end">
                                            <span class="material-symbols-outlined text-[15px]">verified</span>
                                            <span>Verified</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── MODAL: PROOF INSPECTION LIGHTBOX ──────────────────────────────────────── -->
<div id="proofModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-border animate-scale-up">
        <div class="p-4 border-b border-border flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sky text-xl">receipt</span>
                <h3 class="font-extrabold text-sm text-ink" id="proofModalTitle">Payment Receipt Proof</h3>
            </div>
            <button onclick="closeProofModal()" class="text-muted hover:text-ink">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-4 text-center bg-slate-900 flex items-center justify-center min-h-[300px] max-h-[500px] overflow-auto">
            <img id="proofModalImg" src="" alt="Payment Proof Screenshot" class="max-w-full max-h-[460px] object-contain rounded-lg shadow-md">
        </div>
        <div class="p-4 bg-surface-alt flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-ink" id="proofModalUtr"></span>
            <a id="proofModalDownload" href="" target="_blank" download class="px-3 py-1.5 bg-sky text-white rounded-xl text-xs font-bold flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">download</span>
                <span>Open Full Image</span>
            </a>
        </div>
    </div>
</div>

<!-- ── MODAL: VERIFY CONFIRMATION ────────────────────────────────────────────── -->
<div id="verifyModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface rounded-3xl max-w-md w-full p-6 shadow-2xl border border-border animate-scale-up">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <h3 class="font-extrabold text-base text-ink">Confirm Payment Verification</h3>
                <p class="text-xs text-muted">Booking #<span id="verifyReqId"></span></p>
            </div>
        </div>

        <p class="text-xs text-ink leading-relaxed mb-4">
            Are you sure you want to mark this reservation for <strong id="verifyGuestName"></strong> (₹<span id="verifyAmount"></span>) as <strong class="text-emerald-700">PAID</strong>?
        </p>

        <form method="POST" action="<?= url('admin/payments/verify') ?>" class="space-y-4">
            <input type="hidden" name="request_id" id="verifyFormReqId" value="">

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-muted mb-1">Reconciliation Notes</label>
                <input type="text" name="notes" value="Verified in bank / UPI statement." 
                       class="w-full px-3 py-2 bg-surface-alt border border-border rounded-xl text-xs font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
            </div>

            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-ink">
                <input type="checkbox" name="auto_approve_booking" value="1" checked class="rounded border-border text-sky focus:ring-sky">
                <span>Also auto-approve booking status if pending</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeVerifyModal()" class="px-4 py-2 border border-border rounded-xl text-xs font-bold text-ink hover:bg-surface-alt">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20">
                    Verify &amp; Mark Paid
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL: REJECT PROOF ─────────────────────────────────────────────────── -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-surface rounded-3xl max-w-md w-full p-6 shadow-2xl border border-border animate-scale-up">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">cancel</span>
            </div>
            <div>
                <h3 class="font-extrabold text-base text-ink">Reject Payment Proof</h3>
                <p class="text-xs text-muted">Booking #<span id="rejectReqId"></span></p>
            </div>
        </div>

        <form method="POST" action="<?= url('admin/payments/reject') ?>" class="space-y-4">
            <input type="hidden" name="request_id" id="rejectFormReqId" value="">

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-muted mb-1">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="2" placeholder="e.g. UTR number not found in bank statement / Screenshot unclear..." 
                          class="w-full px-3 py-2 bg-surface-alt border border-border rounded-xl text-xs font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 resize-none" required></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-border rounded-xl text-xs font-bold text-ink hover:bg-surface-alt">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20">
                    Reject Proof
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openProofModal(imgUrl, reqId, utr) {
    document.getElementById('proofModalImg').src = imgUrl;
    document.getElementById('proofModalTitle').textContent = `Payment Proof — Booking #REQ-${reqId}`;
    document.getElementById('proofModalUtr').textContent = utr ? `UTR: ${utr}` : '';
    document.getElementById('proofModalDownload').href = imgUrl;
    document.getElementById('proofModal').classList.remove('hidden');
}
function closeProofModal() {
    document.getElementById('proofModal').classList.add('hidden');
}

function openVerifyModal(reqId, name, amount) {
    document.getElementById('verifyReqId').textContent = reqId;
    document.getElementById('verifyFormReqId').value = reqId;
    document.getElementById('verifyGuestName').textContent = name;
    document.getElementById('verifyAmount').textContent = amount;
    document.getElementById('verifyModal').classList.remove('hidden');
}
function closeVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
}

function openRejectModal(reqId) {
    document.getElementById('rejectReqId').textContent = reqId;
    document.getElementById('rejectFormReqId').value = reqId;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
