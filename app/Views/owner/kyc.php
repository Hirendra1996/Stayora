<?php
$pageTitle = 'KYC & Ownership Verification';
$activePage = 'kyc';
require_once __DIR__ . '/../Includes/owner_header.php';
?>

<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-600 border border-sky-200">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    Compliance &amp; Trust Desk
                </span>
                <?php if (!empty($owner['is_verified'])): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                        <span class="material-symbols-outlined text-xs">verified</span> Verified Host
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        <span class="material-symbols-outlined text-xs">hourglass_empty</span> Verification In Progress
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 mt-2 tracking-tight">Host KYC &amp; Property Title Documents</h1>
            <p class="text-sm text-slate-500 mt-1">Submit legal ownership papers, electricity bills, and government identity proof to maintain verified host status.</p>
        </div>

        <button onclick="document.getElementById('uploadKycModal').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold text-sm shadow-lg shadow-sky-500/25 transition-all">
            <span class="material-symbols-outlined text-lg">upload_file</span>
            <span>Upload New Document</span>
        </button>
    </div>

    <!-- Notification Messages -->
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
                <span class="material-symbols-outlined text-2xl">description</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Uploads</p>
                <p class="text-2xl font-black text-slate-900 mt-0.5"><?= $stats['total'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Verified</p>
                <p class="text-2xl font-black text-emerald-800 mt-0.5"><?= $stats['verified'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-amber-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">hourglass_top</span>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Under Review</p>
                <p class="text-2xl font-black text-amber-800 mt-0.5"><?= $stats['pending'] ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">unpublished</span>
            </div>
            <div>
                <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Action Needed</p>
                <p class="text-2xl font-black text-rose-800 mt-0.5"><?= $stats['rejected'] ?></p>
            </div>
        </div>
    </div>

    <!-- Instructions / Trust Checklist Banner -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-6 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-sky-300 text-xs font-bold">
                    <span class="material-symbols-outlined text-sm">shield</span>
                    Verification Criteria
                </div>
                <h3 class="text-lg font-bold">Why upload KYC &amp; Ownership documents?</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Verified properties receive a verified badge on public search pages, rank higher on guest recommendations, and enjoy expedited automated payouts. Upload clear scans or high-res photos.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 text-xs flex-shrink-0">
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-3 py-2">
                    <span class="material-symbols-outlined text-emerald-400 text-base">id_card</span>
                    <span>Host Identity (PAN / Aadhaar)</span>
                </div>
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-3 py-2">
                    <span class="material-symbols-outlined text-emerald-400 text-base">real_estate_agent</span>
                    <span>Title Deed / 7/12 Extract</span>
                </div>
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-3 py-2">
                    <span class="material-symbols-outlined text-emerald-400 text-base">electric_meter</span>
                    <span>Recent Electricity Bill</span>
                </div>
                <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-3 py-2">
                    <span class="material-symbols-outlined text-emerald-400 text-base">policy</span>
                    <span>Trade / FSSAI License</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">Submitted Documents Roster</h2>
            <span class="text-xs text-slate-500 font-semibold"><?= count($documents) ?> Records</span>
        </div>

        <?php if (empty($documents)): ?>
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <span class="material-symbols-outlined text-3xl">cloud_off</span>
                </div>
                <h3 class="text-base font-bold text-slate-700">No documents uploaded yet</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Upload your identity proof and property deeds to initiate platform verification.</p>
                <button onclick="document.getElementById('uploadKycModal').classList.remove('hidden')"
                        class="mt-4 px-4 py-2 bg-sky-600 text-white rounded-xl text-xs font-bold hover:bg-sky-700 transition">
                    Upload Your First Document
                </button>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-6">Document Type</th>
                            <th class="py-3 px-6">Associated Property</th>
                            <th class="py-3 px-6">Document #</th>
                            <th class="py-3 px-6">Submission Date</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php foreach ($documents as $doc): ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                            <span class="material-symbols-outlined text-lg">
                                                <?php
                                                switch ($doc['document_type']) {
                                                    case 'aadhaar':
                                                    case 'pan':
                                                        echo 'badge'; break;
                                                    case 'electricity_bill':
                                                        echo 'electric_meter'; break;
                                                    case 'property_registry':
                                                        echo 'assignment'; break;
                                                    case 'fssai_license':
                                                        echo 'verified'; break;
                                                    default:
                                                        echo 'description';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 capitalize">
                                                <?= str_replace('_', ' ', htmlspecialchars($doc['document_type'])) ?>
                                            </p>
                                            <p class="text-[11px] text-slate-400">ID: #DOC-<?= $doc['id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    <?php if (!empty($doc['farmhouse_title'])): ?>
                                        <span class="inline-flex items-center gap-1 font-semibold text-slate-800">
                                            <span class="material-symbols-outlined text-xs text-sky-500">villa</span>
                                            <?= htmlspecialchars($doc['farmhouse_title']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic">Host Identity (All Properties)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 font-mono font-semibold text-slate-700">
                                    <?= !empty($doc['document_number']) ? htmlspecialchars($doc['document_number']) : '—' ?>
                                </td>
                                <td class="py-4 px-6 text-slate-500">
                                    <?= date('d M Y, h:i A', strtotime($doc['created_at'])) ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($doc['status'] === 'verified'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-xs">verified</span> Verified
                                        </span>
                                    <?php elseif ($doc['status'] === 'rejected'): ?>
                                        <div>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="material-symbols-outlined text-xs">cancel</span> Rejected
                                            </span>
                                            <?php if (!empty($doc['rejection_reason'])): ?>
                                                <p class="text-[10px] text-rose-600 mt-1 max-w-xs mx-auto"><?= htmlspecialchars($doc['rejection_reason']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="material-symbols-outlined text-xs">schedule</span> In Review
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="<?= asset($doc['file_path']) ?>" target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                            <span>View</span>
                                        </a>

                                        <?php if ($doc['status'] !== 'verified'): ?>
                                            <form method="POST" action="<?= url('owner/kyc/delete') ?>" onsubmit="return confirm('Delete this unverified document?');" class="inline">
                                                <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Delete">
                                                    <span class="material-symbols-outlined text-base">delete</span>
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

<!-- Upload KYC Document Modal -->
<div id="uploadKycModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">upload_file</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Upload Legal Document</h3>
                    <p class="text-xs text-slate-500">Government ID, Title Deed, or Electricity Bill</p>
                </div>
            </div>
            <button onclick="document.getElementById('uploadKycModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <form action="<?= url('owner/kyc') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document Type <span class="text-rose-500">*</span></label>
                <select name="document_type" required class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-sky-500 focus:outline-hidden">
                    <option value="">-- Choose Document Type --</option>
                    <option value="property_registry">Property Registry / Sale Deed / 7/12</option>
                    <option value="electricity_bill">Electricity / Utility Bill (Last 3 Months)</option>
                    <option value="pan">PAN Card (Host / Entity)</option>
                    <option value="aadhaar">Aadhaar Card (Front &amp; Back)</option>
                    <option value="fssai_license">Trade License / FSSAI / Tourism Permit</option>
                    <option value="other">Other Supporting Legal Instrument</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Property Association</label>
                <select name="farmhouse_id" class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-sky-500 focus:outline-hidden">
                    <option value="">Host Identity (Applies to all my properties)</option>
                    <?php foreach ($farmhouses as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['title']) ?> (<?= htmlspecialchars($f['location']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document Identifier / Number</label>
                <input type="text" name="document_number" placeholder="e.g. Registration No, PAN No, Consumer No"
                       class="w-full text-xs font-semibold rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:border-sky-500 focus:outline-hidden">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Upload Scanned File <span class="text-rose-500">*</span></label>
                <div class="border-2 border-dashed border-slate-300 hover:border-sky-400 rounded-2xl p-5 text-center bg-slate-50 transition cursor-pointer relative">
                    <input type="file" name="document_file" required accept=".jpg,.jpeg,.png,.pdf,.webp"
                           class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                           onchange="document.getElementById('fileNameLabel').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                    <span class="material-symbols-outlined text-3xl text-slate-400">cloud_upload</span>
                    <p class="text-xs font-bold text-slate-700 mt-1" id="fileNameLabel">Choose PDF, JPG, or PNG</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Maximum file size: 10MB</p>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('uploadKycModal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition">
                    Submit for Verification
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../Includes/owner_footer.php'; ?>
