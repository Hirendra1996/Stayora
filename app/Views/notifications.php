<?php
$pageTitle = 'Notification Center';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Notification Center | Stayora</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">

<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 transition text-slate-700">
                <span class="material-symbols-outlined text-lg leading-none">arrow_back</span>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Notification Center</h1>
                <p class="text-xs text-slate-500">Real-time alerts, bookings, and platform updates</p>
            </div>
        </div>

        <button onclick="markAllRead()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">done_all</span>
            <span>Mark All Read</span>
        </button>
    </div>

    <!-- Notification List -->
    <?php if (empty($notifications)): ?>
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-3xl">notifications_off</span>
            </div>
            <h3 class="text-base font-bold text-slate-700">All caught up!</h3>
            <p class="text-xs text-slate-500 mt-1">You have no unread notifications or alerts at this moment.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs divide-y divide-slate-100">
            <?php foreach ($notifications as $n): ?>
                <?php
                $isRead = !empty($n['is_read']);
                $type = $n['type'] ?? 'info';
                ?>
                <div class="p-4 sm:p-5 flex items-start gap-4 transition <?= $isRead ? 'hover:bg-slate-50/50' : 'bg-sky-50/40 hover:bg-sky-50/70' ?>" id="notif-<?= $n['id'] ?>">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 font-bold
                        <?php
                        if ($type === 'booking') echo 'bg-emerald-100 text-emerald-700';
                        elseif ($type === 'payment') echo 'bg-sky-100 text-sky-700';
                        elseif ($type === 'inquiry') echo 'bg-amber-100 text-amber-700';
                        else echo 'bg-slate-100 text-slate-700';
                        ?>">
                        <span class="material-symbols-outlined text-xl">
                            <?php
                            if ($type === 'booking') echo 'calendar_month';
                            elseif ($type === 'payment') echo 'payments';
                            elseif ($type === 'inquiry') echo 'forum';
                            else echo 'notifications';
                            ?>
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-900 <?= !$isRead ? 'font-black' : '' ?>">
                                <?= htmlspecialchars($n['title']) ?>
                            </h3>
                            <span class="text-[10px] font-semibold text-slate-400 flex-shrink-0">
                                <?= date('d M, h:i A', strtotime($n['created_at'])) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            <?= nl2br(htmlspecialchars($n['message'])) ?>
                        </p>

                        <div class="mt-2.5 flex items-center gap-3">
                            <?php if (!empty($n['link'])): ?>
                                <a href="<?= url($n['link']) ?>" class="text-[11px] font-bold text-sky-600 hover:text-sky-700 hover:underline flex items-center gap-1">
                                    <span>View Details</span>
                                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!$isRead): ?>
                                <button onclick="markOneRead(<?= $n['id'] ?>)" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600">
                                    Mark as read
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$isRead): ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 flex-shrink-0 mt-1.5" title="Unread"></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function markOneRead(id) {
    fetch('<?= url("api/notifications/mark-read") ?>?id=' + id)
        .then(() => {
            const el = document.getElementById('notif-' + id);
            if (el) {
                el.classList.remove('bg-sky-50/40');
                const dot = el.querySelector('.bg-sky-500');
                if (dot) dot.remove();
            }
        });
}

function markAllRead() {
    fetch('<?= url("api/notifications/mark-all-read") ?>')
        .then(() => location.reload());
}
</script>
</body>
</html>
