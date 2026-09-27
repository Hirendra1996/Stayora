<?php include __DIR__ . "/../Includes/admin_header.php"; ?>

<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full flex flex-col flex-1 pb-24 md:pb-8 overflow-x-hidden">
    
    <!-- Status Messages -->
    <?php if (isset($message)): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="font-bold text-sm"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-red-600">error</span>
            <p class="font-bold text-sm"><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="mb-6 sm:mb-8 text-center sm:text-left">
        <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-on-surface mb-2 sm:mb-3">
            My Profile & Platform
        </h1>
        <p class="font-body text-sm sm:text-base text-on-surface-variant max-w-2xl leading-relaxed mx-auto sm:mx-0">
            Manage your personal account, platform identities, and contact details used publicly across the website.
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 flex-1 items-start w-full relative">
        
        <!-- Left Column: Forms -->
        <div class="w-full lg:w-[62%] xl:w-[66%] space-y-5 lg:space-y-8 flex flex-col flex-1 order-2 lg:order-1">
            
            <!-- 1. Basic Information (Admin Account Settings) -->
            <form method="POST" action="" class="bg-surface-container-lowest border border-outline-variant/30 shadow-sm rounded-2xl p-4 sm:p-6 lg:p-8 relative overflow-hidden group">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary bg-primary-container/40 p-2 rounded-xl text-[20px] sm:text-2xl shadow-sm">person</span>
                        <h2 class="font-display text-lg sm:text-xl md:text-2xl font-bold text-on-surface tracking-tight">Admin Profile</h2>
                    </div>
                    <button type="submit" class="text-primary font-bold text-xs uppercase tracking-widest hover:underline">Update Account</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 relative z-10 w-full">
                    <div class="space-y-2 md:col-span-2">
                        <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Full Name</label>
                        <input name="name" class="w-full bg-surface-container-low border border-transparent rounded-xl px-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-primary/50 transition-all outline-none" type="text" value="<?= htmlspecialchars($adminData['name'] ?? '') ?>" required />
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Admin Email Address</label>
                        <div class="relative group/input w-full">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-primary transition-colors text-[20px]">mail</span>
                            <input name="email" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-primary/50 transition-all outline-none" type="email" value="<?= htmlspecialchars($adminData['email'] ?? '') ?>" required />
                        </div>
                    </div>
                </div>
            </form>

            <!-- 2. Site Configuration (Site Settings Table mapped from DB) -->
            <!-- * enctype="multipart/form-data" added natively if you do implement logos in future * -->
            <form method="POST" action="" enctype="multipart/form-data" class="bg-surface-container-lowest border border-outline-variant/30 shadow-sm rounded-2xl p-4 sm:p-6 lg:p-8 relative overflow-hidden group">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 sm:mb-8 relative z-10 gap-3 border-b border-outline-variant/30 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-tertiary bg-tertiary-container/30 p-2 rounded-xl text-[20px] sm:text-2xl shadow-sm">public</span>
                        <h2 class="font-display text-lg sm:text-xl md:text-2xl font-bold text-on-surface tracking-tight">Platform Configuration</h2>
                    </div>
                    <button type="submit" class="bg-tertiary text-on-tertiary px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-md hover:bg-tertiary/90 transition-colors">
                        Save Site Details
                    </button>
                </div>

                <div class="space-y-6 sm:space-y-8 relative z-10 w-full">
                    
                    <!-- Basic Setup -->
                    <div>
                        <h3 class="font-display font-semibold text-base sm:text-lg text-on-surface mb-4">Core Essentials</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Website Name</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-tertiary transition-colors text-[20px]">web</span>
                                    <input name="site_name" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-tertiary/50 transition-all outline-none" type="text" value="<?= htmlspecialchars($siteSettings['site_name'] ?? '') ?>" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Public Display Email</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-tertiary transition-colors text-[20px]">mark_email_read</span>
                                    <input name="setting_email" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-tertiary/50 transition-all outline-none" type="email" value="<?= htmlspecialchars($siteSettings['email'] ?? '') ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Phones & Links -->
                    <div>
                        <h3 class="font-display font-semibold text-base sm:text-lg text-on-surface mb-4">Contact Numbers</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Support Call Line</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-tertiary transition-colors text-[20px]">call</span>
                                    <input name="mobile_number" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-tertiary/50 transition-all outline-none" type="tel" value="<?= htmlspecialchars($siteSettings['mobile_number'] ?? '') ?>" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">WhatsApp Agent</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-[#25D366] transition-colors text-[20px]">chat</span>
                                    <input name="whatsapp_number" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-[#25D366]/50 transition-all outline-none" type="tel" value="<?= htmlspecialchars($siteSettings['whatsapp_number'] ?? '') ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Network URLs -->
                    <div>
                        <h3 class="font-display font-semibold text-base sm:text-lg text-on-surface mb-4">Social Integration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Facebook URL</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-blue-500 transition-colors text-[20px]">thumb_up</span>
                                    <input name="facebook_link" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-blue-500/50 transition-all outline-none" type="url" value="<?= htmlspecialchars($siteSettings['facebook_link'] ?? '') ?>" placeholder="https://facebook.com/yourpage" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Instagram URL</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within/input:text-pink-500 transition-colors text-[20px]">photo_camera</span>
                                    <input name="instagram_link" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-pink-500/50 transition-all outline-none" type="url" value="<?= htmlspecialchars($siteSettings['instagram_link'] ?? '') ?>" placeholder="https://instagram.com/yourprofile" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Geographical Location & Content Text -->
                    <div>
                        <h3 class="font-display font-semibold text-base sm:text-lg text-on-surface mb-4">Content & Localization</h3>
                        <div class="grid grid-cols-1 gap-4 sm:gap-5">
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Full Location Address</label>
                                <div class="relative group/input">
                                    <textarea name="address" rows="3" class="w-full bg-surface-container-low border border-transparent rounded-xl px-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-tertiary/50 transition-all outline-none resize-none" placeholder="Enter standard physical address for your contact page"><?= htmlspecialchars($siteSettings['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Google Maps Embedded Link (Iframe Src)</label>
                                <div class="relative group/input">
                                    <span class="material-symbols-outlined absolute left-4 top-4 text-on-surface-variant group-focus-within/input:text-tertiary transition-colors text-[20px]">map</span>
                                    <input name="google_map_link" class="w-full bg-surface-container-low border border-transparent rounded-xl pl-12 pr-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-tertiary/50 transition-all outline-none" type="text" value="<?= htmlspecialchars($siteSettings['google_map_link'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?..." />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Website Footer Disclaimer / Text</label>
                                <div class="relative group/input">
                                    <textarea name="footer_text" rows="3" class="w-full bg-surface-container-low border border-transparent rounded-xl px-4 py-3 sm:py-3.5 text-on-surface font-body font-medium hover:bg-surface-container-high focus:bg-surface-container-lowest focus:border-tertiary/50 transition-all outline-none resize-y" placeholder="© Copyright 2024 YourBrand. All Rights Reserved."><?= htmlspecialchars($siteSettings['footer_text'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <!-- 3. Security / Password Card -->
            <form method="POST" action="" class="bg-surface-container-lowest border border-outline-variant/30 shadow-sm rounded-2xl p-4 sm:p-6 lg:p-8 relative overflow-hidden group">
                <input type="hidden" name="action" value="change_password">
                
                <div class="flex items-center gap-3 mb-5 sm:mb-6 relative z-10">
                    <span class="material-symbols-outlined text-error bg-error-container/40 p-2 rounded-xl text-[20px] sm:text-2xl shadow-sm">lock</span>
                    <h2 class="font-display text-lg sm:text-xl md:text-2xl font-bold text-on-surface tracking-tight">Security Controls</h2>
                </div>

                <div class="space-y-5 sm:space-y-6 relative z-10 w-full">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 w-full">
                        <div class="space-y-2 w-full md:pr-4">
                            <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Current Password</label>
                            <input name="current_password" class="w-full bg-surface-container-low border border-transparent rounded-xl px-4 py-3 text-on-surface focus:border-error/50 outline-none" type="password" required />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 border-t border-outline-variant/20 pt-5">
                        <div class="space-y-2">
                            <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">New Password</label>
                            <input name="new_password" id="new_pw" class="w-full bg-surface-container-low border border-transparent rounded-xl px-4 py-3 text-on-surface focus:border-error/50 outline-none" type="password" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block font-label text-[11px] font-black uppercase tracking-widest text-on-surface-variant ml-1">Confirm New Password</label>
                            <input id="confirm_pw" class="w-full bg-surface-container-low border border-transparent rounded-xl px-4 py-3 text-on-surface focus:border-error/50 outline-none" type="password" required />
                        </div>
                    </div>

                    <div class="flex justify-start sm:justify-end pt-2">
                        <button type="submit" onclick="return validatePassword()" class="w-full sm:w-auto bg-surface-variant text-on-surface font-bold py-3.5 px-8 rounded-xl hover:bg-[#d8d9c2] transition-all shadow-sm hover:shadow-md">
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
            
        </div>

        <!-- Right Column: Account Quick Overview Summary -->
        <div class="w-full lg:w-[38%] xl:w-[34%] shrink-0 space-y-6 flex flex-col order-1 lg:order-2 h-auto lg:sticky lg:top-24 relative">
            <div class="bg-surface-container-lowest border border-outline-variant/30 shadow-sm rounded-2xl p-6 flex flex-col items-center text-center">
                <div class="relative mb-5 group inline-block mt-2">
                    <img class="w-28 h-28 sm:w-32 rounded-full object-cover shadow-xl border-4 border-surface-container-lowest" src="https://ui-avatars.com/api/?name=<?= urlencode($adminData['name'] ?? 'A') ?>&background=E7E9C8&color=1B1D0E&size=200" alt="Admin Profile" />
                </div>
                <h3 class="font-display text-xl sm:text-2xl font-bold text-on-surface tracking-tight mb-1">
                    <?= htmlspecialchars($adminData['name'] ?? 'Administrator') ?>
                </h3>
                <div class="flex items-center gap-1.5 bg-surface-variant/40 px-3.5 py-1.5 rounded-full mb-4">
                    <span class="w-2 h-2 rounded-full bg-primary relative"><span class="absolute inset-0 animate-ping bg-primary opacity-60 rounded-full"></span></span>
                    <p class="text-on-surface-variant text-[11px] font-bold uppercase tracking-widest mt-[1px]">Super Administrator</p>
                </div>
                
                <div class="w-full border-t border-outline-variant/30 pt-4 pb-2 mt-2 space-y-3 text-xs">
                    <!-- Status list summary -->
                    <div class="flex items-center justify-between text-on-surface">
                        <span class="text-on-surface-variant font-bold">Main Site Linked</span>
                        <span class="font-semibold text-tertiary flex items-center gap-1">
                            <?= htmlspecialchars($siteSettings['site_name'] ?? 'Not Configured') ?>
                            <span class="material-symbols-outlined text-[14px]">public</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface">
                        <span class="text-on-surface-variant font-bold">Public Email Linked</span>
                        <span class="font-semibold text-tertiary">
                            <?= $siteSettings['email'] ? 'Active' : 'Missing' ?>
                        </span>
                    </div>
                </div>

                <div class="w-full border-t border-outline-variant/30 pt-4 mt-2 text-xs text-on-surface-variant/70">
                    <p>Member since: <?= isset($adminData['created_at']) ? date('M Y', strtotime($adminData['created_at'])) : 'N/A' ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validatePassword() {
    const pw = document.getElementById('new_pw').value;
    const confirm = document.getElementById('confirm_pw').value;
    if (pw !== confirm) {
        alert("New passwords do not match!");
        return false;
    }
    return true;
}
</script>

</main>
<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>