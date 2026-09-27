<?php include __DIR__ . "/../Includes/admin_header.php"; ?>

<div class="flex-1 flex flex-col relative overflow-hidden bg-surface">
    <!-- TopAppBar -->
    <header class="flex justify-between items-center w-full px-4 md:px-6 py-3 bg-surface/80 backdrop-blur-xl border-b border-outline-variant/20 sticky top-0 z-50">
        <div class="flex items-center space-x-6">
            <span class="text-lg font-bold text-green-900 font-epilogue">The Elevated Estate</span>
        </div>
        <!-- Status Indicator -->
        <div class="flex items-center gap-3">
             <span class="text-xs font-bold uppercase text-on-surface-variant">Status:</span>
             <div class="px-3 py-1 rounded-full text-xs font-bold <?= $farmDetails['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= strtoupper($farmDetails['status']) ?>
             </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4 md:p-8 lg:p-10 space-y-8">
        <header>
            <h2 class="font-display text-2xl md:text-4xl text-on-surface font-bold tracking-tight">
                <?= htmlspecialchars($farmDetails['title'] ?? 'Select Property') ?>
            </h2>
            <p class="font-body text-on-surface-variant italic">
                Manage listing visibility and track customer inquiries for this estate.
            </p>
        </header>

        <!-- Farmhouse Selector (DYNAMIC) -->
        <section class="bg-surface-container p-6 rounded-xl border border-outline-variant/20">
            <form action="" method="GET" id="farmSelectorForm">
                <label class="block text-xs uppercase tracking-wider text-on-surface-variant font-bold mb-3">Select Property</label>
                <div class="relative max-w-md">
                    <select name="id" onchange="document.getElementById('farmSelectorForm').submit()" 
                            class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg py-4 pl-4 pr-10 shadow-sm focus:ring-2 focus:ring-primary ring-1 ring-black/5">
                        <?php foreach($allFarms as $farm): ?>
                            <option value="<?= $farm['id'] ?>" <?= $selectedFarmId == $farm['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($farm['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Details / Availability View -->
            <section class="lg:col-span-2 space-y-6">
                <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/30">
                    <h3 class="text-xl font-bold mb-4">Property Inquiries (Leads)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-on-surface-variant border-b border-outline-variant">
                                    <th class="pb-3">Customer</th>
                                    <th class="pb-3">Contact</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                <?php if(empty($inquiries)): ?>
                                    <tr><td colspan="4" class="py-10 text-center text-outline">No leads found for this property.</td></tr>
                                <?php else: ?>
                                    <?php foreach($inquiries as $lead): ?>
                                    <tr class="hover:bg-surface-container/30">
                                        <td class="py-4 font-bold text-on-surface"><?= htmlspecialchars($lead['name']) ?></td>
                                        <td class="py-4 text-on-surface-variant"><?= htmlspecialchars($lead['phone']) ?></td>
                                        <td class="py-4">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] bg-primary-container text-on-primary-container uppercase font-black">
                                                <?= $lead['status'] ?>
                                            </span>
                                        </td>
                                        <td class="py-4 text-xs"><?= date('M d, Y', strtotime($lead['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Sidebar Actions -->
            <aside class="space-y-6">
                <!-- Listing Status Control -->
                <div class="bg-surface-container p-6 rounded-2xl border border-outline-variant/20 shadow-sm">
                    <h4 class="font-bold text-lg mb-4">Quick Management</h4>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="update_status" value="1">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase font-bold text-on-surface-variant">Switch Listing Status</label>
                            <select name="status" class="w-full bg-surface-container-lowest rounded-lg py-2 text-sm border-none shadow-inner">
                                <option value="active" <?= $farmDetails['status'] === 'active' ? 'selected' : '' ?>>Make Active (Visible)</option>
                                <option value="inactive" <?= $farmDetails['status'] === 'inactive' ? 'selected' : '' ?>>Deactivate (Hidden)</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:brightness-110 shadow-md">
                            Apply Update
                        </button>
                    </form>
                </div>

                <div class="p-6 rounded-2xl bg-primary/5 border border-primary/20">
                    <p class="text-xs text-on-surface-variant leading-relaxed italic">
                        <strong>Tip:</strong> Inactive farmhouses will not show up in the public search but will keep their existing leads in this dashboard.
                    </p>
                </div>
            </aside>
        </div>
    </main>
</div>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>