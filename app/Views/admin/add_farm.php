<?php
$pageTitle  = "Add New Farmhouse";
$activePage = "properties";

include __DIR__ . "/../Includes/admin_header.php";

$selectedAmenities = $_POST['amenities'] ?? [];
$selectedRules     = $_POST['rules'] ?? [];
$isRoomBookingEnabled = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? !empty($_POST['allow_room_booking'])
    : true;
?>

<div class="space-y-8 pb-16">

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

    <!-- ── 2. BREADCRUMBS & PAGE HEADER ──────────────────────────────────────── -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1 text-xs text-muted mb-1 font-semibold">
                <a href="<?= url('admin/dashboard') ?>" class="hover:text-sky transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a href="<?= url('admin/managefarmhouses') ?>" class="hover:text-sky transition-colors">Properties</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink font-bold">Add New Estate</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight">
                Add New Farmhouse
            </h1>
            <p class="text-muted text-sm mt-1">
                Configure details, assign partner hosts, set pricing tiers, amenities, rules, and gallery media.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="<?= url('admin/managefarmhouses') ?>" 
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white hover:bg-sky-xl text-ink font-semibold text-xs border border-border shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Back to Properties
            </a>
            <button type="button" onclick="document.getElementById('add-farm-form').requestSubmit()" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/25 hover:shadow-sky/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-lg">add_home</span>
                Publish Listing
            </button>
        </div>
    </div>

    <!-- ── 3. QUICK JUMP SECTION ANCHOR TABS ─────────────────────────────────── -->
    <div class="sticky top-4 z-40 bg-white/90 backdrop-blur-md border border-border rounded-2xl p-2 shadow-sm flex items-center gap-2 overflow-x-auto">
        <a href="#sec-basic" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">info</span> Basic Info
        </a>
        <a href="#sec-location" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">location_on</span> Location & Contact
        </a>
        <a href="#sec-pricing" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">payments</span> Pricing & Capacity
        </a>
        <a href="#sec-amenities" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">checklist</span> Amenities
        </a>
        <a href="#sec-rules" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">gavel</span> Rules
        </a>
        <a href="#sec-gallery" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-ink hover:bg-sky-l hover:text-sky transition-all flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-base text-sky">photo_library</span> Gallery Media
        </a>
    </div>

    <!-- ── 4. MAIN FORM ──────────────────────────────────────────────────────── -->
    <form id="add-farm-form" method="POST" action="<?= url('admin/addfarm') ?>" enctype="multipart/form-data" class="space-y-8" novalidate onsubmit="return validateFarmForm(event)">

        <!-- ── SECTION A: BASIC INFORMATION ── -->
        <div id="sec-basic" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky font-bold">
                    <span class="material-symbols-outlined text-xl">info</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Basic Information</h3>
                    <p class="text-xs text-muted">Primary estate naming, assigned partner host, and publishing controls.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Property Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="farm_title" name="title" required maxlength="150"
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                           placeholder="e.g. Whispering Pines Luxury Estate & Pool" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                    <p class="text-[11px] text-rose-500 font-semibold mt-1 hidden" id="err-title">Property title is required (min 3 characters).</p>
                </div>

                <!-- Property Category -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Property Category / Architecture Type <span class="text-rose-500">*</span>
                    </label>
                    <select id="category" name="category" required
                            class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <?php 
                        $propertyTypeModel = new \App\Models\Admin\PropertyTypeModel();
                        $dynamicTypes = $propertyTypeModel->getActive();
                        $selectedCategory = $_POST['category'] ?? 'Farmhouse';
                        foreach ($dynamicTypes as $pt): ?>
                            <option value="<?= htmlspecialchars($pt['name']) ?>" <?= (strtolower($selectedCategory) === strtolower($pt['name'])) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pt['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Host Assignment -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted">
                            Assign Partner Host
                        </label>
                        <a href="<?= url('admin/owners') ?>" target="_blank" class="text-[11px] text-sky font-bold hover:underline">
                            + Add New Host
                        </a>
                    </div>
                    <select id="owner_id" name="owner_id" onchange="autoFillHostContact(this)" 
                            class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="" data-phone="" data-email="">— Unassigned / Platform Managed —</option>
                        <?php foreach ($owners as $owner): ?>
                            <option value="<?= $owner['id'] ?>" 
                                    data-phone="<?= htmlspecialchars($owner['phone'] ?? '') ?>"
                                    data-email="<?= htmlspecialchars($owner['email'] ?? '') ?>"
                                    <?= (($_POST['owner_id'] ?? '') == $owner['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($owner['name']) ?> (<?= htmlspecialchars($owner['phone'] ?? $owner['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Visibility Status -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Listing Visibility Status
                    </label>
                    <select name="status" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active (Published Live)</option>
                        <option value="pending" <?= (($_POST['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending Review</option>
                        <option value="rejected" <?= (($_POST['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>Paused / Rejected</option>
                    </select>
                </div>

                <!-- Admin Approval Status -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Admin Verification Status
                    </label>
                    <select name="admin_approval_status" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="approved" <?= (($_POST['admin_approval_status'] ?? 'approved') === 'approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="pending" <?= (($_POST['admin_approval_status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending Verification</option>
                        <option value="rejected" <?= (($_POST['admin_approval_status'] ?? '') === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>

                <!-- Booking Confirmation Mode (F35) -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Booking Engine Mode (F35)
                    </label>
                    <select name="booking_mode" class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-medium text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                        <option value="request" <?= (($_POST['booking_mode'] ?? 'request') === 'request') ? 'selected' : '' ?>>📝 Request to Book (Host/Admin Review)</option>
                        <option value="instant" <?= (($_POST['booking_mode'] ?? '') === 'instant') ? 'selected' : '' ?>>⚡ Instant Booking (Auto-Confirmed Directly)</option>
                    </select>
                </div>

                <!-- Rich Description Editor -->
                <div class="md:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Estate Description
                    </label>
                    
                    <!-- Formatting Toolbar -->
                    <div class="flex items-center gap-1 p-2 bg-stone-100 border border-border rounded-t-xl">
                        <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('bold')" class="p-1.5 rounded-lg hover:bg-white text-ink font-bold text-xs" title="Bold">
                            <span class="material-symbols-outlined text-[16px]">format_bold</span>
                        </button>
                        <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('italic')" class="p-1.5 rounded-lg hover:bg-white text-ink font-bold text-xs" title="Italic">
                            <span class="material-symbols-outlined text-[16px]">format_italic</span>
                        </button>
                        <span class="w-px h-4 bg-border inline-block mx-1"></span>
                        <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('insertUnorderedList')" class="p-1.5 rounded-lg hover:bg-white text-ink font-bold text-xs" title="Bullet List">
                            <span class="material-symbols-outlined text-[16px]">format_list_bulleted</span>
                        </button>
                        <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('insertOrderedList')" class="p-1.5 rounded-lg hover:bg-white text-ink font-bold text-xs" title="Numbered List">
                            <span class="material-symbols-outlined text-[16px]">format_list_numbered</span>
                        </button>
                        <span class="w-px h-4 bg-border inline-block mx-1"></span>
                        <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('removeFormat')" class="p-1.5 rounded-lg hover:bg-white text-ink font-bold text-xs" title="Clear Formatting">
                            <span class="material-symbols-outlined text-[16px]">format_clear</span>
                        </button>
                    </div>

                    <!-- Editable Content Area -->
                    <div id="desc-rich-editor" contenteditable="true" 
                         oninput="syncDescEditor()"
                         class="w-full min-h-[140px] p-4 bg-surface border border-t-0 border-border rounded-b-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 leading-relaxed font-normal"
                         placeholder="Highlight the scenic views, estate amenities, private lawn, swimming pool, and unique stay experiences..."><?= $_POST['description'] ?? '' ?></div>

                    <textarea id="farm_description" name="description" class="hidden"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <!-- Internal Notes -->
                <div class="md:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Internal Platform Notes (Private memo)
                    </label>
                    <textarea name="owner_notes" rows="2" placeholder="Private notes on property access keys, caretaker contacts, or seasonal pricing guidelines..." 
                              class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-xs text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 resize-none"><?= htmlspecialchars($_POST['owner_notes'] ?? '') ?></textarea>
                </div>

            </div>

        </div>

        <!-- ── SECTION B: LOCATION & CONTACT ── -->
        <div id="sec-location" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold">
                    <span class="material-symbols-outlined text-xl">location_on</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Location & Direct Contact</h3>
                    <p class="text-xs text-muted">Geographical address and 1-click customer inquiry contact numbers.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- City / Area -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        City / Region <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="farm_location" name="location" required 
                           value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" 
                           placeholder="e.g. Ratlam, Madhya Pradesh" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-semibold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                    <p class="text-[11px] text-rose-500 font-semibold mt-1 hidden" id="err-location">City/Location is required.</p>
                </div>

                <!-- Full Address -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Full Address / Landmark
                    </label>
                    <input type="text" name="address" 
                           value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" 
                           placeholder="e.g. Survey No. 42, Near Bypass Road, Sailana" 
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                </div>

                <!-- Google Maps Location Link -->
                <div class="md:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Google Maps Location Link / Embed
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-500 text-lg pointer-events-none">map</span>
                        <input type="text" name="google_map_link" id="farm_google_map_link"
                               value="<?= htmlspecialchars($_POST['google_map_link'] ?? '') ?>" 
                               placeholder="e.g. https://maps.app.goo.gl/... or https://www.google.com/maps?q=..." 
                               class="w-full pl-10 pr-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                    </div>
                    <p class="text-[11px] text-muted font-normal mt-1">
                        Paste the Google Maps share link, location URL, coordinates, or embed code. The corresponding interactive map will automatically appear on the property details page.
                    </p>
                </div>

                <!-- Contact Phone -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Direct Contact Phone
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted text-lg pointer-events-none">call</span>
                        <input type="tel" id="farm_contact_phone" name="contact_phone" maxlength="15"
                               value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>" 
                               placeholder="9876543210" 
                               class="w-full pl-10 pr-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                    </div>
                </div>

                <!-- WhatsApp Number -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-muted">
                            WhatsApp Connect Number
                        </label>
                        <button type="button" onclick="copyPhoneToWhatsapp()" class="text-[11px] text-sky font-bold hover:underline">
                            Copy from Phone
                        </button>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 text-lg pointer-events-none">chat</span>
                        <input type="tel" id="farm_whatsapp_number" name="whatsapp_number" maxlength="15"
                               value="<?= htmlspecialchars($_POST['whatsapp_number'] ?? '') ?>" 
                               placeholder="9876543210" 
                               class="w-full pl-10 pr-4 py-3 bg-surface border border-border rounded-xl text-sm text-ink focus:outline-none focus:ring-2 focus:ring-sky/30 transition-all">
                    </div>
                </div>

            </div>

        </div>

        <!-- ── SECTION C: PRICING & CAPACITY SIMULATOR ── -->
        <div id="sec-pricing" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Pricing Tiers & Guest Capacity</h3>
                    <p class="text-xs text-muted">Set full estate vs room-wise booking rates, bedroom specs, and guest limits.</p>
                </div>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Complete Farmhouse Price -->
                <div class="p-5 rounded-2xl bg-sky-l/30 border border-sky/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-ink flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sky text-base">villa</span>
                            Complete Farmhouse Booking Price <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] font-bold text-sky-d bg-sky-l px-2 py-0.5 rounded-md">Full Estate Exclusive</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-ink text-base">₹</span>
                        <input type="number" id="farm_price" name="price" required min="0" step="0.01"
                               value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" 
                               placeholder="15000" 
                               oninput="updatePricePreview(this.value)"
                               class="w-full pl-9 pr-4 py-3 bg-white border border-border rounded-xl text-base font-black text-ink focus:outline-none focus:ring-2 focus:ring-sky/40 shadow-2xs">
                    </div>
                    <p class="text-[11px] text-muted font-medium">Nightly rate when a guest reserves the entire property exclusively.</p>
                    <p class="text-[11px] text-rose-500 font-semibold hidden" id="err-price">Enter a valid full farmhouse price (> 0).</p>
                </div>

                <!-- Pricing Info Banner -->
                <div class="p-5 rounded-2xl bg-surface border border-border flex flex-col justify-between space-y-3">
                    <div class="flex items-center gap-2 text-indigo-700 font-extrabold text-xs uppercase tracking-wider">
                        <span class="material-symbols-outlined text-base">hotel</span>
                        <span>Dual Booking Mode Architecture</span>
                    </div>
                    <p class="text-xs text-muted leading-relaxed">
                        FarmLelo allows guests to book either the <strong>Complete Farmhouse</strong> or <strong>Specific Room Types</strong> (e.g. Standard, Deluxe, Suite). Configure the room inventory and pricing tiers below.
                    </p>
                    <div class="pt-2 border-t border-border/60 flex items-center justify-between text-xs font-bold text-ink">
                        <span>Room Booking Status</span>
                        <span id="add-room-booking-status-badge" class="<?= $isRoomBookingEnabled ? 'text-sky-d bg-sky-l' : 'text-slate-600 bg-slate-100 border border-slate-200' ?> px-2.5 py-1 rounded-lg text-[11px] font-bold">
                            <?= $isRoomBookingEnabled ? 'Room-Wise Active' : 'Disabled (Full Farmhouse Only)' ?>
                        </span>
                    </div>
                </div>

            </div>

            <!-- ── ROOM-WISE INVENTORY & PRICING BUILDER ── -->
            <div class="p-5 sm:p-6 rounded-2xl bg-indigo-50/30 border border-indigo-200/80 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-indigo-200/60">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-700 text-lg">meeting_room</span>
                            <h4 class="text-sm font-extrabold text-ink">Room Types, Inventory &amp; Room-Wise Pricing</h4>
                        </div>
                        <p class="text-xs text-muted mt-0.5">
                            Create and manage multiple room types (e.g. Standard Room, Deluxe Room), set available room count and price per room.
                        </p>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none bg-white px-3.5 py-2 rounded-xl border border-indigo-200 shadow-2xs hover:border-indigo-400 transition-all">
                        <input type="checkbox" id="allow_room_booking" name="allow_room_booking" value="1"
                               onchange="toggleRoomWiseBooking(this.checked)"
                               class="w-4 h-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500/30 cursor-pointer"
                               <?= $isRoomBookingEnabled ? 'checked' : '' ?>>
                        <span class="text-xs font-bold text-ink">Enable Room Booking</span>
                    </label>
                </div>

                <!-- Hidden fallback for legacy room_price -->
                <input type="hidden" id="farm_room_price" name="room_price" value="<?= htmlspecialchars($_POST['room_price'] ?? '') ?>">

                <!-- Dynamic Room Types Wrapper -->
                <div id="room-types-wrapper" class="<?= $isRoomBookingEnabled ? '' : 'hidden' ?> space-y-4">
                    
                    <!-- Live Inventory Summary Banner -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-white border border-indigo-100 rounded-xl text-xs shadow-2xs">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 font-bold text-ink bg-surface px-2.5 py-1 rounded-lg border border-border">
                                <span class="material-symbols-outlined text-indigo-600 text-sm">bedroom_parent</span>
                                <span id="rt-summary-types">0</span> Room Types
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                <span class="material-symbols-outlined text-emerald-600 text-sm">inventory_2</span>
                                <span id="rt-summary-rooms">0</span> Total Rooms
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-lg border border-violet-200">
                                <span class="material-symbols-outlined text-violet-600 text-sm">groups</span>
                                <span id="rt-summary-guests">0</span> Max Guests
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200">
                                <span class="material-symbols-outlined text-sky-600 text-sm">payments</span>
                                Starting <span id="rt-summary-min-price">₹0</span> / room
                            </span>
                        </div>
                        <button type="button" onclick="addRoomTypeRow()" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined text-base">add_circle</span> Add Room Type
                        </button>
                    </div>

                    <!-- Room Types Rows List -->
                    <div id="room-types-container" class="space-y-3">
                        <!-- Javascript dynamically mounts room rows here -->
                    </div>

                    <p class="text-[11px] text-muted flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-indigo-500 text-sm">info</span>
                        <span>Each room type maintains its own availability count and price. Bookings will update room availability independently.</span>
                    </p>
                </div>

                <div id="room-types-disabled-msg" class="<?= $isRoomBookingEnabled ? 'hidden' : '' ?> text-xs text-muted p-4 bg-white/80 border border-indigo-100 rounded-xl text-center">
                    Room-wise booking is disabled for this property. Check "Enable Room Booking" above to configure room inventory.
                </div>
            </div>

            <!-- Bedrooms & Capacity Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                
                <!-- Bedrooms Count -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Bedrooms (BHK Count) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="farm_bedrooms" name="bedrooms" min="1" max="50" required
                               value="<?= htmlspecialchars($_POST['bedrooms'] ?? '2') ?>" 
                               oninput="calculateTotalBedCapacity()"
                               class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    </div>
                </div>

                <!-- Capacity Per Bedroom -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Guests Per Bedroom <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="farm_bedroom_capacity" name="bedroom_capacity" min="1" max="20" required
                               value="<?= htmlspecialchars($_POST['bedroom_capacity'] ?? '2') ?>" 
                               oninput="calculateTotalBedCapacity()"
                               placeholder="2"
                               class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    </div>
                    <p class="text-[10px] text-muted mt-1" id="bed_capacity_calc_hint">2 beds × 2 = 4 standard guests</p>
                </div>

                <!-- Total Night Capacity -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Max Overnight Guests
                    </label>
                    <input type="number" id="farm_night_capacity" name="night_capacity" min="1"
                           value="<?= htmlspecialchars($_POST['night_capacity'] ?? '10') ?>" 
                           placeholder="10"
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <p class="text-[10px] text-muted mt-1">Total overnight capacity</p>
                </div>

                <!-- Total Day Capacity -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-1">
                        Max Day Gathering (Event)
                    </label>
                    <input type="number" id="farm_day_capacity" name="day_capacity" min="1"
                           value="<?= htmlspecialchars($_POST['day_capacity'] ?? '25') ?>" 
                           placeholder="25"
                           class="w-full px-4 py-3 bg-surface border border-border rounded-xl text-sm font-bold text-ink focus:outline-none focus:ring-2 focus:ring-sky/30">
                    <p class="text-[10px] text-muted mt-1">Day gathering & lawn events</p>
                </div>

            </div>

            <!-- Negotiable switch + Live Preview Card -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-surface border border-border">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" id="is_negotiable" name="is_negotiable" class="w-5 h-5 rounded-lg border-border text-sky focus:ring-sky/30 cursor-pointer"
                           <?= !empty($_POST['is_negotiable']) ? 'checked' : '' ?>>
                    <div>
                        <span class="text-sm font-bold text-ink block">Price is Open to Negotiation</span>
                        <span class="text-xs text-muted">Displays a 'Negotiable' badge on public estate listings.</span>
                    </div>
                </label>

                <div class="flex items-center gap-3 bg-white px-5 py-2.5 rounded-xl border border-sky/30 shadow-xs">
                    <span class="text-xs font-bold text-muted uppercase tracking-wider">Full Farmhouse Rate:</span>
                    <span id="price_display_pill" class="text-xl font-black text-sky">₹0 <span class="text-xs font-normal text-muted">/ night</span></span>
                </div>
            </div>

        </div>

        <!-- ── SECTION D: AMENITIES ── -->
        <div id="sec-amenities" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 font-bold">
                    <span class="material-symbols-outlined text-xl">checklist</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Equipped Amenities</h3>
                    <p class="text-xs text-muted">Check all amenities available at this farmhouse estate.</p>
                </div>
            </div>

            <?php
            $groupedAmenities = [];
            foreach ($availableAmenities as $am) {
                $groupedAmenities[$am['category']][] = $am;
            }
            $catLabels = [
                'general'  => 'General & Living',
                'kitchen'  => 'Kitchen & Dining',
                'bedroom'  => 'Bedrooms & Bath',
                'outdoor'  => 'Outdoor & Recreation',
                'other'    => 'Additional Features'
            ];
            ?>

            <div class="space-y-6">
                <?php foreach ($groupedAmenities as $cat => $items): ?>
                <div>
                    <div class="flex items-center justify-between mb-3 pb-1 border-b border-stone-100">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-muted flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-sky"></span>
                            <?= $catLabels[$cat] ?? ucfirst($cat) ?> (<?= count($items) ?>)
                        </span>
                        <div class="flex gap-2">
                            <button type="button" onclick="toggleCatAmenities('cat_<?= $cat ?>', true)" class="text-[11px] font-bold text-sky hover:underline">Select All</button>
                            <span class="text-muted text-[11px]">•</span>
                            <button type="button" onclick="toggleCatAmenities('cat_<?= $cat ?>', false)" class="text-[11px] font-bold text-muted hover:text-ink">Clear</button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2.5 cat_<?= $cat ?>">
                        <?php foreach ($items as $am): 
                            $isChecked = in_array($am['id'], $selectedAmenities);
                        ?>
                        <label class="amenity-chip-label inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-semibold cursor-pointer transition-all select-none
                               <?= $isChecked ? 'bg-sky-l border-sky text-sky-d font-bold shadow-2xs' : 'bg-surface border-border text-ink hover:border-sky/40' ?>">
                            <input type="checkbox" name="amenities[]" value="<?= $am['id'] ?>" class="hidden" onchange="onAmenityChipToggle(this)" <?= $isChecked ? 'checked' : '' ?>>
                            <span class="material-symbols-outlined text-[17px] <?= $isChecked ? 'text-sky' : 'text-muted' ?>">
                                <?= htmlspecialchars($am['icon_class'] ?? 'check') ?>
                            </span>
                            <?= htmlspecialchars($am['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- ── SECTION E: HOUSE RULES ── -->
        <div id="sec-rules" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 font-bold">
                    <span class="material-symbols-outlined text-xl">gavel</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Property & Stay Rules</h3>
                    <p class="text-xs text-muted">Specify policies for guests (e.g. pets, smoking, loud music, parties).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($allRulePresets as $preset): 
                    $pId = $preset['id'];
                    $isAllowed = isset($selectedRules[$pId]['is_allowed']) ? (int)$selectedRules[$pId]['is_allowed'] : 1;
                ?>
                <div class="p-4 rounded-2xl bg-surface border border-border flex items-center justify-between gap-3 hover:border-sky/40 transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="material-symbols-outlined text-sky text-xl shrink-0">
                            <?= htmlspecialchars($preset['icon_class'] ?? 'rule') ?>
                        </span>
                        <span class="font-bold text-xs text-ink truncate"><?= htmlspecialchars($preset['rule_name']) ?></span>
                    </div>

                    <div class="shrink-0">
                        <input type="hidden" name="rules[<?= $pId ?>][rule_name]" value="<?= htmlspecialchars($preset['rule_name']) ?>">
                        <input type="hidden" id="rule_input_<?= $pId ?>" name="rules[<?= $pId ?>][is_allowed]" value="<?= $isAllowed ?>">
                        
                        <button type="button" id="rule_btn_<?= $pId ?>" onclick="toggleRuleState(<?= $pId ?>)" 
                                class="px-3 py-1 rounded-full text-[11px] font-extrabold transition-all <?= $isAllowed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                            <?= $isAllowed ? '✓ Allowed' : '✕ Not Allowed' ?>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- ── SECTION F: GALLERY MEDIA ── -->
        <div id="sec-gallery" class="bg-white rounded-3xl border border-border p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
            
            <div class="flex items-center gap-3 pb-4 border-b border-border">
                <div class="w-10 h-10 rounded-xl bg-sky-l border border-sky/20 flex items-center justify-center text-sky font-bold">
                    <span class="material-symbols-outlined text-xl">photo_library</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-ink">Media & Photo Gallery</h3>
                    <p class="text-xs text-muted">Upload high-resolution property cover and interior/lawn photographs.</p>
                </div>
            </div>

            <!-- Drag and Drop Box -->
            <label for="farm_images" id="drop-zone"
                   class="block border-2 border-dashed border-sky/40 hover:border-sky bg-sky-l/20 hover:bg-sky-l/40 rounded-3xl p-8 sm:p-12 text-center cursor-pointer transition-all group">
                <div class="w-16 h-16 rounded-2xl bg-white border border-sky/30 text-sky flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform shadow-xs">
                    <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                </div>
                <h4 class="text-sm font-bold text-ink">Click to Browse or Drag & Drop Photos</h4>
                <p class="text-xs text-muted mt-1">Supports JPG, PNG, WEBP — Recommended 1200x800 resolution</p>
                <input type="file" id="farm_images" name="images[]" multiple accept="image/*" class="hidden" onchange="handleNewImages(this.files)">
            </label>

            <!-- Preview Grid -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-ink uppercase tracking-wider">Uploaded Photos Queue (<span id="queue_count">0</span>)</span>
                    <button type="button" onclick="clearImageQueue()" class="text-xs text-rose-500 font-bold hover:underline hidden" id="clear_queue_btn">Clear All</button>
                </div>
                <div id="image_preview_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <p class="text-xs text-muted col-span-full py-4 text-center">No photos selected yet.</p>
                </div>
            </div>

        </div>

        <!-- ── SUBMIT BAR ── -->
        <div class="p-6 rounded-3xl bg-ink text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl border border-sky/30">
            <div class="flex items-center gap-3 text-left">
                <div class="w-12 h-12 rounded-2xl bg-sky/20 border border-sky/40 flex items-center justify-center text-sky shrink-0">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white">Ready to Publish Estate?</h4>
                    <p class="text-xs text-stone-300">All configurations, pricing tiers, and photos will be saved instantly.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="<?= url('admin/managefarmhouses') ?>" class="flex-1 sm:flex-none text-center px-5 py-3 rounded-xl border border-stone-600 text-stone-300 hover:text-white hover:bg-stone-800 text-xs font-bold transition-colors">
                    Discard
                </a>
                <button type="submit" 
                        class="flex-1 sm:flex-none px-8 py-3 rounded-xl bg-gradient-to-r from-sky to-sky-d text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-sky/30 hover:shadow-sky/50 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">add_home</span>
                    Publish Farmhouse
                </button>
            </div>
        </div>

    </form>

</div>

<!-- ── JAVASCRIPT LOGIC ── -->
<script>
    // Rich Text Formatting
    function formatDesc(cmd) {
        document.execCommand(cmd, false, null);
        syncDescEditor();
    }

    function syncDescEditor() {
        const rich = document.getElementById('desc-rich-editor');
        const hidden = document.getElementById('farm_description');
        if (rich && hidden) {
            hidden.value = rich.innerHTML;
        }
    }

    // Auto-fill Contact from Selected Host
    function autoFillHostContact(selectEl) {
        const selected = selectEl.options[selectEl.selectedIndex];
        const phone = selected.getAttribute('data-phone') || '';
        
        const phoneInput = document.getElementById('farm_contact_phone');
        const waInput = document.getElementById('farm_whatsapp_number');

        if (phone && (!phoneInput.value || phoneInput.value === '')) {
            phoneInput.value = phone;
        }
        if (phone && (!waInput.value || waInput.value === '')) {
            waInput.value = phone;
        }
    }

    function copyPhoneToWhatsapp() {
        const phoneVal = document.getElementById('farm_contact_phone').value;
        if (phoneVal) {
            document.getElementById('farm_whatsapp_number').value = phoneVal;
        }
    }

    // Live Price Formatter
    function updatePricePreview(val) {
        const pill = document.getElementById('price_display_pill');
        const num = parseFloat(val) || 0;
        pill.innerHTML = `₹${num.toLocaleString('en-IN')} <span class="text-xs font-normal text-muted">/ night</span>`;
    }

    // ── ROOM TYPES DYNAMIC BUILDER ──
    let roomTypeIndex = 0;
    const initialRoomTypes = <?= json_encode($_POST['room_types'] ?? []) ?>;

    function toggleRoomWiseBooking(enabled) {
        const wrapper = document.getElementById('room-types-wrapper');
        const msg = document.getElementById('room-types-disabled-msg');
        const allowInput = document.getElementById('allow_room_booking');
        const badge = document.getElementById('room-booking-status-badge');
        
        if (enabled) {
            if (wrapper) {
                wrapper.classList.remove('hidden');
                wrapper.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);
            }
            if (msg) msg.classList.add('hidden');
            if (allowInput) allowInput.checked = true;
            if (badge) {
                badge.className = 'text-sky-d bg-sky-l px-2.5 py-1 rounded-lg text-[11px] font-bold';
                badge.textContent = 'Room-Wise Active';
            }
            const container = document.getElementById('room-types-container');
            if (container && container.children.length === 0) {
                addRoomTypeRow({ room_type_name: 'Standard Room', total_rooms: 2, price_per_room: 2000, capacity_per_room: 2, description: '' });
            }
        } else {
            if (wrapper) {
                wrapper.classList.add('hidden');
                wrapper.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
            }
            if (msg) msg.classList.remove('hidden');
            if (allowInput) allowInput.checked = false;
            if (badge) {
                badge.className = 'text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg text-[11px] font-bold';
                badge.textContent = 'Disabled (Full Farmhouse Only)';
            }
        }
        updateRoomTypesSummary();
    }

    function addRoomTypeRow(data = {}) {
        const container = document.getElementById('room-types-container');
        if (!container) return;

        const idx = roomTypeIndex++;
        const id = data.id || '';
        const name = data.room_type_name || '';
        const rooms = data.total_rooms || 1;
        const price = (data.price_per_room !== undefined && data.price_per_room !== null) ? data.price_per_room : '';
        const cap = data.capacity_per_room || 2;
        const desc = data.description || '';

        const row = document.createElement('div');
        row.className = 'room-type-row p-4 rounded-2xl bg-white border border-border shadow-2xs space-y-3 transition-all hover:border-indigo-300 animate-fade-in';
        row.id = `room-type-row-${idx}`;
        row.innerHTML = `
            <input type="hidden" name="room_types[${idx}][id]" value="${escapeHtml(id)}">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <!-- Room Type Name -->
                <div class="sm:col-span-4">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-muted mb-1">
                        Room Type Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-muted text-base pointer-events-none">bedroom_parent</span>
                        <input type="text" name="room_types[${idx}][room_type_name]" value="${escapeHtml(name)}" required
                               placeholder="e.g. Deluxe Room"
                               oninput="updateRoomTypesSummary()"
                               class="w-full pl-9 pr-3 py-2 bg-surface border border-border rounded-xl text-xs font-bold text-ink focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>

                <!-- Number of Rooms Available -->
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-muted mb-1">
                        Rooms Count <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="room_types[${idx}][total_rooms]" value="${rooms}" min="1" max="100" required
                           placeholder="2"
                           oninput="updateRoomTypesSummary()"
                           class="w-full px-3 py-2 bg-surface border border-border rounded-xl text-xs font-bold text-ink text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <!-- Price Per Room -->
                <div class="sm:col-span-3">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-muted mb-1">
                        Price / Room (₹) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-ink text-xs">₹</span>
                        <input type="number" name="room_types[${idx}][price_per_room]" value="${price}" min="0" step="0.01" required
                               placeholder="2500"
                               oninput="updateRoomTypesSummary()"
                               class="w-full pl-7 pr-3 py-2 bg-surface border border-border rounded-xl text-xs font-black text-ink focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>

                <!-- Capacity per Room -->
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-muted mb-1">
                        Guests / Room <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="room_types[${idx}][capacity_per_room]" value="${cap}" min="1" max="10" required
                           placeholder="2"
                           oninput="updateRoomTypesSummary()"
                           class="w-full px-3 py-2 bg-surface border border-border rounded-xl text-xs font-bold text-ink text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <!-- Delete Action -->
                <div class="sm:col-span-1 flex items-end justify-end pt-2 sm:pt-4">
                    <button type="button" onclick="removeRoomTypeRow(${idx})" 
                            class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors" title="Delete Room Type">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>

                <!-- Features / Description -->
                <div class="sm:col-span-12">
                    <input type="text" name="room_types[${idx}][description]" value="${escapeHtml(desc)}"
                           placeholder="Optional room specs: e.g. King Bed, Pool View, Attached Western Bath, AC"
                           class="w-full px-3 py-1.5 bg-surface border border-border/70 rounded-lg text-[11px] text-muted focus:text-ink focus:outline-none focus:border-indigo-400">
                </div>
            </div>
        `;

        container.appendChild(row);
        updateRoomTypesSummary();
    }

    function removeRoomTypeRow(idx) {
        const row = document.getElementById(`room-type-row-${idx}`);
        if (row) {
            row.remove();
            updateRoomTypesSummary();
        }
    }

    function updateRoomTypesSummary() {
        const isRoomBookingOn = document.getElementById('allow_room_booking')?.checked;
        const rows = document.querySelectorAll('.room-type-row');
        let totalRooms = 0;
        let totalGuestCapacity = 0;
        let minPrice = null;
        let count = 0;

        rows.forEach(r => {
            const roomsInp = r.querySelector('input[name*="[total_rooms]"]');
            const priceInp = r.querySelector('input[name*="[price_per_room]"]');
            const capInp   = r.querySelector('input[name*="[capacity_per_room]"]');
            if (roomsInp && priceInp) {
                count++;
                const rooms = parseInt(roomsInp.value) || 0;
                const price = parseFloat(priceInp.value) || 0;
                const cap   = capInp ? (parseInt(capInp.value) || 2) : 2;
                totalRooms += rooms;
                totalGuestCapacity += (rooms * cap);
                if (price > 0 && (minPrice === null || price < minPrice)) {
                    minPrice = price;
                }
            }
        });

        const typesEl = document.getElementById('rt-summary-types');
        const roomsEl = document.getElementById('rt-summary-rooms');
        const guestsEl = document.getElementById('rt-summary-guests');
        const priceEl = document.getElementById('rt-summary-min-price');
        const hiddenRoomPrice = document.getElementById('farm_room_price');

        if (typesEl) typesEl.textContent = count;
        if (roomsEl) roomsEl.textContent = totalRooms;
        if (guestsEl) guestsEl.textContent = totalGuestCapacity;
        if (priceEl) priceEl.textContent = minPrice !== null ? `₹${minPrice.toLocaleString('en-IN')}` : '₹0';
        if (hiddenRoomPrice) hiddenRoomPrice.value = minPrice !== null ? minPrice : '';

        // Auto-sync bedrooms & capacity fields when Room-Wise Booking is enabled
        if (isRoomBookingOn && totalRooms > 0) {
            const bedInp = document.getElementById('farm_bedrooms');
            const capInp = document.getElementById('farm_bedroom_capacity');
            const nightInp = document.getElementById('farm_night_capacity');

            if (bedInp) {
                bedInp.value = totalRooms;
            }
            if (capInp) {
                const avgCap = Math.max(1, Math.round(totalGuestCapacity / totalRooms));
                capInp.value = avgCap;
            }
            if (nightInp) {
                const currentNight = parseInt(nightInp.value) || 0;
                if (currentNight < totalGuestCapacity || currentNight === 0) {
                    nightInp.value = totalGuestCapacity;
                }
            }
            calculateTotalBedCapacity();
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function calculateTotalBedCapacity() {
        const beds = parseInt(document.getElementById('farm_bedrooms')?.value) || 0;
        const cap = parseInt(document.getElementById('farm_bedroom_capacity')?.value) || 0;
        const total = beds * cap;
        const hint = document.getElementById('bed_capacity_calc_hint');
        if (hint) {
            hint.textContent = `${beds} bedroom${beds === 1 ? '' : 's'} × ${cap} = ${total} standard guests`;
        }
        const nightInp = document.getElementById('farm_night_capacity');
        if (nightInp && (!nightInp.value || parseInt(nightInp.value) === 0)) {
            nightInp.placeholder = total > 0 ? total : 10;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const pInp = document.getElementById('farm_price');
        if (pInp && pInp.value) updatePricePreview(pInp.value);
        calculateTotalBedCapacity();

        // Populate initial room types if provided in POST
        if (initialRoomTypes && Object.keys(initialRoomTypes).length > 0) {
            Object.values(initialRoomTypes).forEach(rt => addRoomTypeRow(rt));
        }

        const isRoomEnabled = document.getElementById('allow_room_booking')?.checked;
        const wrapper = document.getElementById('room-types-wrapper');
        if (!isRoomEnabled && wrapper) {
            wrapper.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
        }
    });

    // Rule State Toggle
    function toggleRuleState(pId) {
        const inp = document.getElementById('rule_input_' + pId);
        const btn = document.getElementById('rule_btn_' + pId);
        const current = parseInt(inp.value) === 1 ? 1 : 0;
        const next = current === 1 ? 0 : 1;

        inp.value = next;
        if (next === 1) {
            btn.className = 'px-3 py-1 rounded-full text-[11px] font-extrabold transition-all bg-emerald-50 text-emerald-700 border border-emerald-200';
            btn.textContent = '✓ Allowed';
        } else {
            btn.className = 'px-3 py-1 rounded-full text-[11px] font-extrabold transition-all bg-rose-50 text-rose-700 border border-rose-200';
            btn.textContent = '✕ Not Allowed';
        }
    }

    // Amenity Chip Toggle
    function onAmenityChipToggle(inp) {
        const label = inp.closest('.amenity-chip-label');
        const icon = label.querySelector('.material-symbols-outlined');
        if (inp.checked) {
            label.className = 'amenity-chip-label inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-semibold cursor-pointer transition-all select-none bg-sky-l border-sky text-sky-d font-bold shadow-2xs';
            if (icon) icon.className = 'material-symbols-outlined text-[17px] text-sky';
        } else {
            label.className = 'amenity-chip-label inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-semibold cursor-pointer transition-all select-none bg-surface border-border text-ink hover:border-sky/40';
            if (icon) icon.className = 'material-symbols-outlined text-[17px] text-muted';
        }
    }

    function toggleCatAmenities(catClass, checkAll) {
        const container = document.querySelector('.' + catClass);
        if (!container) return;
        const inputs = container.querySelectorAll('input[type="checkbox"]');
        inputs.forEach(inp => {
            inp.checked = checkAll;
            onAmenityChipToggle(inp);
        });
    }

    // Gallery Queue Handling
    let uploadedFiles = [];

    function handleNewImages(files) {
        for (let i = 0; i < files.length; i++) {
            uploadedFiles.push(files[i]);
        }
        renderImagePreviews();
    }

    function renderImagePreviews() {
        const grid = document.getElementById('image_preview_grid');
        const countSpan = document.getElementById('queue_count');
        const clearBtn = document.getElementById('clear_queue_btn');

        countSpan.textContent = uploadedFiles.length;
        if (uploadedFiles.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
            grid.innerHTML = `<p class="text-xs text-muted col-span-full py-4 text-center">No photos selected yet.</p>`;
            return;
        }

        grid.innerHTML = '';
        uploadedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const card = document.createElement('div');
                card.className = 'relative rounded-2xl overflow-hidden aspect-4/3 bg-surface border border-border shadow-2xs group';
                card.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    ${index === 0 ? '<span class="absolute bottom-2 left-2 bg-sky text-white text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md shadow-xs">Cover Photo</span>' : ''}
                    <button type="button" onclick="removeQueuedImage(${index})" class="absolute top-2 right-2 w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-md hover:bg-rose-700 transition-colors" title="Remove Photo">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                `;
                grid.appendChild(card);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeQueuedImage(index) {
        uploadedFiles.splice(index, 1);
        renderImagePreviews();
    }

    function clearImageQueue() {
        uploadedFiles = [];
        const fileInput = document.getElementById('farm_images');
        if (fileInput) fileInput.value = '';
        renderImagePreviews();
    }

    // Client-Side Validation
    function validateFarmForm(e) {
        syncDescEditor();

        let valid = true;
        const titleInp = document.getElementById('farm_title');
        const locInp   = document.getElementById('farm_location');
        const priceInp = document.getElementById('farm_price');

        // Reset error messages
        document.getElementById('err-title').classList.add('hidden');
        document.getElementById('err-location').classList.add('hidden');
        document.getElementById('err-price').classList.add('hidden');

        if (!titleInp.value.trim() || titleInp.value.trim().length < 3) {
            document.getElementById('err-title').classList.remove('hidden');
            titleInp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            titleInp.focus();
            valid = false;
        } else if (!locInp.value.trim()) {
            document.getElementById('err-location').classList.remove('hidden');
            locInp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            locInp.focus();
            valid = false;
        } else if (!priceInp.value || parseFloat(priceInp.value) <= 0) {
            document.getElementById('err-price').classList.remove('hidden');
            priceInp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            priceInp.focus();
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            return false;
        }
        return true;
    }
</script>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>