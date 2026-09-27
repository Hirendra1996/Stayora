<?php
// ── Safety guard: redirect if accessed directly without controller variables ──
if (!isset($farmhouse) || !is_array($farmhouse)) {
    http_response_code(404);
    exit('Farmhouse not found.');
}

include __DIR__ . "/../Includes/user_header.php";

// Fallback for userData in case it isn't defined in this controller
$userData = $userData ?? ['name' => 'Guest', 'profile_image' => ''];

// ─── Amenity icon map ─────────────────────────────────────────────────────────
$amenityIcons = [
    'Swimming Pool' => 'pool',
    'Garden'        => 'park',
    'Parking'       => 'directions_car',
    'WiFi'          => 'wifi',
    'Kitchen'       => 'kitchen',
    'AC Rooms'      => 'ac_unit',
    'Music System'  => 'speaker',
    'Bonfire'       => 'local_fire_department',
];

// ─── Safe variable defaults ───────────────────────────────────────────────────
$unavailableDates = $unavailableDates ?? [];
$adminSettings    = $adminSettings    ?? [];
$inquirySuccess   = $inquirySuccess   ?? false;
$inquiryError     = $inquiryError     ?? '';

$adminPhone    = $adminSettings['admin_phone']     ?? '';
$adminWhatsapp = $adminSettings['admin_whatsapp']  ?? '';
$whatsappUrl   = 'https://wa.me/91' . preg_replace('/\D/', '', $adminWhatsapp);

// ─── Images ───────────────────────────────────────────────────────────────────
$images     = $farmhouse['images'] ?? [];
$primaryImg = farmhouse_img_url($images[0]['image_url'] ?? null, 'https://placehold.co/900x600/E8F5E9/2E7D32?text=No+Image');

// ─── Build booked/blocked ranges for JS calendar ─────────────────────────────
$bookedRanges  = [];
$blockedRanges = [];
foreach ($unavailableDates as $range) {
    if (($range['type'] ?? '') === 'booked') {
        $bookedRanges[]  = ['start' => $range['start_date'], 'end' => $range['end_date']];
    } else {
        $blockedRanges[] = ['start' => $range['start_date'], 'end' => $range['end_date']];
    }
}
?>

