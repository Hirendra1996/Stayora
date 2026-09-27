<?php
$pageTitle  = "System Settings";
$activePage = "settings";

include __DIR__ . "/../Includes/admin_header.php";

$siteName    = htmlspecialchars($settings['site_name'] ?? 'FarmLelo');
$tagline     = htmlspecialchars($settings['tagline'] ?? 'Luxury Farmhouses & Private Villa Escapes');
$phone       = htmlspecialchars($settings['mobile_number'] ?? '');
$whatsapp    = htmlspecialchars($settings['whatsapp_number'] ?? '');
$email       = htmlspecialchars($settings['email'] ?? '');
$address     = htmlspecialchars($settings['address'] ?? '');
$gmap        = htmlspecialchars($settings['google_map_link'] ?? '');
$fb          = htmlspecialchars($settings['facebook_link'] ?? '');
$insta       = htmlspecialchars($settings['instagram_link'] ?? '');
$yt          = htmlspecialchars($settings['youtube_link'] ?? '');
$tw          = htmlspecialchars($settings['twitter_link'] ?? '');
$upiId       = htmlspecialchars($settings['upi_id'] ?? '');
$bankName    = htmlspecialchars($settings['bank_name'] ?? '');
$accName     = htmlspecialchars($settings['account_name'] ?? '');
$accNum      = htmlspecialchars($settings['account_number'] ?? '');
$ifsc        = htmlspecialchars($settings['ifsc_code'] ?? '');
$payNotes    = htmlspecialchars($settings['payment_instructions'] ?? '');
$footerText   = htmlspecialchars($settings['footer_text'] ?? '© ' . date('Y') . ' FarmLelo. All rights reserved.');
$metaDesc     = htmlspecialchars($settings['meta_description'] ?? '');
$metaKeywords = htmlspecialchars($settings['meta_keywords'] ?? '');
$googleVerify = htmlspecialchars($settings['google_site_verification'] ?? '');
$bingVerify   = htmlspecialchars($settings['bing_site_verification'] ?? '');

$logoUrl     = !empty($settings['logo']) 
    ? (str_starts_with($settings['logo'], 'http') ? $settings['logo'] : asset('assets/images/uploads/settings/' . $settings['logo']))
    : '';

$favUrl      = !empty($settings['favicon']) 
    ? (str_starts_with($settings['favicon'], 'http') ? $settings['favicon'] : asset('assets/images/uploads/settings/' . $settings['favicon']))
    : '';

