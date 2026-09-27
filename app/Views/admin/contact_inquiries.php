<?php include __DIR__ . "/../Includes/admin_header.php"; ?>

<main class="min-h-screen bg-surface">
    <div class="p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Mobile Menu Header -->
        <div class="lg:hidden flex items-center justify-between mb-6">
            <button onclick="toggleSidebar()" class="p-2 -ml-2 text-primary">
                <span class="material-symbols-outlined text-[28px]">menu</span>
            </button>
            <span class="text-xl font-bold font-headline text-green-900 tracking-tight">Contact Inquiries</span>
            <div class="w-10"></div>
        </div>

        <!-- Success Toast -->
        <?php if (!empty($success_message)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
                <p class="font-bold text-sm"><?= htmlspecialchars($success_message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Page Title -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold font-headline tracking-tight text-on-surface">Contact Inquiries</h1>
                <p class="text-on-surface-variant font-body mt-1 text-sm md:text-base">Review and respond to messages submitted via the contact form.</p>
            </div>

            <!-- Stats Summary Pill -->
            <div class="flex items-center gap-2 bg-surface-container rounded-xl px-4 py-2 shadow-inner self-start md:self-auto">
                <span class="material-symbols-outlined text-[18px] text-primary">inbox</span>
                <span class="text-sm font-bold text-on-surface"><?= (int)($stats['total'] ?? 0) ?> Total</span>
            </div>
        </div>

        <!-- Inquiries Grid -->
        <?php if (empty($inquiries)): ?>
            <div class="py-12 flex flex-col items-center justify-center text-on-surface-variant text-center bg-surface-container-low rounded-3xl border border-dashed border-outline-variant/30">
                <span class="material-symbols-outlined text-5xl opacity-50 mb-4">search_off</span>
                <p class="font-bold">No Contact Inquiries Found.</p>
                <p class="text-sm">No one has submitted a contact form yet.</p>
            </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">

            <?php foreach ($inquiries as $row):
                $fullName    = !empty($row['full_name']) ? $row['full_name'] : 'Unknown Sender';
                $phone       = !empty($row['phone'])     ? $row['phone']     : 'N/A';
                $email       = !empty($row['email'])     ? $row['email']     : 'N/A';
                $message     = !empty($row['Message'])   ? $row['Message']   : '';
                $initial     = strtoupper(substr($fullName, 0, 1));
                $safePhone   = ltrim($phone, '+');
                $encId       = $row['encrypted_id'];
            ?>
            <div class="bg-white dark:bg-stone-900 border border-outline-variant/10 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm hover:shadow-xl hover:border-primary/20 transition-all group flex flex-col justify-between">

                <!-- Card Header -->
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-secondary-container text-secondary flex items-center justify-center font-black text-lg shadow-inner">
                                <?= htmlspecialchars($initial) ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-on-surface leading-tight text-base truncate max-w-[140px] md:max-w-[160px]"
                                    title="<?= htmlspecialchars($fullName) ?>">
                                    <?= htmlspecialchars($fullName) ?>
                                </h3>
                                <p class="text-xs text-on-surface-variant font-medium"><?= htmlspecialchars($phone) ?></p>
                            </div>
                        </div>

                        <!-- Static "New" badge — no status column in this table -->
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary/5 text-primary border border-primary/10">
                            <span class="material-symbols-outlined text-[12px]">mark_email_unread</span>
                            <span class="text-[9px] font-black uppercase tracking-widest">New</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="space-y-3">

                        <!-- Email row -->
                        <div class="flex items-center gap-2 bg-surface-container/50 px-3 py-2 rounded-xl border border-outline-variant/5">
                            <span class="material-symbols-outlined text-[18px] text-primary">alternate_email</span>
                            <p class="text-sm font-bold text-primary truncate">
                                <?= htmlspecialchars($email) ?>
                            </p>
                        </div>

                        <!-- Message preview -->
                        <div class="bg-surface p-3 rounded-xl min-h-[70px] max-h-[140px] overflow-y-auto">
    <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed font-body italic text-stone-600 dark:text-stone-400 whitespace-pre-line break-words">
        <?php if (!empty($message)): ?>
            <?= htmlspecialchars($message) ?>
        <?php else: ?>
            <span class="opacity-50">No message content submitted with this inquiry.</span>
        <?php endif; ?>
    </p>
</div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="mt-6 flex items-center justify-between pt-4 border-t border-outline-variant/10">
                    <span class="text-[10px] font-bold text-outline-variant uppercase flex items-center gap-1"
                          title="<?= htmlspecialchars($row['created_at'] ?? '') ?>">
                        <span class="material-symbols-outlined text-[14px]">event_note</span>
                        <?= isset($row['created_at']) ? date('M j, y', strtotime($row['created_at'])) : 'Unknown' ?>
                    </span>

                    <div class="flex items-center gap-2">
                        <!-- Call -->
                        <a href="tel:<?= htmlspecialchars($phone) ?>"
                           class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-700 rounded-xl shadow hover:bg-blue-200 transition-all">
                            <span class="material-symbols-outlined text-[20px]">call</span>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/<?= htmlspecialchars($safePhone) ?>" target="_blank"
                           class="w-10 h-10 flex items-center justify-center bg-green-100 text-green-700 rounded-xl shadow hover:bg-green-200 transition-all">
                            <span class="material-symbols-outlined text-[20px]">chat</span>
                        </a>

                        <!-- Email -->
                        <a href="mailto:<?= htmlspecialchars($email) ?>"
                           class="w-10 h-10 flex items-center justify-center bg-violet-100 text-violet-700 rounded-xl shadow hover:bg-violet-200 transition-all">
                            <span class="material-symbols-outlined text-[20px]">mail</span>
                        </a>

                        <!-- Delete — uses encrypted_id, never raw id -->
                        <form method="POST" action=""
                              onsubmit="return confirm('Permanently delete this contact inquiry?');">
                            <input type="hidden" name="action"       value="delete">
                            <input type="hidden" name="encrypted_id" value="<?= htmlspecialchars($encId) ?>">
                            <button type="submit"
                                    class="ml-1 w-10 h-10 flex items-center justify-center text-red-400 hover:text-red-700 hover:bg-red-50 rounded-xl transition-all">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- KPI Stats Bar -->
        <div class="mt-12 bg-white/40 dark:bg-stone-900/40 backdrop-blur-lg rounded-3xl p-6 md:p-8 border border-outline-variant/10 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="space-y-1">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Total Submissions</p>
                <p class="text-3xl font-black text-on-surface"><?= sprintf('%02d', (int)($stats['total'] ?? 0)) ?></p>
            </div>

            <div class="space-y-1 md:border-l md:pl-8 border-outline-variant/20">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Latest Submission</p>
                <?php
                    $latest = !empty($inquiries) ? $inquiries[0]['created_at'] : null;
                ?>
                <p class="text-3xl font-black text-primary">
                    <?= $latest ? date('M j, Y', strtotime($latest)) : '—' ?>
                </p>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . "/../Includes/admin_footer.php"; ?>