<!-- Offset container fixing UI overlap caused by global Fixed Header (from 0 to 72px base) -->
<div class="min-h-screen bg-background font-body pt-[72px] text-on-surface w-full pb-20">

    <!-- ── Responsive User/Dashboard Secondary Header ────────────────────────────────────────── -->
    <header class="flex justify-between items-center px-4 md:px-10 h-16 w-full bg-white/70 backdrop-blur-xl border-b border-outline-variant/30 sticky top-[72px] z-30 transition-all duration-300">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="md:hidden p-2 text-primary hover:bg-primary/10 rounded-xl transition-colors">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
            <h1 class="hidden md:block font-headline text-[15px] font-bold text-on-surface">
                <?php echo htmlspecialchars($userData['name'] ?? 'User'); ?>'s Interface Explorer
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-primary/20 shadow-sm bg-surface-container">
                <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_name'] ?? 'U') . '&background=16A5DE&color=fff'; ?>" alt="Profile" class="w-full h-full object-cover"/>
            </div>
        </div>
    </header>

    <!-- ── Main Responsive Canvas Wrapper ─────────────────────────────────────── -->
    <div class="p-4 md:px-8 lg:px-16 pt-6 md:pt-10 max-w-[90rem] mx-auto w-full flex flex-col gap-6 md:gap-8">

        <!-- ── Breadcrumbs & Badges ──────────────────────────────────────────────────────────── -->
        <div class="flex flex-wrap justify-between items-center gap-4 w-full">
            <nav class="flex items-center text-xs md:text-[13px] font-semibold text-on-surface-variant flex-wrap gap-1 bg-surface-container-low px-4 py-2 rounded-full border border-stone-200 shadow-sm w-fit">
                <a href="<?= url('farmhouses') ?>" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">holiday_village</span> Estates
                </a>
                
                <?php if (!empty($farmhouse['location'])): ?>
                <span class="material-symbols-outlined text-[18px] opacity-40">chevron_right</span>
                <a href="<?= url('farmhouses?search=' . urlencode($farmhouse['location'])) ?>"
                   class="hover:text-primary transition-colors">
                    <?= htmlspecialchars($farmhouse['location']) ?>
                </a>
                <?php endif; ?>
                
                <span class="material-symbols-outlined text-[18px] opacity-40">chevron_right</span>
                <span class="text-on-background font-black line-clamp-1 truncate max-w-[150px] md:max-w-[200px]">
                    <?= htmlspecialchars($farmhouse['title'] ?? 'Farmhouse') ?>
                </span>
            </nav>

            <div class="flex gap-2 shrink-0">
                <span class="px-3 md:px-4 py-1.5 rounded-full bg-brand-emerald-100 text-brand-emerald-600 font-bold text-[10px] uppercase flex items-center gap-1 shadow-sm border border-brand-emerald-200 tracking-wider">
                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">verified</span> Verified
                </span>
                <?php if (!empty($farmhouse['is_negotiable'])): ?>
                <span class="px-3 md:px-4 py-1.5 rounded-full bg-orange-100 border border-orange-200 text-orange-700 font-bold text-[10px] uppercase hidden sm:flex items-center gap-1 shadow-sm tracking-wider">
                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">sell</span> Price Negotiable
                </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Hero Headers ──────────────────────────────────────────────────────────────── -->
        <div class="mt-2 md:mt-4 px-1 w-full max-w-4xl">
            <h1 class="font-headline text-3xl md:text-5xl lg:text-[54px] font-black text-on-surface tracking-tight leading-tight">
                <?= htmlspecialchars($farmhouse['title'] ?? 'Untitled Estate') ?>
            </h1>
            
            <?php
            $displayAddress = $farmhouse['address'] ?? $farmhouse['location'] ?? '';
            if ($displayAddress): ?>
            <p class="text-on-surface-variant font-medium mt-3 flex items-center gap-2 text-sm md:text-base underline underline-offset-4 decoration-primary/30 decoration-2 hover:text-on-surface transition-colors cursor-pointer w-fit">
                <span class="material-symbols-outlined text-primary mb-px" style="font-variation-settings:'FILL' 1;">location_on</span>
                <?= htmlspecialchars($displayAddress) ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- ── Unified Image Gallery (Modern Bento Design) ────────────────────────────────────────── -->
        <div class="relative w-full rounded-[1.5rem] md:rounded-[2.5rem] overflow-hidden mt-2 bg-surface-container-low shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 bg-white/5 h-[30vh] sm:h-[45vh] md:h-[60vh] min-h-[300px]">

                <!-- Main Super Image Box (Span 2 col + 2 row on destkop) -->
                <div class="md:col-span-2 md:row-span-2 relative cursor-pointer group w-full h-full overflow-hidden bg-stone-100"
                     onclick="openGallery(0)">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                         src="<?= htmlspecialchars($primaryImg) ?>"
                         alt="<?= htmlspecialchars($farmhouse['title'] ?? 'Main property picture') ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>

                <?php
                // Sub Grid Layout Extraction logic (Pulls extra photos or fills missing tiles softly)
                $extraImages = array_slice($images, 1, 4);
                $remaining   = max(0, count($images) - 5);
                for ($i = 0; $i < 4; $i++):
                    $isLast = ($i === 3);
                ?>
                <div class="hidden md:block overflow-hidden relative cursor-pointer w-full h-full group bg-stone-100" onclick="openGallery(<?= $i + 1 ?>)">
                    <?php if (isset($extraImages[$i]['image_url'])): ?>
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                             src="<?= htmlspecialchars(farmhouse_img_url($extraImages[$i]['image_url'])) ?>"
                             alt="Interior or exterior visual <?= $i + 2 ?>">
                             
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-300"></div>
                             
                        <?php if ($isLast && $remaining > 0): ?>
                        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px] flex flex-col items-center justify-center text-white cursor-pointer hover:bg-black/40 transition-colors text-lg"
                             onclick="event.stopPropagation(); openGallery(4)">
                            <span class="material-symbols-outlined text-4xl mb-1 drop-shadow">photo_library</span>
                            <span class="font-bold text-sm tracking-wide">Show all (+<?= $remaining ?>)</span>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Standard Fallback Filler Element -->
                        <div class="w-full h-full flex flex-col gap-1 items-center justify-center bg-stone-50 border border-stone-100/50 text-stone-300 shadow-inner group-hover:bg-stone-100 transition-colors">
                            <span class="material-symbols-outlined text-[32px] opacity-70" style="font-variation-settings: 'FILL' 1;">landscape</span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Absolute Global 'View all photos' mobile-responsive Button overlay positioned gracefully  -->
            <button onclick="openGallery(0)" class="absolute bottom-5 right-5 bg-white text-on-surface shadow-xl hover:bg-surface-container border-2 border-transparent hover:scale-105 active:scale-95 flex items-center gap-2 rounded-2xl px-5 py-2.5 font-bold text-sm md:text-sm transition-all duration-300">
                <span class="material-symbols-outlined text-xl">grid_view</span> All Pictures
            </button>
        </div>

        <!-- ── Primary Structural Layout Grids: Split Left(8) details vs Right(4) Forms ───────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 md:gap-14 pt-8 md:pt-12 items-start px-2">

            <!-- ── LEFT WING W/ PROPERTY SPECS ────────────────────────────────────── -->
            <div class="lg:col-span-8 flex flex-col gap-12 lg:pr-6 border-b border-stone-200 lg:border-b-0 pb-10 lg:pb-0">
                
                <!-- Detailed Context Segment Section  -->
                <section>
                    <h2 class="font-headline text-xl md:text-[28px] font-bold mb-4 md:mb-5">Detailed Estate Context</h2>
                    <p class="text-on-surface-variant font-medium text-base md:text-[17px] leading-[1.85] text-justify max-w-4xl text-pretty opacity-90 tracking-[0.015em]">
                        <?= nl2br(htmlspecialchars($farmhouse['description'] ?? 'An exclusive, immersive rural getaway located in beautiful agrarian zones...')) ?>
                    </p>
                    <?php if (!empty($farmhouse['owner_notes'])): ?>
                    <div class="mt-8 p-5 bg-yellow-50 border-l-[4px] border-l-yellow-400 rounded-xl flex gap-4 w-full text-yellow-900 text-[14px]">
                         <span class="material-symbols-outlined shrink-0 text-yellow-500">campaign</span>
                        <div class="flex flex-col space-y-1 w-full font-medium">
                            <span class="font-bold text-[12px] uppercase tracking-wider opacity-60">Admin Message Log Board: </span>
                            <?= nl2br(htmlspecialchars($farmhouse['owner_notes'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>
                
                <hr class="w-2/3 border-t-2 border-stone-100 opacity-80 my-4" />

                <!-- Amenities Aesthetic Showcase Modules Card-->
                <?php if (!empty($farmhouse['amenities'])): ?>
                <section class="">
                    <h2 class="font-headline text-xl md:text-[28px] font-bold mb-6 md:mb-8">Highlights & Facilities Provided</h2>
                    
                    <div class="grid grid-cols-1 min-[450px]:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-7 pt-1 w-full">
                        <?php foreach ($farmhouse['amenities'] as $amenity):
                            $amenity = trim($amenity);
                            $icon    = $amenityIcons[$amenity] ?? 'check_circle';
                        ?>
                        <div class="flex items-center gap-4 bg-transparent w-full">
                            <div class="w-11 h-11 bg-primary-container shrink-0 text-white rounded-xl shadow-inner border border-primary/20 flex justify-center items-center drop-shadow-sm">
                                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings:'FILL' 1;"><?= htmlspecialchars($icon) ?></span>
                            </div>
                            <span class="text-sm md:text-base font-semibold text-on-surface tracking-wide"><?= htmlspecialchars($amenity) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <hr class="w-2/3 border-t-2 border-stone-100 opacity-80 my-4" />
                <?php endif; ?>

                <!-- Location & Google Map Segment -->
                <?php
                    $uMapLocation = trim(($farmhouse['location'] ?? '') . (!empty($farmhouse['address']) ? ', ' . $farmhouse['address'] : ''));
                    $uMapEmbed    = build_google_map_embed_url($farmhouse['google_map_link'] ?? '', $uMapLocation);
                    $uMapDirect   = build_google_map_direct_url($farmhouse['google_map_link'] ?? '', $uMapLocation);
                ?>
                <section class="space-y-4">
                    <div class="flex justify-between items-center flex-wrap gap-3">
                        <div>
                            <h2 class="font-headline text-xl md:text-[28px] font-bold m-0 leading-none">Where You'll Be</h2>
                            <p class="text-on-surface-variant font-medium text-sm mt-1.5 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-red-500 text-lg">location_on</span>
                                <span><?= htmlspecialchars($farmhouse['location'] ?? 'India') ?></span>
                                <?php if (!empty($farmhouse['address'])): ?>
                                    <span>· <?= htmlspecialchars($farmhouse['address']) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <a href="<?= htmlspecialchars($uMapDirect) ?>" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs border border-sky-200 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-base">map</span>
                            Open in Google Maps
                            <span class="material-symbols-outlined text-xs">open_in_new</span>
                        </a>
                    </div>
                    <div class="w-full h-80 rounded-3xl overflow-hidden border border-stone-200 shadow-sm bg-stone-50 relative">
                        <iframe 
                            src="<?= htmlspecialchars($uMapEmbed) ?>" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </section>
                <hr class="w-2/3 border-t-2 border-stone-100 opacity-80 my-4" />

                <!-- ── Core Calendar Matrix Segment ──────────────────────────────────── -->
                <section>
                    <div class="flex justify-between items-center mb-6 flex-wrap gap-4 px-1">
                        <div class="flex flex-col gap-1 w-fit">
                            <h2 class="font-headline text-xl md:text-[28px] font-bold m-0 leading-none">Trip Viability</h2>
                            <span class="text-on-surface-variant font-medium text-xs opacity-80 mt-2 block tracking-widest uppercase ml-0.5">ESTATE CALENDAR</span>
                        </div>
                    </div>
                    
                    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-stone-200">
                        <div class="flex justify-between items-center bg-stone-50 py-3 px-5 border border-stone-200 rounded-xl mb-6">
                           <!-- Control Toggles Custom Elements Wrapper UI Styles Matrix Code injection block element class matrixs elements controls code-->
                            <button onclick="calPrev()" class="w-9 h-9 flex justify-center items-center bg-white shadow-sm border border-stone-200 text-stone-500 rounded-full hover:bg-primary hover:text-white transition-all transform active:scale-95 outline-none focus:outline-none focus-visible:outline-none">
                                <span class="material-symbols-outlined font-bold text-[20px]">arrow_back_ios_new</span>
                            </button>
                            <h3 id="calTitle" class="font-headline font-black tracking-wide text-on-surface uppercase min-w-[140px] text-center w-full block bg-transparent h-fit drop-shadow-sm p-1 rounded focus:outline-none user-select-none select-none text-[15px] pointer-events-none appearance-none ">MONTH LOADING</h3>
                            <button onclick="calNext()" class="w-9 h-9 flex justify-center items-center bg-white shadow-sm border border-stone-200 text-stone-500 rounded-full hover:bg-primary hover:text-white transition-all transform active:scale-95 outline-none focus:outline-none focus-visible:outline-none">
                                <span class="material-symbols-outlined font-bold text-[20px]">arrow_forward_ios</span>
                            </button>
                        </div>
                        
                        <div class="w-full flex-grow mx-auto select-none overflow-x-hidden p-1 min-h-[300px]">
                            <!-- Main grid calendar wrapper internal injected content handler component structure UI interface matrix handler control node node nodes wrapper container block structural wrapper -->
                            <div class="grid grid-cols-7 text-center gap-1.5 md:gap-3 lg:gap-4 select-none mb-3 bg-surface-container-low p-2 rounded-xl text-stone-400 font-bold uppercase border-dashed border-2 text-[10px]">
                                <?php foreach (['SUN','MON','TUE','WED','THU','FRI','SAT'] as $d): ?> <span><?= $d ?></span> <?php endforeach; ?>
                            </div>
                            
                            <!-- Rendering the Grid Nodes Javascript wrapper div via script payload injected via id attribute payload selector targets controls variables elements component struct module controller matrix structure codes styles code CSS structural layout framework DOM wrapper base wrapper parent controller code segment struct-->
                            <div id="calGrid" class="grid grid-cols-7 gap-1 md:gap-3 justify-center text-center pb-2 select-none user-select-none touch-none touch-pan-x ">
                                <!-- injected here directly -->
                            </div>
                        </div>

                        <div class="mt-6 border-t border-dashed border-stone-300 pt-6 flex flex-wrap justify-center sm:justify-start gap-4 md:gap-8 px-4 w-full">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 bg-primary/20 ring-1 ring-primary inline-block rounded-[3px]"></span><span class="text-[12px] md:text-sm font-semibold opacity-90">Can book instantly</span></div>
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 bg-error border border-error/50 ring ring-error/20 inline-block rounded-full shadow-inner relative "><span class="w-1 h-1 bg-white/40 block absolute top-1 right-0 rounded-full inset-1 "></span></span><span class="text-[12px] md:text-sm font-bold opacity-60">Reserved </span></div>
                            <div class="flex items-center gap-2 opacity-60"><span class="w-2.5 h-2.5 bg-stone-300 border border-stone-400 inline-block rounded-[3px]"></span><span class="text-[12px] md:text-sm font-medium">Blocked</span></div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- === END OF LEFT COLUMN === -->
            <div class="lg:col-span-4 relative mt-[-2rem] md:mt-0 max-w-[100%] mx-auto w-full md:w-3/4 lg:w-full mb-10 pb-2 flex-grow h-fit">
               
               <div class="sticky lg:top-[120px] pb-10"> 

                 <div class="bg-surface-container-lowest border border-stone-200/80 shadow-xl rounded-[28px] p-6 lg:p-8 flex flex-col justify-start">
                    
                    <!-- Header Block: Price -->
                    <div class="w-full flex justify-between items-center pb-5 border-b border-stone-100">
                        <div>
                            <?php if (!empty($farmhouse['price']) && (float)$farmhouse['price'] > 0): ?>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl lg:text-4xl font-black font-headline text-primary">
                                    ₹<?= number_format((float)$farmhouse['price'], 0) ?>
                                </span>
                                <span class="text-sm font-semibold text-on-surface-variant">/ night</span>
                            </div>
                            <?php else: ?>
                            <h3 class="text-2xl font-bold font-headline text-on-surface">Price on Request</h3>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($farmhouse['is_negotiable'])): ?>
                        <span class="px-3 py-1 rounded-full bg-orange-100 border border-orange-200 text-orange-700 font-bold text-[11px] uppercase tracking-wider">
                            Negotiable
                        </span>
                        <?php endif; ?>
                    </div> 

                    <!-- Inquiry Feedback Alerts -->
                    <?php if ($inquirySuccess): ?>
                    <div class="mt-5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm font-semibold">
                        <span class="material-symbols-outlined text-emerald-600 shrink-0">check_circle</span>
                        <span>Inquiry submitted successfully! We'll get back to you shortly.</span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($inquiryError)): ?>
                    <div class="mt-5 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 text-sm font-semibold">
                        <span class="material-symbols-outlined text-rose-600 shrink-0">error</span>
                        <span><?= htmlspecialchars($inquiryError) ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Inquiry Form -->
                    <form method="POST" action="" id="inquiryForm" class="mt-6 flex flex-col gap-4">
                        <input type="hidden" name="submit_inquiry" value="1">

                        <div>
                            <label for="userName" class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="userName" required
                                   class="w-full px-4 py-3 bg-surface-container-low border border-stone-200 rounded-xl font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all text-sm"
                                   value="<?= htmlspecialchars($_POST['name'] ?? ($userData['name'] !== 'Guest' ? $userData['name'] : '')) ?>"
                                   placeholder="Enter your full name">
                        </div>

                        <div>
                            <label for="userPhone" class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Contact Number <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" id="userPhone" required
                                   class="w-full px-4 py-3 bg-surface-container-low border border-stone-200 rounded-xl font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all text-sm"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                   placeholder="Enter your mobile number">
                        </div>

                        <div>
                            <label for="userMessage" class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Message (Optional)
                            </label>
                            <textarea name="message" id="userMessage" rows="3"
                                      class="w-full px-4 py-3 bg-surface-container-low border border-stone-200 rounded-xl font-medium text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all text-sm resize-none"
                                      placeholder="Dates, guests count, or any special requests..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-primary/20 transition-all transform active:scale-95 text-sm flex items-center justify-center gap-2 mt-1">
                            <span class="material-symbols-outlined text-lg">send</span>
                            Submit Booking Inquiry
                        </button>
                    </form>

                    <!-- Direct Connect / Admin Support Buttons -->
                    <?php if (!empty($adminPhone) || !empty($adminWhatsapp)): ?>
                    <div class="mt-6 pt-6 border-t border-stone-200 flex flex-col gap-3">
                        <p class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider text-center">
                            Or connect instantly
                        </p>

                        <?php if (!empty($adminWhatsapp)): ?>
                        <a href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank"
                           class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white py-3.5 px-4 rounded-xl font-bold flex items-center justify-center gap-2.5 text-sm shadow-sm transition-all active:scale-95">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
                            Chat on WhatsApp
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($adminPhone)): ?>
                        <a href="tel:<?= htmlspecialchars($adminPhone) ?>"
                           class="w-full bg-surface-container-high hover:bg-surface-container-highest text-on-surface py-3.5 px-4 rounded-xl font-bold flex items-center justify-center gap-2.5 text-sm border border-stone-200 transition-all active:scale-95">
                            <span class="material-symbols-outlined text-primary text-xl">call</span>
                            Call Support (<?= htmlspecialchars($adminPhone) ?>)
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                 </div> 
               </div> 

            </div>
            <!-- === END OF RIGHT DOCK === -->

        </div>
        <!-- === END OF PRIMARY STRUCTURAL LAYOUT GRIDS === -->

    </div>
    <!-- === END OF MAIN RESPONSIVE CANVAS WRAPPER === -->

</div>
<!-- === END OF OFFSET CONTAINER === -->

<!-- ── CALENDAR LOGIC SCRIPT ── -->
<script>
(function () {
    const bookedRanges  = <?= json_encode($bookedRanges,  JSON_HEX_TAG) ?>;
    const blockedRanges = <?= json_encode($blockedRanges, JSON_HEX_TAG) ?>;
    const MONTHS = ['JANUARY','FEBRUARY','MARCH','APRIL','MAY','JUNE','JULY','AUGUST','SEPTEMBER','OCTOBER','NOVEMBER','DECEMBER'];

    let viewYear, viewMonth;
    function init() {
        const t = new Date();
        viewYear  = t.getFullYear();
        viewMonth = t.getMonth();
        render();
    }

    function toDateStr(y, m, d) {
        return `${y}-${String(m + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    }
    function inRange(dateStr, ranges) {
        return ranges.some(r => dateStr >= r.start && dateStr <= r.end);
    }
    function render() {
        const titleEl = document.getElementById('calTitle');
        if (titleEl) titleEl.textContent = `${MONTHS[viewMonth]} ${viewYear}`;
        const grid = document.getElementById('calGrid');
        if (!grid) return;

        const firstDay  = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMon = new Date(viewYear, viewMonth + 1, 0).getDate();
        const today     = new Date();
        const todayStr  = toDateStr(today.getFullYear(), today.getMonth(), today.getDate());
        let html = '';

        for (let i = 0; i < firstDay; i++) {
            html += `<div class="p-1 sm:p-2 bg-transparent select-none"></div>`;
        }
        for (let d = 1; d <= daysInMon; d++) {
            const ds   = toDateStr(viewYear, viewMonth, d);
            const past = ds < todayStr;

            let cls = 'flex h-10 w-full sm:h-[46px] rounded-[10px] items-center justify-center font-bold text-sm sm:text-base ring-0 outline-none transition-all duration-200 ';

            if (past) {
                cls += 'bg-stone-50 border border-dashed border-stone-200 text-stone-300 cursor-not-allowed opacity-40 select-none';
            } else if (inRange(ds, bookedRanges)) {
                cls += 'bg-red-100 border border-red-300 text-red-600 line-through opacity-70 cursor-not-allowed';
            } else if (inRange(ds, blockedRanges)) {
                cls += 'bg-stone-200 border border-stone-300 text-stone-500 line-through opacity-60 cursor-not-allowed';
            } else {
                cls += 'bg-white border border-stone-200 hover:bg-primary/10 hover:border-primary text-on-surface hover:text-primary cursor-pointer shadow-sm';
            }

            if (ds === todayStr) {
                cls = cls.replace('border-stone-200', 'border-primary ring-2 ring-primary/30');
            }

            html += `<div class="${cls}" title="${ds}">${d}</div>`;
        }
        grid.innerHTML = html;
    }

    window.calNext = function () {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        render();
    };
    window.calPrev = function () {
        const t = new Date();
        if (viewYear === t.getFullYear() && viewMonth === t.getMonth()) return;
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        render();
    };
    init();
})();
</script>

<!-- ── FULLSCREEN IMAGE GALLERY MODAL ── -->
<?php if (count($images) > 0): ?>
<div id="galleryModal"
     class="fixed inset-0 bg-black/90 backdrop-blur-md z-[60] hidden flex-col items-center justify-center p-4 selection:bg-transparent"
     onclick="closeGallery(event)">

    <!-- Close button -->
    <button onclick="closeGallery()"
            class="absolute top-5 md:top-8 right-5 md:right-8 bg-white/10 hover:bg-white text-white hover:text-black font-bold w-12 h-12 flex justify-center items-center rounded-full transition-all shadow-2xl backdrop-blur-md border border-white/20 transform hover:scale-105 active:scale-95 z-50">
        <span class="material-symbols-outlined text-2xl leading-none">close</span>
    </button>

    <!-- Prev button -->
    <button onclick="prevPhoto(event)"
            class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white text-white hover:text-black w-12 h-12 flex justify-center items-center rounded-full transition-all backdrop-blur-md border border-white/20 hover:scale-105 active:scale-95 z-50">
        <span class="material-symbols-outlined text-2xl leading-none">chevron_left</span>
    </button>

    <!-- Image Display -->
    <div class="relative max-w-5xl max-h-[80vh] flex flex-col items-center justify-center p-2" onclick="event.stopPropagation()">
        <img id="galleryImg"
             src="<?= htmlspecialchars($primaryImg) ?>"
             alt="Gallery Preview"
             class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl transition-opacity duration-300">
        <div id="galleryCounter" class="text-white/80 font-bold text-xs uppercase tracking-widest mt-4">
            1 OF <?= count($images) ?>
        </div>
    </div>

    <!-- Next button -->
    <button onclick="nextPhoto(event)"
            class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white text-white hover:text-black w-12 h-12 flex justify-center items-center rounded-full transition-all backdrop-blur-md border border-white/20 hover:scale-105 active:scale-95 z-50">
        <span class="material-symbols-outlined text-2xl leading-none">chevron_right</span>
    </button>
</div>

<script>
    const allPhotos = <?= json_encode(array_map('farmhouse_img_url', array_column($images, 'image_url'))) ?>;
    let currentPhoto = 0;

    function openGallery(index) {
        if (!allPhotos.length) return;
        currentPhoto = index % allPhotos.length;
        updateGallery();
        const modal = document.getElementById('galleryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeGallery(e) {
        if (e && e.target !== document.getElementById('galleryModal') && e.type !== 'click') return;
        const modal = document.getElementById('galleryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function nextPhoto(e) {
        if (e) e.stopPropagation();
        currentPhoto = (currentPhoto + 1) % allPhotos.length;
        updateGallery();
    }

    function prevPhoto(e) {
        if (e) e.stopPropagation();
        currentPhoto = (currentPhoto - 1 + allPhotos.length) % allPhotos.length;
        updateGallery();
    }

    function updateGallery() {
        const img = document.getElementById('galleryImg');
        if (!img) return;
        img.classList.remove('opacity-100');
        img.classList.add('opacity-0');

        setTimeout(() => {
            img.src = allPhotos[currentPhoto];
            img.onload = () => {
                img.classList.remove('opacity-0');
                img.classList.add('opacity-100');
            };
        }, 150);
        const counter = document.getElementById('galleryCounter');
        if (counter) {
            counter.textContent = (currentPhoto + 1) + ' OF ' + allPhotos.length;
        }
    }

    document.addEventListener('keydown', e => {
        const modal = document.getElementById('galleryModal');
        if (!modal || modal.classList.contains('hidden')) return;
        if (e.key === 'ArrowRight') nextPhoto();
        if (e.key === 'ArrowLeft')  prevPhoto();
        if (e.key === 'Escape')     closeGallery();
    });
</script>
<?php endif; ?>

<?php include __DIR__ . "/../Includes/user_footer.php"; ?>