<?php
include __DIR__ . '/../Includes/owner_header.php';

$owner           = $owner           ?? [];
$success_message = $success_message ?? null;
$error_message   = $error_message   ?? null;

$profileImage = !empty($owner['profile_image'])
    ? asset('assets/images/uploads/avatars/' . htmlspecialchars($owner['profile_image']))
    : 'https://ui-avatars.com/api/?name=' . urlencode($owner['name'] ?? 'Owner') . '&background=16a5de&color=fff&bold=true&size=128';

$joinDate = !empty($owner['created_at']) ? date('d F Y', strtotime($owner['created_at'])) : 'Recent';
?>

<div class="p-4 md:p-8 max-w-[1100px] mx-auto space-y-6">

    <!-- ── Page Heading ── -->
    <div>
        <h1 class="font-headline font-black text-2xl md:text-3xl text-slate-900 leading-tight">Profile &amp; Account Settings</h1>
        <p class="text-xs text-slate-500 font-semibold mt-1">Manage your personal credentials, contact info, and workspace password.</p>
    </div>

    <!-- ── Flash Messages ── -->
    <?php if ($success_message): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 text-xs font-bold shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-500">check_circle</span>
            <span><?= htmlspecialchars($success_message) ?></span>
        </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 text-xs font-bold shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-red-500">error</span>
            <span><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 1. TOP AVATAR & HOST META BANNER                            -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs flex flex-col sm:flex-row items-center gap-6">
        <!-- Avatar with Quick Camera Trigger -->
        <div class="relative shrink-0">
            <img id="avatar-img-preview" src="<?= $profileImage ?>" alt="Profile"
                 class="w-24 h-24 md:w-28 md:h-28 rounded-3xl object-cover border-4 border-slate-100 shadow-sm">
            <label for="avatar-quick-upload"
                   class="absolute -bottom-2 -right-2 w-10 h-10 bg-primary hover:bg-primary-hover text-white rounded-2xl flex items-center justify-center cursor-pointer shadow-md shadow-primary/30 border-2 border-white transition-all active:scale-95"
                   title="Change photo">
                <span class="material-symbols-outlined text-[18px]">photo_camera</span>
            </label>
        </div>

        <!-- Name & Badges -->
        <div class="flex-1 text-center sm:text-left space-y-2">
            <h2 class="font-headline font-black text-xl md:text-2xl text-slate-900 leading-tight">
                <?= htmlspecialchars($owner['name'] ?? 'Host Partner') ?>
            </h2>
            <p class="text-slate-500 font-semibold text-xs"><?= htmlspecialchars($owner['email'] ?? '') ?></p>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 pt-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 border border-sky-200 text-primary text-xs font-extrabold uppercase tracking-wide">
                    <span class="material-symbols-outlined text-sm">verified</span>
                    <span>Verified Host Partner</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold">
                    <span class="material-symbols-outlined text-sm text-slate-400">calendar_month</span>
                    <span>Partner Since <?= $joinDate ?></span>
                </span>
            </div>
        </div>

        <!-- Phone Card -->
        <div class="hidden md:flex flex-col items-end shrink-0 pl-6 border-l border-slate-100">
            <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Primary Phone</span>
            <span class="text-base font-black text-slate-900 font-headline mt-0.5"><?= htmlspecialchars($owner['phone'] ?? '—') ?></span>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 2. TWO-COLUMN FORMS GRID                                    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ── Left: Personal Info Form ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs flex flex-col justify-between">
            <div>
                <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100 mb-6">
                    <span class="material-symbols-outlined text-primary text-lg">person</span>
                    <span>Personal &amp; Contact Info</span>
                </h2>

                <form action="<?= url('owner/profile/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-4" id="profile-form">
                    <!-- Hidden avatar file input triggered by camera badge -->
                    <input type="file" id="avatar-quick-upload" name="profile_image"
                           accept="image/jpeg,image/png,image/webp" class="hidden"
                           onchange="previewAvatar(this)">

                    <!-- Full Name -->
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Full Name *</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg">badge</span>
                            <input type="text" name="name" value="<?= htmlspecialchars($owner['name'] ?? '') ?>" required
                                   class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 transition bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Phone Number *</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg">call</span>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($owner['phone'] ?? '') ?>" maxlength="10" pattern="[0-9]{10}" required
                                   class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 transition bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <!-- Email (Read-only) -->
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Email Address (Registered)</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg">mail</span>
                            <input type="email" value="<?= htmlspecialchars($owner['email'] ?? '') ?>" readonly
                                   class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-200 outline-none text-sm font-semibold text-slate-500 bg-slate-100 cursor-not-allowed">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-semibold">Contact support if you need to update your registered email address.</p>
                    </div>
                </form>
            </div>

            <div class="pt-6">
                <button type="submit" form="profile-form"
                        class="w-full h-12 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 shadow-xs transition-all active:scale-95">
                    <span class="material-symbols-outlined text-sm">save</span>
                    <span>Save Profile Changes</span>
                </button>
            </div>
        </div>

        <!-- ── Right: Security & Password Form ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs flex flex-col justify-between">
            <div>
                <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100 mb-6">
                    <span class="material-symbols-outlined text-primary text-lg">lock</span>
                    <span>Change Workspace Password</span>
                </h2>

                <form action="<?= url('owner/profile/change-password') ?>" method="POST" class="space-y-4" id="password-form">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Current Password *</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg">key</span>
                            <input type="password" name="current_password" id="cur_pwd" placeholder="••••••••" required
                                   class="w-full h-12 pl-10 pr-10 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 transition bg-slate-50 focus:bg-white">
                            <button type="button" class="absolute right-3 text-slate-400 hover:text-primary" onclick="togglePass('cur_pwd', this)">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">New Password (Min 8 chars) *</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg">lock</span>
                            <input type="password" name="new_password" id="new_pwd" placeholder="••••••••" minlength="8" required
                                   class="w-full h-12 pl-10 pr-10 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 transition bg-slate-50 focus:bg-white">
                            <button type="button" class="absolute right-3 text-slate-400 hover:text-primary" onclick="togglePass('new_pwd', this)">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Confirm New Password *</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg">lock_reset</span>
                            <input type="password" name="confirm_password" id="cnf_pwd" placeholder="••••••••" minlength="8" required
                                   class="w-full h-12 pl-10 pr-10 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 transition bg-slate-50 focus:bg-white">
                            <button type="button" class="absolute right-3 text-slate-400 hover:text-primary" onclick="togglePass('cnf_pwd', this)">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="pt-6">
                <button type="submit" form="password-form"
                        class="w-full h-12 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 shadow-xs transition-all active:scale-95">
                    <span class="material-symbols-outlined text-sm">enhanced_encryption</span>
                    <span>Update Security Password</span>
                </button>
            </div>
        </div>

    </div>

</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-img-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
        icon.classList.add('text-primary');
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
        icon.classList.remove('text-primary');
    }
}
</script>

<?php 
include __DIR__ . '/../Includes/owner_footer.php'; 
?>