$qrUrl       = !empty($settings['payment_qr_code']) 
    ? (str_starts_with($settings['payment_qr_code'], 'http') ? $settings['payment_qr_code'] : asset('assets/images/uploads/settings/' . $settings['payment_qr_code']))
    : '';
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

    <!-- ── 2. PAGE HEADER & ACTIONS ─────────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1 text-xs text-muted mb-1 font-semibold">
                <a href="<?= url('admin/dashboard') ?>" class="hover:text-sky transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink font-bold">System Settings</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-sky text-3xl">tune</span>
                System & Platform Settings
            </h1>
            <p class="text-muted text-sm mt-1">
                Configure brand assets, payment gateway QR codes, bank accounts, helpline contacts, and platform metadata.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-3 shrink-0">
            <button type="button" onclick="document.getElementById('settingsForm').submit()" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">save</span>
                Save All Changes
            </button>
        </div>
    </div>

    <!-- ── 3. QUICK JUMP SECTION ANCHOR TABS ─────────────────────────────────── -->
    <div class="sticky top-4 z-40 bg-white/90 backdrop-blur-md border border-border rounded-2xl p-2 shadow-sm flex items-center gap-2 overflow-x-auto">
        <a href="#sec-payment" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">qr_code_2</span> Payment & QR Code
        </a>
        <a href="#sec-brand" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">palette</span> Brand & Logos
        </a>
        <a href="#sec-contact" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">contact_phone</span> Helpline & Contacts
        </a>
        <a href="#sec-social" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">share</span> Social Channels
        </a>
        <a href="#sec-seo" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">search</span> SEO & Footer
        </a>
    </div>

    <!-- ── 4. MAIN FORM ──────────────────────────────────────────────────────── -->
    <form id="settingsForm" method="POST" action="<?= url('admin/settings') ?>" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="save_settings">

        <!-- ── SECTION A: PAYMENT QR CODE & BANK GATEWAY ── -->
        <div id="sec-payment" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold">
                    <span class="material-symbols-outlined text-xl">qr_code_2</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">UPI Payment Gateway & Official QR Code</h3>
                    <p class="text-xs text-muted">Upload your official UPI payment QR code and specify your UPI ID for guest reservation deposits.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Payment QR Code Uploader & Preview Card -->
                <div class="lg:col-span-5 space-y-4">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block">
                        Official UPI Payment QR Code
                    </label>

                    <div class="p-6 rounded-3xl bg-surface border border-border flex flex-col items-center justify-center text-center space-y-4">
                        <?php if (!empty($qrUrl)): ?>
                            <div class="relative group">
                                <div class="w-48 h-48 rounded-2xl bg-white p-3 border-2 border-emerald-500 shadow-md flex items-center justify-center overflow-hidden">
                                    <img src="<?= $qrUrl ?>" alt="Payment QR Code" class="w-full h-full object-contain">
                                </div>
                                <span class="absolute -top-2 -right-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                                    Active QR
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="<?= $qrUrl ?>" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-sky-xl text-ink border border-border text-xs font-bold transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-sky">zoom_in</span> Enlarge
                                </a>
                                <button type="button" onclick="submitRemoveMedia('payment_qr_code')" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">delete</span> Remove QR
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="w-36 h-36 rounded-2xl bg-stone-100 border-2 border-dashed border-border flex flex-col items-center justify-center text-muted">
                                <span class="material-symbols-outlined text-4xl text-muted/50 mb-1">qr_code_scanner</span>
                                <span class="text-[11px] font-bold">No QR Uploaded</span>
                            </div>
                        <?php endif; ?>

                        <!-- File Input -->
                        <div class="w-full">
                            <label for="payment_qr_code" class="block w-full py-2.5 px-4 bg-white hover:bg-sky-xl border border-sky/40 text-sky text-xs font-bold rounded-xl cursor-pointer text-center transition-all shadow-xs">
                                <span class="material-symbols-outlined text-sm align-middle mr-1">upload</span>
                                <?= !empty($qrUrl) ? 'Replace QR Code Image' : 'Upload QR Code Image' ?>
                            </label>
                            <input type="file" name="payment_qr_code" id="payment_qr_code" accept="image/*" class="hidden" onchange="previewSelectedImage(this, 'qr-live-preview')">
                            <p class="text-[10px] text-muted mt-1.5">PNG, JPG, WEBP — Square QR code image</p>
                        </div>
                    </div>
                </div>

                <!-- UPI & Payment Deposit Instructions -->
                <div class="lg:col-span-7 space-y-5 flex flex-col justify-between">
                    
                    <!-- Primary UPI ID -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                            Primary UPI ID / VPA <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">account_balance_wallet</span>
                            <input type="text" name="upi_id" value="<?= $upiId ?>" placeholder="e.g. farmlelo@okhdfcbank or 8889000399@ybl" 
                                   class="w-full pl-10 pr-4 py-3 bg-surface border border-border rounded-xl text-sm font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all font-mono">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">This UPI VPA will be shown alongside your QR code for manual UPI app transfers.</p>
                    </div>

                    <!-- Payment Deposit Instructions -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                            Guest Booking Deposit Instructions
                        </label>
                        <textarea name="payment_instructions" rows="4" placeholder="e.g. Please scan the QR code to pay the advance booking deposit. After payment, share the transaction screenshot or UTR number on WhatsApp (+91 8889000399) to confirm your reservation." 
                                  class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 leading-relaxed resize-none"><?= $payNotes ?></textarea>
                        <p class="text-[11px] text-muted mt-1.5">Guidance shown to guests when making booking reservation advance payments.</p>
                    </div>

                    <!-- Live Summary Pill -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                                <span class="material-symbols-outlined text-lg">verified</span>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-emerald-900 block">Instant UPI Gateway Active</span>
                                <span class="text-[11px] text-emerald-700">QR Code + UPI ID Mode Enabled</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white px-2.5 py-1 rounded-md">
                            Direct UPI
                        </span>
                    </div>

                </div>

            </div>

        </div>

        <!-- ── SECTION B: BRAND IDENTITY, LOGOS & FAVICON ── -->
        <div id="sec-brand" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky font-bold">
                    <span class="material-symbols-outlined text-xl">palette</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Brand Identity & Official Logos</h3>
                    <p class="text-xs text-muted">Upload high-resolution logos, favicons, platform naming, and punchy taglines.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Platform Name -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Platform / Site Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="site_name" value="<?= $siteName ?>" required 
                           placeholder="e.g. FarmLelo" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-sm font-extrabold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

                <!-- Tagline -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Brand Tagline
                    </label>
                    <input type="text" name="tagline" value="<?= $tagline ?>" 
                           placeholder="e.g. Luxury Farmhouses & Private Villa Escapes" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

            </div>

            <!-- Logo & Favicon Upload Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                
                <!-- Primary Logo -->
                <div class="p-5 rounded-3xl bg-surface border border-border space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-ink uppercase tracking-wider">Primary Brand Logo</span>
                        <?php if (!empty($logoUrl)): ?>
                            <button type="button" onclick="submitRemoveMedia('logo')" class="text-xs text-rose-500 font-bold hover:underline">
                                Remove Logo
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="h-28 rounded-2xl bg-white border border-border flex items-center justify-center p-3 overflow-hidden shadow-2xs">
                        <?php if (!empty($logoUrl)): ?>
                            <img src="<?= $logoUrl ?>" alt="Brand Logo" class="max-h-full max-w-full object-contain">
                        <?php else: ?>
                            <div class="flex items-center gap-2 text-muted text-xs font-semibold">
                                <span class="material-symbols-outlined text-2xl text-muted/60">image</span>
                                Default Text Logo Active
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="logo_file" class="block w-full py-2 px-3 bg-white hover:bg-sky-xl border border-sky/40 text-sky text-xs font-bold rounded-xl cursor-pointer text-center transition-all shadow-xs">
                            <span class="material-symbols-outlined text-sm align-middle mr-1">upload</span>
                            <?= !empty($logoUrl) ? 'Replace Logo' : 'Upload Logo File' ?>
                        </label>
                        <input type="file" name="logo" id="logo_file" accept="image/*" class="hidden">
                        <p class="text-[10px] text-muted text-center mt-1">PNG, SVG, WEBP with transparent background recommended</p>
                    </div>
                </div>

                <!-- Favicon -->
                <div class="p-5 rounded-3xl bg-surface border border-border space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-ink uppercase tracking-wider">Browser Tab Favicon</span>
                        <?php if (!empty($favUrl)): ?>
                            <button type="button" onclick="submitRemoveMedia('favicon')" class="text-xs text-rose-500 font-bold hover:underline">
                                Remove Favicon
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="h-28 rounded-2xl bg-white border border-border flex items-center justify-center p-3 shadow-2xs">
                        <div class="px-4 py-2 rounded-xl bg-stone-100 border border-border flex items-center gap-2 text-xs font-bold text-ink">
                            <?php if (!empty($favUrl)): ?>
                                <img src="<?= $favUrl ?>" alt="Favicon" class="w-5 h-5 object-contain">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-sky text-base">villa</span>
                            <?php endif; ?>
                            <span><?= $siteName ?> — Luxury Farmhouses</span>
                        </div>
                    </div>

                    <div>
                        <label for="favicon_file" class="block w-full py-2 px-3 bg-white hover:bg-sky-xl border border-sky/40 text-sky text-xs font-bold rounded-xl cursor-pointer text-center transition-all shadow-xs">
                            <span class="material-symbols-outlined text-sm align-middle mr-1">upload</span>
                            <?= !empty($favUrl) ? 'Replace Favicon' : 'Upload Favicon' ?>
                        </label>
                        <input type="file" name="favicon" id="favicon_file" accept="image/*,.ico" class="hidden">
                        <p class="text-[10px] text-muted text-center mt-1">ICO, PNG, SVG — 32x32 or 64x64 square</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- ── SECTION C: OFFICIAL HELPLINE & DIRECT CONTACT ── -->
        <div id="sec-contact" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold">
                    <span class="material-symbols-outlined text-xl">contact_phone</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Official Helpline & Customer Support Contacts</h3>
                    <p class="text-xs text-muted">Primary phone, WhatsApp, and email addresses displayed publicly across platform headers and footers.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Phone -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Official Support Phone
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">call</span>
                        <input type="tel" name="mobile_number" value="<?= $phone ?>" placeholder="8889000399" 
                               class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    </div>
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Official WhatsApp Support Number
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 text-lg pointer-events-none">chat</span>
                        <input type="tel" name="whatsapp_number" value="<?= $whatsapp ?>" placeholder="8889000399" 
                               class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Official Support Email Address
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">mail</span>
                        <input type="email" name="email" value="<?= $email ?>" placeholder="admin.farmlelo@gmail.com" 
                               class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    </div>
                </div>

                <!-- Google Maps Link -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Google Maps Location Link
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">pin_drop</span>
                        <input type="url" name="google_map_link" value="<?= $gmap ?>" placeholder="https://maps.google.com/..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    </div>
                </div>

                <!-- Physical Address -->
                <div class="md:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Headquarters / Office Address
                    </label>
                    <textarea name="address" rows="2" placeholder="e.g. 102 Business Park, Vijay Nagar, Indore, Madhya Pradesh" 
                              class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 resize-none"><?= $address ?></textarea>
                </div>

            </div>

        </div>

        <!-- ── SECTION D: SOCIAL CHANNELS ── -->
        <div id="sec-social" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 font-bold">
                    <span class="material-symbols-outlined text-xl">share</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Social Media & Community Channels</h3>
                    <p class="text-xs text-muted">Links to your official social network profiles.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Instagram -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Instagram URL</label>
                    <input type="url" name="instagram_link" value="<?= $insta ?>" placeholder="https://instagram.com/farmleloofficial" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

                <!-- Facebook -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Facebook Page URL</label>
                    <input type="url" name="facebook_link" value="<?= $fb ?>" placeholder="https://facebook.com/farmlelo" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

                <!-- YouTube -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">YouTube Channel URL</label>
                    <input type="url" name="youtube_link" value="<?= $yt ?>" placeholder="https://youtube.com/@farmlelo" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

                <!-- Twitter / X -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">Twitter / X URL</label>
                    <input type="url" name="twitter_link" value="<?= $tw ?>" placeholder="https://x.com/farmlelo" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

            </div>

        </div>

        <!-- ── SECTION E: SEO & SEARCH ENGINE OPTIMIZATION ── -->
        <div id="sec-seo" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 font-bold">
                    <span class="material-symbols-outlined text-xl">search</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">SEO Metadata & Search Engine Visibility</h3>
                    <p class="text-xs text-muted">Configure default metadata, webmaster verification tags, and XML sitemaps for Google, Bing, Yahoo, and Yandex.</p>
                </div>
            </div>

            <!-- XML Sitemap & Robots Quick Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-sky/5 border border-sky/20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-sky text-2xl">account_tree</span>
                        <div>
                            <div class="text-xs font-bold text-ink">Dynamic XML Sitemap</div>
                            <a href="<?= absolute_url('sitemap.xml') ?>" target="_blank" class="text-[11px] text-sky hover:underline break-all">
                                <?= absolute_url('sitemap.xml') ?>
                            </a>
                        </div>
                    </div>
                    <a href="<?= absolute_url('sitemap.xml') ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-sky text-white text-[11px] font-bold shrink-0 hover:bg-sky-d transition-colors">
                        View XML
                    </a>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600 text-2xl">smart_toy</span>
                        <div>
                            <div class="text-xs font-bold text-ink">Robots.txt Directives</div>
                            <a href="<?= absolute_url('robots.txt') ?>" target="_blank" class="text-[11px] text-emerald-600 hover:underline break-all">
                                <?= absolute_url('robots.txt') ?>
                            </a>
                        </div>
                    </div>
                    <a href="<?= absolute_url('robots.txt') ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-[11px] font-bold shrink-0 hover:bg-emerald-700 transition-colors">
                        View Robots
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                
                <!-- Meta Description -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Default Meta Description (Search Snippet ~155 characters)
                    </label>
                    <textarea name="meta_description" rows="2" placeholder="Discover and book luxury farmhouses, private pools, and lawn villas across India on FarmLelo." 
                              class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 resize-none"><?= $metaDesc ?></textarea>
                </div>

                <!-- Meta Keywords -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Default Meta Keywords (Comma separated)
                    </label>
                    <input type="text" name="meta_keywords" value="<?= $metaKeywords ?>" 
                           placeholder="luxury farmhouses, private pool villas, farm stays india, weekend getaways, indore farmhouses, surat villas" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

                <!-- Search Engine Verifications Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    
                    <!-- Google Search Console -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                            Google Search Console Verification Token
                        </label>
                        <input type="text" name="google_site_verification" value="<?= $googleVerify ?>" 
                               placeholder="e.g. gSc-AbC123XyZ789... or meta code" 
                               class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <p class="text-[10px] text-muted mt-1">Found in Google Search Console &gt; Settings &gt; Ownership Verification &gt; HTML Tag.</p>
                    </div>

                    <!-- Bing Webmaster Tools -->
                    <div>
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                            Bing Webmaster Tools Verification Token
                        </label>
                        <input type="text" name="bing_site_verification" value="<?= $bingVerify ?>" 
                               placeholder="e.g. 1234567890ABCDEF1234567890ABCDEF" 
                               class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <p class="text-[10px] text-muted mt-1">Found in Bing Webmaster Tools &gt; Add Site &gt; HTML Meta Tag.</p>
                    </div>

                </div>

                <!-- Footer Copyright -->
                <div class="pt-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Footer Copyright Notice
                    </label>
                    <input type="text" name="footer_text" value="<?= $footerText ?>" 
                           class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                </div>

            </div>

        </div>

        <!-- ── SUBMIT BAR ── -->
        <div class="p-6 rounded-3xl bg-ink text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl border border-sky/30">
            <div class="flex items-center gap-3 text-left">
                <div class="w-12 h-12 rounded-2xl bg-sky/20 border border-sky/40 flex items-center justify-center text-sky shrink-0">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white">Save Platform Configuration?</h4>
                    <p class="text-xs text-stone-300">All updated payment details, QR codes, logos, and contacts will take effect immediately.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="<?= url('admin/dashboard') ?>" class="flex-1 sm:flex-none text-center px-5 py-3 rounded-xl border border-stone-600 text-stone-300 hover:text-white hover:bg-stone-800 text-xs font-bold transition-colors">
                    Discard
                </a>
                <button type="submit" 
                        class="flex-1 sm:flex-none px-8 py-3 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/30 hover:shadow-sky/50 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Save Settings
                </button>
            </div>
        </div>

    </form>

</div>

<!-- Hidden Form for Removing Media Assets -->
<form id="removeMediaForm" method="POST" action="<?= url('admin/settings') ?>" class="hidden">
    <input type="hidden" name="action" value="remove_media">
    <input type="hidden" name="field" id="remove_media_field" value="">
</form>

<script>
    function submitRemoveMedia(fieldName) {
        if (!confirm('Are you sure you want to remove this uploaded image?')) return;
        document.getElementById('remove_media_field').value = fieldName;
        document.getElementById('removeMediaForm').submit();
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>
