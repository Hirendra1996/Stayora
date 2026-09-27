<?php
include __DIR__ . '/../../Includes/owner_header.php';

$farmhouse    = $farmhouse    ?? [];
$images       = $images       ?? [];
$amenities    = $amenities    ?? [];
$rules        = $rules        ?? [];
$bookingCount = $bookingCount ?? 0;

$id = (int)($farmhouse['id'] ?? 0);
$encId = \App\Helpers\CryptoHelper::encrypt($id);
$status = strtolower($farmhouse['status'] ?? 'pending');
?>

<div class="p-4 md:p-8 max-w-[1300px] mx-auto space-y-6">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 1. BACK & ACTIONS BAR                                       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <a href="<?= url('owner/farmhouses') ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-primary font-extrabold text-xs transition text-decoration-none">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span>Back to My Farmhouses</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="<?= url('owner/farmhouses/edit?id=' . $encId) ?>"
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white font-extrabold px-5 py-2.5 rounded-xl text-xs shadow-xs transition-all text-decoration-none">
                <span class="material-symbols-outlined text-sm">edit</span>
                <span>Edit Listing</span>
            </a>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 2. MEDIA GALLERY STRIP                                      -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <?php if (!empty($images)): ?>
        <div class="grid <?= count($images) >= 3 ? 'grid-cols-1 md:grid-cols-3' : (count($images) == 2 ? 'grid-cols-2' : 'grid-cols-1') ?> gap-3 rounded-3xl overflow-hidden h-[240px] md:h-[320px] shadow-sm">
            <?php foreach (array_slice($images, 0, 3) as $i => $img): ?>
                <div class="relative overflow-hidden h-full bg-slate-100 group">
                    <img src="<?= htmlspecialchars(farmhouse_img_url($img['image_url'] ?? '')) ?>" alt=""
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <?php if ($i === 2 && count($images) > 3): ?>
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center">
                            <span class="text-white font-black text-2xl font-headline">+<?= count($images) - 3 ?> More Photos</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-slate-100 border border-slate-200 rounded-3xl h-[180px] flex flex-col items-center justify-center text-slate-400 gap-2">
            <span class="material-symbols-outlined text-4xl text-slate-300">image</span>
            <span class="text-xs font-bold">No photos uploaded for this property yet.</span>
        </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- 3. MAIN DETAILS GRID                                        -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Main Info -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Title & Status Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-7 shadow-2xs">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h1 class="text-xl md:text-2xl font-black font-headline text-slate-900 leading-tight">
                            <?= htmlspecialchars($farmhouse['title'] ?? 'Farmhouse') ?>
                        </h1>
                        <p class="text-slate-500 font-semibold text-xs mt-1.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            <span><?= htmlspecialchars($farmhouse['location'] ?? 'India') ?></span>
                            <?php if (!empty($farmhouse['address'])): ?>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-400"><?= htmlspecialchars($farmhouse['address']) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-xs font-extrabold uppercase tracking-wide">
                            <?= htmlspecialchars($farmhouse['category'] ?? 'Farmhouse') ?>
                        </span>
                        <?php if ($status === 'active'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold uppercase tracking-wide">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active Live
                            </span>
                        <?php elseif ($status === 'pending'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-extrabold uppercase tracking-wide">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> In Review
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-extrabold uppercase tracking-wide">
                                Rejected
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($farmhouse['description'])): ?>
                    <div class="mt-5 pt-5 border-t border-slate-100 text-xs md:text-sm text-slate-600 leading-relaxed">
                        <?php
                            $descRaw = $farmhouse['description'];
                            $descDecoded = htmlspecialchars_decode($descRaw, ENT_QUOTES);
                            $hasHtml = (strip_tags($descDecoded) !== $descDecoded);
                            if ($hasHtml) {
                                echo strip_tags($descDecoded, '<ol><ul><li><p><br><br/><strong><b><i><em><span><div>');
                            } else {
                                echo nl2br(htmlspecialchars($descRaw, ENT_QUOTES, 'UTF-8'));
                            }
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Property Metrics Grid -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-7 shadow-2xs">
                <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">info</span>
                    <span>Configuration &amp; Pricing</span>
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <!-- Category -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">domain</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Category</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5"><?= htmlspecialchars($farmhouse['category'] ?? 'Farmhouse') ?></div>
                    </div>

                    <!-- Price / Day -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">currency_rupee</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Price / Day</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5">₹<?= number_format((float)($farmhouse['price'] ?? 0)) ?></div>
                    </div>

                    <!-- Bedrooms -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">king_bed</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Bedrooms</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5"><?= $farmhouse['bedrooms'] ?? '—' ?> BHK</div>
                    </div>

                    <!-- Day Capacity -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">sunny</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Day Capacity</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5"><?= $farmhouse['day_capacity'] ?? '—' ?> Guests</div>
                    </div>

                    <!-- Night Capacity -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">bed</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Night Capacity</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5"><?= $farmhouse['night_capacity'] ?? '—' ?> Guests</div>
                    </div>

                    <!-- Negotiable -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">handshake</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Negotiable</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5"><?= !empty($farmhouse['is_negotiable']) ? 'Yes' : 'Fixed' ?></div>
                    </div>

                    <!-- Total Inquiries -->
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <span class="material-symbols-outlined text-primary text-xl mb-1">calendar_month</span>
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Inquiries</div>
                        <div class="text-base font-black text-slate-900 font-headline mt-0.5"><?= $bookingCount ?> Received</div>
                    </div>
                </div>
            </div>

            <!-- Room Types & Inventory Breakdown Card -->
            <?php if (!empty($roomTypes)): ?>
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-7 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">meeting_room</span>
                        <span>Room Types &amp; Room-Wise Pricing</span>
                    </h2>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                        <?= count($roomTypes) ?> Room Type<?= count($roomTypes) > 1 ? 's' : '' ?> Available
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <?php foreach ($roomTypes as $rt): ?>
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex flex-col justify-between space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-base">bedroom_parent</span>
                                        <h3 class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($rt['room_type_name']) ?></h3>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-500 mt-1">
                                        <span class="text-emerald-700 font-bold"><?= (int)$rt['total_rooms'] ?> Room<?= (int)$rt['total_rooms'] > 1 ? 's' : '' ?> Available</span> • Max <?= (int)$rt['capacity_per_room'] ?> guests/room
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-primary text-base font-headline">₹<?= number_format((float)$rt['price_per_room']) ?></div>
                                    <div class="text-[10px] text-slate-400 font-semibold">/ room / night</div>
                                </div>
                            </div>

                            <?php if (!empty($rt['description'])): ?>
                                <p class="text-[11px] text-slate-600 bg-white/80 p-2.5 rounded-xl border border-slate-200/60 leading-relaxed">
                                    <?= htmlspecialchars($rt['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Amenities Card -->
            <?php if (!empty($amenities)): ?>
                <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-7 shadow-2xs">
                    <h2 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">check_circle</span>
                        <span>Included Amenities</span>
                    </h2>

                    <div class="flex flex-wrap gap-2.5">
                        <?php foreach ($amenities as $amenity): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700">
                                <span class="material-symbols-outlined text-sm text-primary">verified</span>
                                <span><?= htmlspecialchars($amenity['name'] ?? $amenity) ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right 1 Col: Quick Actions & Rules -->
        <div class="space-y-6">

            <!-- Host Actions Box -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs space-y-4">
                <h3 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider">Host Controls</h3>

                <div class="space-y-2.5">
                    <a href="<?= url('owner/farmhouses/edit?id=' . $encId) ?>" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-extrabold text-slate-800 transition-all text-decoration-none">
                        <span class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">edit</span>Edit Property Info</span>
                        <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                    </a>

                    <a href="<?= url('owner/farmhouses') ?>" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-extrabold text-slate-800 transition-all text-decoration-none">
                        <span class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">view_list</span>All Listings</span>
                        <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                    </a>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="https://wa.me/919876543210?text=Hello%20Farmlelo,%20I%20need%20help%20with%20farmhouse%20ID%20<?= $id ?>" target="_blank"
                       class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-extrabold flex items-center justify-center gap-2 shadow-xs transition-all text-decoration-none">
                        <svg style="width:15px;height:15px;fill:currentColor;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                        <span>WhatsApp Concierge</span>
                    </a>
                </div>
            </div>

            <!-- House Rules Card -->
            <?php if (!empty($rules)): ?>
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-2xs">
                    <h3 class="font-headline font-black text-sm text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">rule</span>
                        <span>House Rules</span>
                    </h3>

                    <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
                        <?php foreach ($rules as $rule): 
                            $ruleTitle = is_array($rule) ? ($rule['rule_name'] ?? $rule['name'] ?? $rule['rule_text'] ?? '') : (string)$rule;
                            if (empty($ruleTitle)) continue;
                        ?>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-xs text-primary mt-0.5">check</span>
                                <span><?= htmlspecialchars($ruleTitle) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php
include __DIR__ . '/../../Includes/owner_footer.php';
?>
