<?php
include __DIR__ . '/../../Includes/owner_header.php';

$availableAmenities = $availableAmenities ?? [];
$allRulePresets     = $allRulePresets     ?? [];
$message            = $message            ?? null;
$error              = $error              ?? null;

// Group amenities by category
$amenitiesByCategory = [];
foreach ($availableAmenities as $a) {
    $amenitiesByCategory[$a['category'] ?? 'General'][] = $a;
}

$isRoomBookingEnabled = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? !empty($_POST['allow_room_booking'])
    : true;
?>

<div class="p-4 md:p-8 max-w-[1000px] mx-auto space-y-6">

    <!-- ── Header & Breadcrumbs ── -->
    <div class="flex items-center gap-3.5">
        <a href="<?= url('owner/farmhouses') ?>" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-primary hover:border-primary transition-all shadow-2xs text-decoration-none">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
        </a>
        <div>
            <h1 class="font-headline font-black text-2xl md:text-3xl text-slate-900 leading-tight">List a New Farmhouse</h1>
            <p class="text-xs text-slate-500 font-semibold mt-1">Submit your property details for our quality audit &amp; instant live listing.</p>
        </div>
    </div>

    <!-- ── Alerts ── -->
    <?php if ($message): ?>
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 text-xs font-bold shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-500">check_circle</span>
            <span><?= htmlspecialchars($message) ?></span>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 text-xs font-bold shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-red-500">error</span>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- ── Form ── -->
    <form action="<?= url('owner/farmhouses/add') ?>" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="syncDescEditor()">

        <!-- ── 1. Basic Info Card ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-5">
            <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                <span class="material-symbols-outlined text-primary text-lg">villa</span>
                <span>Basic Property Details</span>
            </h2>

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Property Name / Title *</label>
                <input type="text" name="title" placeholder="e.g. Royal Mirage Luxury Villa &amp; Pool" required
                       class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Property Category *</label>
                    <select name="category" required
                            class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 transition bg-slate-50 focus:bg-white">
                        <option value="Guest House">Guest House</option>
                        <option value="Resort">Resort</option>
                        <option value="Farmhouse" selected>Farmhouse</option>
                        <option value="Villa">Villa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Location / City *</label>
                    <input type="text" name="location" placeholder="e.g. Indore, Surat, Delhi NCR" required
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Price per Day / Night (₹) *</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 text-slate-400 font-bold text-sm">₹</span>
                        <input type="number" name="price" placeholder="25000" min="1" step="0.01" required
                               class="w-full h-12 pl-8 pr-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Full Address / Landmark</label>
                <input type="text" name="address" placeholder="Village / Area, Highway / Road, City, Pincode"
                       class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
            </div>

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Google Maps Location Link</label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-rose-500 text-lg pointer-events-none">map</span>
                    <input type="text" name="google_map_link" placeholder="e.g. https://maps.app.goo.gl/... or https://www.google.com/maps?q=..."
                           class="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                </div>
                <p class="text-[11px] text-slate-400 font-semibold mt-1">Paste your property's Google Maps link, coordinates, or embed link. An interactive map will be displayed on the property details page.</p>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Property Description</label>
                
                <!-- Formatting Toolbar -->
                <div class="flex items-center gap-1 p-2 bg-slate-100 border border-slate-200 rounded-t-xl">
                    <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('bold')" class="p-1.5 rounded-lg hover:bg-white text-slate-700 font-bold text-xs" title="Bold">
                        <span class="material-symbols-outlined text-[16px]">format_bold</span>
                    </button>
                    <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('italic')" class="p-1.5 rounded-lg hover:bg-white text-slate-700 font-bold text-xs" title="Italic">
                        <span class="material-symbols-outlined text-[16px]">format_italic</span>
                    </button>
                    <span class="w-px h-4 bg-slate-200 inline-block mx-1"></span>
                    <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('insertUnorderedList')" class="p-1.5 rounded-lg hover:bg-white text-slate-700 font-bold text-xs" title="Bullet List">
                        <span class="material-symbols-outlined text-[16px]">format_list_bulleted</span>
                    </button>
                    <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('insertOrderedList')" class="p-1.5 rounded-lg hover:bg-white text-slate-700 font-bold text-xs" title="Numbered List">
                        <span class="material-symbols-outlined text-[16px]">format_list_numbered</span>
                    </button>
                    <span class="w-px h-4 bg-slate-200 inline-block mx-1"></span>
                    <button type="button" onmousedown="event.preventDefault()" onclick="formatDesc('removeFormat')" class="p-1.5 rounded-lg hover:bg-white text-slate-700 font-bold text-xs" title="Clear Formatting">
                        <span class="material-symbols-outlined text-[16px]">format_clear</span>
                    </button>
                </div>

                <!-- Editable Content Area -->
                <div id="desc-rich-editor" contenteditable="true" 
                     oninput="syncDescEditor()"
                     class="w-full min-h-[140px] p-4 bg-slate-50 border border-t-0 border-slate-200 rounded-b-xl text-sm font-semibold text-slate-900 focus:outline-none focus:bg-white focus:border-primary transition leading-relaxed"
                     placeholder="Describe your farmhouse, amenities, pool dimensions, lawn space, party rules, and surroundings..."><?= $_POST['description'] ?? '' ?></div>

                <textarea id="farm_description" name="description" class="hidden"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- ── 2. Capacity & Configuration Card ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-5">
            <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                <span class="material-symbols-outlined text-primary text-lg">group</span>
                <span>Capacity &amp; Booking Terms</span>
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Bedrooms (BHK)</label>
                    <input type="number" name="bedrooms" value="3" min="0"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-bold text-slate-900 transition bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Day Guests</label>
                    <input type="number" name="day_capacity" min="1" placeholder="e.g. 50"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-bold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Night Stay Capacity</label>
                    <input type="number" name="night_capacity" min="1" placeholder="e.g. 15"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-bold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                </div>
                <div class="flex flex-col justify-end">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none px-4 rounded-xl border border-slate-200 hover:border-primary transition bg-slate-50 h-12">
                        <input type="checkbox" name="is_negotiable" class="w-4 h-4 accent-primary rounded">
                        <span class="text-xs font-extrabold text-slate-700">Price Negotiable</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- ── 2B. Room-Wise Inventory & Pricing Card ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">meeting_room</span>
                    <div>
                        <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider">
                            Room-Wise Booking &amp; Room-Wise Pricing
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Set up multiple room types (e.g. Standard Room, Deluxe Room), available room count, and price per room.
                        </p>
                    </div>
                </div>

                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none px-4 py-2 rounded-xl border border-slate-200 hover:border-primary transition bg-slate-50">
                    <input type="checkbox" id="owner_allow_room_booking" name="allow_room_booking" value="1"
                           onchange="toggleOwnerRoomBooking(this.checked)"
                           class="w-4 h-4 accent-primary rounded"
                           <?= $isRoomBookingEnabled ? 'checked' : '' ?>>
                    <span class="text-xs font-extrabold text-slate-800">Enable Room Booking</span>
                </label>
            </div>

            <!-- Hidden fallback for legacy room_price -->
            <input type="hidden" id="owner_room_price" name="room_price" value="<?= htmlspecialchars($_POST['room_price'] ?? '') ?>">

            <!-- Dynamic Room Types Container -->
            <div id="owner-room-types-wrapper" class="<?= $isRoomBookingEnabled ? '' : 'hidden' ?> space-y-4">
                
                <!-- Inventory Live Summary Bar -->
                <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-white px-3 py-1 rounded-xl border border-slate-200">
                            <span class="material-symbols-outlined text-primary text-sm">bedroom_parent</span>
                            <span id="owner-rt-types-count">0</span> Room Types
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                            <span class="material-symbols-outlined text-emerald-600 text-sm">inventory_2</span>
                            <span id="owner-rt-rooms-count">0</span> Total Rooms
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-sky-700 bg-sky-50 px-3 py-1 rounded-xl border border-sky-200">
                            <span class="material-symbols-outlined text-sky-600 text-sm">payments</span>
                            Starting <span id="owner-rt-min-price">₹0</span> / room
                        </span>
                    </div>

                    <button type="button" onclick="addOwnerRoomTypeRow()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs shadow-xs transition-all active:scale-95">
                        <span class="material-symbols-outlined text-base">add_circle</span> Add Room Type
                    </button>
                </div>

                <!-- Dynamic Rows Container -->
                <div id="owner-room-types-container" class="space-y-3">
                    <!-- Dynamic Rows added via JS -->
                </div>

                <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-primary text-sm">info</span>
                    <span>Guests booking per room can choose specific room types; room availability will update accordingly.</span>
                </p>
            </div>

            <div id="owner-room-types-disabled" class="<?= $isRoomBookingEnabled ? 'hidden' : '' ?> p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-500 text-center">
                Room-wise booking is currently disabled. Check the toggle above to configure room inventory and rates.
            </div>
        </div>

        <!-- ── 3. Contact Numbers Card ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-5">
            <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                <span class="material-symbols-outlined text-primary text-lg">call</span>
                <span>Host Contact Numbers</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Manager Calling Number</label>
                    <input type="tel" name="contact_phone" placeholder="+91 98765 43210"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">WhatsApp Lead Alerts Number</label>
                    <input type="tel" name="whatsapp_number" placeholder="+91 98765 43210"
                           class="w-full h-12 px-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white">
                </div>
            </div>
        </div>

        <!-- ── 4. Amenities Selection Card ── -->
        <?php if (!empty($amenitiesByCategory)): ?>
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-5">
                <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-primary text-lg">verified</span>
                    <span>Property Amenities</span>
                </h2>

                <?php foreach ($amenitiesByCategory as $category => $items): ?>
                    <div>
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-wider mb-3"><?= htmlspecialchars($category) ?></p>
                        <div class="flex flex-wrap gap-2.5">
                            <?php foreach ($items as $a): ?>
                                <label class="flex items-center gap-2 cursor-pointer select-none px-3.5 py-2.5 rounded-xl border border-slate-200 hover:border-primary has-[:checked]:border-primary has-[:checked]:bg-sky-50 has-[:checked]:text-primary transition-all">
                                    <input type="checkbox" name="amenities[]" value="<?= (int)$a['id'] ?>" class="w-4 h-4 accent-primary rounded">
                                    <span class="text-xs font-bold text-slate-700"><?= htmlspecialchars($a['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ── 5. House Rules Card ── -->
        <?php if (!empty($allRulePresets)): ?>
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-4">
                <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-primary text-lg">rule</span>
                    <span>House Rules &amp; Policy</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php foreach ($allRulePresets as $i => $preset): ?>
                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                            <input type="hidden" name="rules[<?= $i ?>][rule_name]" value="<?= htmlspecialchars($preset['rule_name']) ?>">
                            <span class="text-xs font-bold text-slate-800"><?= htmlspecialchars($preset['rule_name']) ?></span>
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="rules[<?= $i ?>][is_allowed]" value="1" checked class="accent-emerald-500">
                                    <span class="text-xs font-bold text-emerald-600">Allow</span>
                                </label>
                                <span class="text-slate-300 text-xs">|</span>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="rules[<?= $i ?>][is_allowed]" value="0" class="accent-rose-500">
                                    <span class="text-xs font-bold text-rose-500">Deny</span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ── 6. Photo Uploads Card ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-4">
            <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                <span class="material-symbols-outlined text-primary text-lg">photo_camera</span>
                <span>Property Photos &amp; Media</span>
            </h2>

            <label class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-300 hover:border-primary rounded-2xl py-10 cursor-pointer transition bg-slate-50/50 hover:bg-slate-50 group">
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-primary group-hover:scale-105 transition shadow-2xs">
                    <span class="material-symbols-outlined text-2xl">cloud_upload</span>
                </div>
                <div class="text-center">
                    <p class="text-sm font-black text-slate-800">Click or drag photos to upload</p>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">JPG, PNG, WebP — multiple high-res photos allowed</p>
                </div>
                <input type="file" name="images[]" accept="image/*" multiple class="hidden" id="img-input" onchange="previewImages(this)">
            </label>
            <div id="img-preview" class="grid grid-cols-3 sm:grid-cols-5 gap-3 hidden"></div>
        </div>

        <!-- ── 7. Private Notes for Admin ── -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-2xs space-y-4">
            <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-4 border-b border-slate-100">
                <span class="material-symbols-outlined text-amber-500 text-lg">edit_note</span>
                <span>Private Notes for Admin <span class="text-xs font-semibold text-slate-400 lowercase">(not visible to guests)</span></span>
            </h2>
            <textarea name="owner_notes" rows="3" placeholder="Google Drive links to more photos, landmark instructions, caretaker notes..."
                      class="w-full p-4 rounded-xl border border-slate-200 focus:border-primary focus:ring-3 focus:ring-primary/15 outline-none text-sm font-semibold text-slate-900 placeholder-slate-400 transition bg-slate-50 focus:bg-white resize-none"></textarea>
        </div>

        <!-- ── Submit & Cancel Actions Bar ── -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white font-black px-8 py-4 rounded-2xl text-sm shadow-md shadow-primary/25 hover:shadow-lg transition-all active:scale-95">
                <span class="material-symbols-outlined text-sm">send</span>
                <span>Submit Farmhouse Listing</span>
            </button>
            <a href="<?= url('owner/farmhouses') ?>" class="text-xs font-extrabold text-slate-400 hover:text-slate-600 transition text-decoration-none">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
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
function previewImages(input) {
    const preview = document.getElementById('img-preview');
    preview.innerHTML = '';
    if (input.files.length === 0) { preview.classList.add('hidden'); return; }
    preview.classList.remove('hidden');
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'aspect-square rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 shadow-2xs';
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// ── OWNER ROOM TYPES REPEATER ──
let ownerRoomTypeIndex = 0;
const initialOwnerRoomTypes = <?= json_encode($_POST['room_types'] ?? []) ?>;

function toggleOwnerRoomBooking(enabled) {
    const wrapper = document.getElementById('owner-room-types-wrapper');
    const msg = document.getElementById('owner-room-types-disabled');
    if (enabled) {
        if (wrapper) wrapper.classList.remove('hidden');
        if (msg) msg.classList.add('hidden');
        const container = document.getElementById('owner-room-types-container');
        if (container && container.children.length === 0) {
            addOwnerRoomTypeRow({ room_type_name: 'Standard Room', total_rooms: 4, price_per_room: 2000, capacity_per_room: 2, description: '' });
        }
    } else {
        if (wrapper) wrapper.classList.add('hidden');
        if (msg) msg.classList.remove('hidden');
    }
    updateOwnerRoomTypesSummary();
}

function addOwnerRoomTypeRow(data = {}) {
    const container = document.getElementById('owner-room-types-container');
    if (!container) return;

    const idx = ownerRoomTypeIndex++;
    const id = data.id || '';
    const name = data.room_type_name || '';
    const rooms = data.total_rooms || 1;
    const price = (data.price_per_room !== undefined && data.price_per_room !== null) ? data.price_per_room : '';
    const cap = data.capacity_per_room || 2;
    const desc = data.description || '';

    const row = document.createElement('div');
    row.className = 'owner-room-type-row p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-2xs space-y-3 transition hover:border-primary/50';
    row.id = `owner-room-type-row-${idx}`;
    row.innerHTML = `
        <input type="hidden" name="room_types[${idx}][id]" value="${escapeHtml(id)}">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Room Type Name -->
            <div class="sm:col-span-4">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    Room Type Name <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none">bedroom_parent</span>
                    <input type="text" name="room_types[${idx}][room_type_name]" value="${escapeHtml(name)}" required
                           placeholder="e.g. Deluxe Room"
                           oninput="updateOwnerRoomTypesSummary()"
                           class="w-full h-10 pl-9 pr-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none text-xs font-bold text-slate-900 bg-white">
                </div>
            </div>

            <!-- Number of Rooms Available -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    Rooms Count <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="room_types[${idx}][total_rooms]" value="${rooms}" min="1" max="100" required
                       placeholder="4"
                       oninput="updateOwnerRoomTypesSummary()"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none text-xs font-bold text-slate-900 text-center bg-white">
            </div>

            <!-- Price Per Room -->
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    Price / Room (₹) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">₹</span>
                    <input type="number" name="room_types[${idx}][price_per_room]" value="${price}" min="0" step="0.01" required
                           placeholder="2000"
                           oninput="updateOwnerRoomTypesSummary()"
                           class="w-full h-10 pl-7 pr-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none text-xs font-black text-slate-900 bg-white">
                </div>
            </div>

            <!-- Max Guests / Room -->
            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    Guests / Room <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="room_types[${idx}][capacity_per_room]" value="${cap}" min="1" max="10" required
                       placeholder="2"
                       oninput="updateOwnerRoomTypesSummary()"
                       class="w-full h-10 px-3 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none text-xs font-bold text-slate-900 text-center bg-white">
            </div>

            <!-- Delete Action -->
            <div class="sm:col-span-1 flex items-end justify-end pt-2 sm:pt-3">
                <button type="button" onclick="removeOwnerRoomTypeRow(${idx})" 
                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition" title="Delete Room Type">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </div>

            <!-- Optional Features / Description -->
            <div class="sm:col-span-12">
                <input type="text" name="room_types[${idx}][description]" value="${escapeHtml(desc)}"
                       placeholder="Optional specs: e.g. Air Conditioned, Attached Bath, Double Bed, Garden View"
                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-[11px] text-slate-600 focus:text-slate-900 focus:border-primary outline-none bg-white">
            </div>
        </div>
    `;

    container.appendChild(row);
    updateOwnerRoomTypesSummary();
}

function removeOwnerRoomTypeRow(idx) {
    const row = document.getElementById(`owner-room-type-row-${idx}`);
    if (row) {
        row.remove();
        updateOwnerRoomTypesSummary();
    }
}

function updateOwnerRoomTypesSummary() {
    const rows = document.querySelectorAll('.owner-room-type-row');
    let totalRooms = 0;
    let minPrice = null;
    let count = 0;

    rows.forEach(r => {
        const roomsInp = r.querySelector('input[name*="[total_rooms]"]');
        const priceInp = r.querySelector('input[name*="[price_per_room]"]');
        if (roomsInp && priceInp) {
            count++;
            const rooms = parseInt(roomsInp.value) || 0;
            const price = parseFloat(priceInp.value) || 0;
            totalRooms += rooms;
            if (price > 0 && (minPrice === null || price < minPrice)) {
                minPrice = price;
            }
        }
    });

    const typesEl = document.getElementById('owner-rt-types-count');
    const roomsEl = document.getElementById('owner-rt-rooms-count');
    const priceEl = document.getElementById('owner-rt-min-price');

    if (typesEl) typesEl.textContent = count;
    if (roomsEl) roomsEl.textContent = totalRooms;
    if (priceEl) priceEl.textContent = minPrice !== null ? `₹${minPrice.toLocaleString('en-IN')}` : '₹0';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

document.addEventListener('DOMContentLoaded', () => {
    if (initialOwnerRoomTypes && Object.keys(initialOwnerRoomTypes).length > 0) {
        Object.values(initialOwnerRoomTypes).forEach(rt => addOwnerRoomTypeRow(rt));
    } else {
        addOwnerRoomTypeRow({ room_type_name: 'Standard Room', total_rooms: 4, price_per_room: 2000, capacity_per_room: 2, description: 'Comfortable air-conditioned bedroom' });
        addOwnerRoomTypeRow({ room_type_name: 'Deluxe Room', total_rooms: 5, price_per_room: 3000, capacity_per_room: 2, description: 'Spacious premium room with balcony view' });
    }
});
</script>

<?php 
include __DIR__ . '/../../Includes/owner_footer.php'; 
?>
