<?php include __DIR__ . "/../Includes/admin_header.php"; ?>

<main class="min-h-screen bg-surface">
    <div class="p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">
        
        <!-- Mobile Menu Header -->
        <div class="lg:hidden flex items-center justify-between mb-6">
            <button onclick="toggleSidebar()" class="p-2 -ml-2 text-primary">
                <span class="material-symbols-outlined text-[28px]">menu</span>
            </button>
            <span class="text-xl font-bold font-headline text-green-900 tracking-tight">Inquiry Manager</span>
            <div class="w-10"></div>
        </div>

        <!-- Success Toast for Form Handling -->
        <?php if (!empty($success_message)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($success_message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Page Title & Primary Filters -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold font-headline tracking-tight text-on-surface">Customer Inquiries</h1>
                <p class="text-on-surface-variant font-body mt-1 text-sm md:text-base">Review and act on property booking requests.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <!-- Status Tab Filter -->
                <div class="flex flex-wrap bg-surface-container rounded-xl p-1 shadow-inner gap-1">
                    <a href="?status=" class="px-3.5 py-1.5 rounded-lg text-xs md:text-sm text-center <?= !$currentStatusFilter ? 'font-bold bg-white text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:bg-black/5' ?>">
                        All (<?= $stats['all'] ?? 0 ?>)
                    </a>
                    <a href="?status=new" class="px-3.5 py-1.5 rounded-lg text-xs md:text-sm text-center <?= $currentStatusFilter === 'new' ? 'font-bold bg-white text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:bg-black/5' ?>">
                        New (<?= $stats['new'] ?? 0 ?>)
                    </a>
                    <a href="?status=contacted" class="px-3.5 py-1.5 rounded-lg text-xs md:text-sm text-center <?= $currentStatusFilter === 'contacted' ? 'font-bold bg-white text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:bg-black/5' ?>">
                        Contacted (<?= $stats['contacted'] ?? 0 ?>)
                    </a>
                    <a href="?status=converted" class="px-3.5 py-1.5 rounded-lg text-xs md:text-sm text-center <?= $currentStatusFilter === 'converted' ? 'font-bold bg-white text-emerald-600 shadow-sm' : 'font-medium text-on-surface-variant hover:bg-black/5' ?>">
                        Converted (<?= $stats['converted'] ?? 0 ?>)
                    </a>
                    <a href="?status=closed" class="px-3.5 py-1.5 rounded-lg text-xs md:text-sm text-center <?= $currentStatusFilter === 'closed' ? 'font-bold bg-white text-stone-600 shadow-sm' : 'font-medium text-on-surface-variant hover:bg-black/5' ?>">
                        Closed (<?= $stats['closed'] ?? 0 ?>)
                    </a>
                </div>
            </div>
        </div>

        <!-- Inquiries Grid List -->
        <?php if(empty($inquiries)): ?>
            <div class="py-12 flex flex-col items-center justify-center text-on-surface-variant text-center bg-surface-container-low rounded-3xl border border-dashed border-outline-variant/30">
                <span class="material-symbols-outlined text-5xl opacity-50 mb-4">search_off</span>
                <p class="font-bold">No Inquiries Found.</p>
                <p class="text-sm">There are no inquiries matching your current view criteria.</p>
                <a href="<?= url('admin/inquiries') ?>" class="mt-4 px-6 py-2 bg-primary/10 text-primary font-bold rounded-lg text-sm hover:bg-primary/20 transition-all">Clear Filters</a>
            </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            
            <?php foreach($inquiries as $row): 
                $nameTitle = !empty($row['name']) ? $row['name'] : 'Unknown Lead';
                $phone = !empty($row['phone']) ? $row['phone'] : 'N/A';
                $initial = strtoupper(substr($nameTitle, 0, 1));
                $safePhoneLink = ltrim($phone, '+'); // Useful for WhatsApp URL
                
                // Color codes logic mapping database logic back safely to design elements
                $stat = $row['status'] ?? 'new';
                if($stat === 'new') {
                    $statusClass = 'bg-primary/5 text-primary border-primary/10';
                    $pillIcon = 'new_releases';
                } elseif($stat === 'contacted') {
                    $statusClass = 'bg-yellow-50 text-yellow-700 border-yellow-100';
                    $pillIcon = 'how_to_reg';
                } elseif($stat === 'converted') {
                    $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    $pillIcon = 'check_circle';
                } else {
                    // Closed
                    $statusClass = 'bg-stone-100 text-stone-600 border-stone-200';
                    $pillIcon = 'lock';
                }
            ?>
            <div class="bg-white dark:bg-stone-900 border border-outline-variant/10 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm hover:shadow-xl hover:border-primary/20 transition-all group flex flex-col justify-between">
                <div>
                    <!-- Card Header: Profile & Dropdown Switch Status via Controller Action -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-secondary-container text-secondary flex items-center justify-center font-black text-lg shadow-inner">
                                <?= htmlspecialchars($initial) ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-on-surface leading-tight text-base truncate max-w-[120px] md:max-w-[140px]" title="<?= htmlspecialchars($nameTitle) ?>">
                                    <?= htmlspecialchars($nameTitle) ?>
                                </h3>
                                <p class="text-xs text-on-surface-variant font-medium"><?= htmlspecialchars($phone) ?></p>
                            </div>
                        </div>
                        
                        <!-- Real-time Admin Action - Swap Database Status easily directly upon changed! -->
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="inquiry_id" value="<?= $row['id'] ?>">
                            <div class="relative cursor-pointer">
                                <select name="status" onchange="this.form.submit()" class="pl-2 pr-6 py-1 bg-transparent cursor-pointer font-black text-[10px] tracking-wider uppercase appearance-none focus:outline-none focus:ring-0 absolute inset-0 w-full opacity-0">
                                    <option value="new" <?= $stat === 'new' ? 'selected' : '' ?>>Mark New</option>
                                    <option value="contacted" <?= $stat === 'contacted' ? 'selected' : '' ?>>Mark Contacted</option>
                                    <option value="converted" <?= $stat === 'converted' ? 'selected' : '' ?>>Mark Converted</option>
                                    <option value="closed" <?= $stat === 'closed' ? 'selected' : '' ?>>Close Inquiry</option>
                                </select>
                                <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg <?= $statusClass ?> border pointer-events-none group-hover:ring-2 ring-primary/20">
                                    <span class="text-[9px] font-black uppercase tracking-widest"><?= htmlspecialchars($stat) ?></span>
                                    <span class="material-symbols-outlined text-[12px] opacity-70">expand_more</span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Card Body: Property Target & DB Text Field Dump -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 bg-surface-container/50 px-3 py-2 rounded-xl border border-outline-variant/5">
                            <span class="material-symbols-outlined text-[18px] text-primary">apartment</span>
                            <p class="text-sm font-bold text-primary truncate">
                                <?= htmlspecialchars($row['farmhouse_title'] ?? 'General Inquiry / N/A') ?>
                            </p>
                        </div>
                        
                        <!-- Notes left by client originally via contact web portal layout format  -->
                        <div class="bg-surface p-3 rounded-xl min-h-[70px]">
                            <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed font-body italic text-stone-600 dark:text-stone-400">
                                <?= !empty($row['message']) ? nl2br(htmlspecialchars($row['message'])) : '<span class="opacity-50">No attached message sent from guest regarding initial lead reach out payload...</span>' ?>
                            </p>
                            <?php if (!empty($row['notes'])): ?>
                                <div class="mt-2 pt-2 border-t border-slate-200 text-[11px] text-sky-800 bg-sky-50 p-2 rounded-lg">
                                    <strong>Follow-up Note:</strong> <?= htmlspecialchars($row['notes']) ?>
                                    <?php if (!empty($row['follow_up_date'])): ?>
                                        <span class="block text-[10px] text-slate-500 mt-0.5">Due: <?= date('d M Y', strtotime($row['follow_up_date'])) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Footer Operations  -->
                <div class="mt-6 flex items-center justify-between pt-4 border-t border-outline-variant/10">
                    <span class="text-[10px] font-bold text-outline-variant uppercase flex items-center gap-1" title="<?= isset($row['created_at']) ? $row['created_at'] : '' ?>">
                        <span class="material-symbols-outlined text-[14px]">event_note</span>
                        <?= isset($row['created_at']) ? date('M j, y', strtotime($row['created_at'])) : 'Unknown Time' ?>
                    </span>
                    
                    <div class="flex items-center gap-2">
                        <!-- Caller Call Launch System (Tel schema)  -->
                        <a href="tel:<?= htmlspecialchars($phone) ?>" class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-700 rounded-xl shadow hover:bg-blue-200 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">call</span>
                        </a>
                        
                        <!-- Open WhatsAPP URL Hook Function Logic built natively onto Browser client URL logic API directly without third-party libraries for zero layout bloats. -->
                        <a href="https://wa.me/<?= htmlspecialchars($safePhoneLink) ?>" target="_blank" class="w-10 h-10 flex items-center justify-center bg-green-100 text-green-700 rounded-xl shadow hover:bg-green-200 transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">chat</span>
                        </a>
                        
                        <form method="POST" action="" onsubmit="return confirm('Permanently delete this inquiry from server records completely?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="inquiry_id" value="<?= $row['id'] ?>">
                             <button type="submit" class="ml-1 w-10 h-10 flex items-center justify-center text-red-400 hover:text-red-700 hover:bg-red-50 rounded-xl transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Overall KPI Metric Stats Dashboard Summaries. -->
        <div class="mt-12 bg-white/40 dark:bg-stone-900/40 backdrop-blur-lg rounded-3xl p-6 md:p-8 border border-outline-variant/10 grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="space-y-1 border-b md:border-b-0 pb-4 md:pb-0">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Lifetime Total Pool Size Amount Tracker</p>
                <p class="text-3xl font-black text-on-surface"><?= sprintf('%02d', (int)($stats['all'] ?? 0)) ?></p>
            </div>
            <div class="space-y-1 md:border-l border-b md:border-b-0 md:pl-8 pb-4 md:pb-0 border-outline-variant/20">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest text-primary font-semibold mb-2">Unread Critical Backlog Required Check Amount Limit Warning Setup Indicator Setup</p>
                <p class="text-3xl font-black text-primary"><?= sprintf('%02d', (int)($stats['new'] ?? 0)) ?></p>
            </div>
            <div class="space-y-1 border-none md:border-l pt-4 md:pt-0 md:pl-8 border-outline-variant/20 col-span-2 md:col-span-1">
                 <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest text-green-700">Archived Conversational Closings Amount Totals Checked Secured Data Completed Safely Backlog Limit Tracker Closed Data Record Metric Total Storage Counter Finished Limit Secured Indicator Limit</p>
                 <p class="text-3xl font-black text-green-700 font-extrabold shadow font-serif text-[60px] pb-[80px]"> <?= sprintf('%02d', (int)($stats['closed'] ?? 0)) ?> </p> 
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>