<?php include __DIR__ . "/../Includes/admin_header.php"; ?>

        <!-- TopAppBar -->
        <header class="flex flex-col md:flex-row justify-between items-center w-full px-4 md:px-8 h-auto md:h-20 sticky top-0 z-40 bg-stone-50/90 dark:bg-stone-950/90 backdrop-blur-xl border-b border-stone-200/50">
            
            <!-- Navigation Links - Filter by GET 'tab' -->
            <?php $currTab = $_GET['tab'] ?? 'all'; ?>
            <div class="flex items-center gap-4 md:gap-8 w-full md:w-auto overflow-x-auto no-scrollbar py-3">
                <a class="<?= $currTab == 'all' ? 'text-green-900 border-b-2 border-green-800' : 'text-stone-500' ?> pb-2 whitespace-nowrap text-sm md:text-base font-bold" href="?tab=all">All Bookings</a>
                <a class="<?= $currTab == 'confirmed' ? 'text-green-900 border-b-2 border-green-800' : 'text-stone-500' ?> pb-2 whitespace-nowrap text-sm" href="?tab=confirmed">Upcoming</a>
                <a class="<?= $currTab == 'completed' ? 'text-green-900 border-b-2 border-green-800' : 'text-stone-500' ?> pb-2 whitespace-nowrap text-sm" href="?tab=completed">Completed</a>
                <a class="<?= $currTab == 'cancelled' ? 'text-green-900 border-b-2 border-green-800' : 'text-stone-500' ?> pb-2 whitespace-nowrap text-sm" href="?tab=cancelled">Cancelled</a>
            </div>
            
            <!-- ... Search box same as your HTML ... -->
        </header>

        <!-- Page Content -->
        <div class="p-4 md:p-10 flex-1 flex flex-col gap-6 md:gap-8 max-w-7xl mx-auto w-full">
            
            <div class="flex flex-col gap-6 bg-surface p-6 rounded-2xl border border-stone-200 shadow-sm">
                <h2 class="font-display text-2xl md:text-3xl font-bold">Manage Bookings (<?= count($bookings) ?>)</h2>
                <p class="text-sm text-on-surface-variant">Overseeing your farmhouse estate's occupancy.</p>
            </div>

            <!-- Bento Grid List -->
            <div class="flex flex-col gap-6">
                <?php if(empty($bookings)): ?>
                    <div class="p-20 text-center text-stone-400 font-bold border-2 border-dashed rounded-3xl">No bookings found in this category.</div>
                <?php else: ?>
                    <?php foreach($bookings as $booking): ?>
                        <div class="bg-stone-50 border border-stone-200 rounded-2xl overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-all <?= $booking['status'] == 'cancelled' ? 'grayscale opacity-70' : '' ?>">
                            
                            <!-- Dynamic Image -->
                            <div class="md:w-72 h-48 md:h-auto shrink-0 relative">
                                <img alt="<?= htmlspecialchars($booking['title']) ?>" class="w-full h-full object-cover" 
                                     src="<?= !empty($booking['thumb']) ? $booking['thumb'] : 'https://placehold.co/600x400?text=Farmhouse' ?>" />
                                <div class="absolute top-3 left-3 <?= $booking['status'] == 'confirmed' ? 'bg-green-700' : 'bg-stone-700' ?> text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
                                    <?= $booking['status'] ?>
                                </div>
                            </div>
                            
                            <!-- Dynamic Content -->
                            <div class="p-6 flex-1 flex flex-col gap-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-display text-xl md:text-2xl font-bold"><?= htmlspecialchars($booking['title']) ?></h3>
                                        <div class="flex items-center gap-2 text-stone-500 text-xs md:text-sm mt-1">
                                            <span class="material-symbols-outlined text-base">calendar_today</span>
                                            <span><?= date('M d', strtotime($booking['check_in'])) ?> - <?= date('M d, Y', strtotime($booking['check_out'])) ?></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-green-700">₹<?= number_format($booking['total_price']) ?></p>
                                        <p class="text-[10px] text-stone-400 uppercase font-bold">Paid Total</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 py-4 border-y border-stone-200">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-stone-400">Customer</p>
                                        <p class="font-semibold text-sm"><?= htmlspecialchars($booking['customer_name']) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-stone-400">Contact</p>
                                        <p class="font-semibold text-sm"><?= htmlspecialchars($booking['customer_phone']) ?></p>
                                    </div>
                                </div>

                                <!-- Real Actions -->
                                <div class="flex flex-wrap items-center justify-end gap-3">
                                    <a href="tel:<?= $booking['customer_phone'] ?>" class="p-2 rounded-lg bg-white border hover:text-green-700"><span class="material-symbols-outlined align-middle">call</span></a>
                                    <a target="_blank" href="https://wa.me/<?= $booking['customer_phone'] ?>" class="px-6 py-2 rounded-lg bg-green-800 text-white font-bold text-sm hover:bg-green-900 transition-all flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">chat</span> WhatsApp
                                    </a>
                                    
                                    <!-- Status Update Forms -->
                                    <?php if($booking['status'] == 'confirmed'): ?>
                                    <form method="POST" onsubmit="return confirm('Cancel this booking?')">
                                        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="action_status" value="cancelled">
                                        <button class="p-2 rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white transition-all"><span class="material-symbols-outlined align-middle">close</span></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>