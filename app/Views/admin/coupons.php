<?php
$pageTitle = 'Promo Coupons & Discounts';
include __DIR__ . '/../Includes/admin_header.php';
?>

<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <div class="flex items-center gap-2 text-sky-600 font-semibold text-xs uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm">confirmation_number</span>
                Pricing Engine & Promotions
            </div>
            <h1 class="text-2xl font-black text-slate-800 mt-1">Coupons & Discount Codes</h1>
            <p class="text-sm text-slate-500">Configure promotional vouchers, percentage discounts, minimum cart tiers, and seasonal campaign codes.</p>
        </div>
        <button onclick="openCouponModal()" class="px-5 py-2.5 bg-gradient-to-r from-sky-600 to-blue-600 text-white font-bold rounded-xl text-sm shadow-md hover:from-sky-500 hover:to-blue-500 transition-all flex items-center gap-2 cursor-pointer">
            <span class="material-symbols-outlined text-lg">add_circle</span>
            Create New Coupon
        </button>
    </div>

    <?php if (!empty($message)): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-rose-600">error</span>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Coupons Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Coupon Code</th>
                        <th class="py-4 px-6">Discount Type & Value</th>
                        <th class="py-4 px-6">Min Cart / Cap</th>
                        <th class="py-4 px-6">Validity Window</th>
                        <th class="py-4 px-6">Usage</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($coupons)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2">loyalty</span>
                                No discount coupons created yet. Click "Create New Coupon" to start.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $c): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 bg-sky-50 border border-sky-200 text-sky-700 font-mono font-black text-xs rounded-lg uppercase tracking-wider">
                                            <?= htmlspecialchars($c['code']) ?>
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 max-w-xs truncate"><?= htmlspecialchars($c['description'] ?: 'No description provided') ?></div>
                                </td>
                                <td class="py-4 px-6 font-semibold">
                                    <?php if ($c['discount_type'] === 'percentage'): ?>
                                        <span class="inline-flex items-center gap-1 text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-md text-xs">
                                            <span class="material-symbols-outlined text-xs">percent</span>
                                            <?= (float)$c['discount_value'] ?>% OFF
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md text-xs">
                                            <span>₹</span>
                                            ₹<?= number_format((float)$c['discount_value']) ?> FLAT OFF
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <div>Min Order: <strong>₹<?= number_format((float)$c['min_booking_amount']) ?></strong></div>
                                    <?php if (!empty($c['max_discount_amount'])): ?>
                                        <div class="text-slate-400">Max Cap: ₹<?= number_format((float)$c['max_discount_amount']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <?php if (empty($c['valid_from']) && empty($c['valid_until'])): ?>
                                        <span class="text-slate-400 italic">Lifetime Valid</span>
                                    <?php else: ?>
                                        <div>From: <?= $c['valid_from'] ? date('d M Y', strtotime($c['valid_from'])) : 'Any' ?></div>
                                        <div>Till: <?= $c['valid_until'] ? date('d M Y', strtotime($c['valid_until'])) : 'No expiry' ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-600">
                                    <span><?= (int)$c['times_used'] ?></span> / <span class="text-slate-400"><?= (int)$c['usage_limit'] ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="<?= url('admin/coupons/toggle?id=' . $c['id']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all <?= $c['status'] === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $c['status'] === 'active' ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                        <?= ucfirst($c['status']) ?>
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button onclick='editCoupon(<?= json_encode($c) ?>)' class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition-colors">Edit</button>
                                    <a href="<?= url('admin/coupons/delete?id=' . $c['id']) ?>" onclick="return confirm('Are you sure you want to delete coupon <?= $c['code'] ?>?')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold rounded-lg text-xs transition-colors">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="couponModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 id="modalTitle" class="text-lg font-black text-slate-800">Create New Coupon</h3>
            <button onclick="closeCouponModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form action="<?= url('admin/coupons/save') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="couponId" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Coupon Code *</label>
                <input type="text" name="code" id="couponCode" required placeholder="e.g. WELCOME10, STAYORA500" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-mono font-bold uppercase tracking-wider focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Campaign Description</label>
                <input type="text" name="description" id="couponDesc" placeholder="e.g. 10% discount on initial booking" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Discount Type *</label>
                    <select name="discount_type" id="couponType" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount (₹)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Discount Value *</label>
                    <input type="number" step="0.01" name="discount_value" id="couponVal" required placeholder="10 or 500" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Min Booking Total (₹)</label>
                    <input type="number" step="0.01" name="min_booking_amount" id="couponMin" placeholder="0" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Max Discount Cap (₹)</label>
                    <input type="number" step="0.01" name="max_discount_amount" id="couponMax" placeholder="Optional" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Valid From</label>
                    <input type="date" name="valid_from" id="couponFrom" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Valid Until</label>
                    <input type="date" name="valid_until" id="couponTo" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Usage Limit</label>
                    <input type="number" name="usage_limit" id="couponLimit" value="1000" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                    <select name="status" id="couponStatus" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-sky-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCouponModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 font-semibold cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl text-sm shadow-md transition-colors cursor-pointer">Save Coupon</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCouponModal() {
    document.getElementById('modalTitle').textContent = 'Create New Coupon';
    document.getElementById('couponId').value = '';
    document.getElementById('couponCode').value = '';
    document.getElementById('couponDesc').value = '';
    document.getElementById('couponType').value = 'percentage';
    document.getElementById('couponVal').value = '';
    document.getElementById('couponMin').value = '0';
    document.getElementById('couponMax').value = '';
    document.getElementById('couponFrom').value = '';
    document.getElementById('couponTo').value = '';
    document.getElementById('couponLimit').value = '1000';
    document.getElementById('couponStatus').value = 'active';

    const modal = document.getElementById('couponModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function editCoupon(c) {
    document.getElementById('modalTitle').textContent = 'Edit Coupon: ' + c.code;
    document.getElementById('couponId').value = c.id;
    document.getElementById('couponCode').value = c.code;
    document.getElementById('couponDesc').value = c.description || '';
    document.getElementById('couponType').value = c.discount_type || 'percentage';
    document.getElementById('couponVal').value = c.discount_value || '';
    document.getElementById('couponMin').value = c.min_booking_amount || '0';
    document.getElementById('couponMax').value = c.max_discount_amount || '';
    document.getElementById('couponFrom').value = c.valid_from || '';
    document.getElementById('couponTo').value = c.valid_until || '';
    document.getElementById('couponLimit').value = c.usage_limit || '1000';
    document.getElementById('couponStatus').value = c.status || 'active';

    const modal = document.getElementById('couponModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCouponModal() {
    const modal = document.getElementById('couponModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<?php include __DIR__ . '/../Includes/admin_footer.php'; ?>
