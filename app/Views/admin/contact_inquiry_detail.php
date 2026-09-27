<?php
$pageTitle  = "Contact Message Details";
$activePage = "inquiries";

include __DIR__ . "/../Includes/admin_header.php";

$fullName  = !empty($inquiry['full_name']) ? $inquiry['full_name'] : 'Unknown Sender';
$email     = !empty($inquiry['email'])     ? $inquiry['email']     : 'N/A';
$phone     = !empty($inquiry['phone'])     ? $inquiry['phone']     : 'N/A';
$message   = !empty($inquiry['Message'])   ? $inquiry['Message']   : (!empty($inquiry['message']) ? $inquiry['message'] : 'No message body provided.');
$initial   = strtoupper(substr($fullName, 0, 1));
$safePhone = preg_replace('/[^0-9]/', '', $phone);
$encId     = $inquiry['encrypted_id'] ?? '';
$createdAt = !empty($inquiry['created_at']) ? date('d M Y, h:i A', strtotime($inquiry['created_at'])) : 'N/A';
?>

<div class="space-y-6 max-w-4xl mx-auto pb-12">

    <!-- Back Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="<?= url('admin/contact-inquiries') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-sky hover:text-sky-d mb-2 transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to Contact Inquiries
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                Inquiry from <?= htmlspecialchars($fullName) ?>
            </h1>
            <p class="text-xs sm:text-sm text-muted mt-1">
                Submitted on <?= htmlspecialchars($createdAt) ?>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- WhatsApp Action -->
            <?php if (!empty($safePhone)): ?>
            <a href="https://wa.me/<?= htmlspecialchars($safePhone) ?>?text=Hello%20<?= urlencode($fullName) ?>,%20thank%20you%20for%20contacting%20Farmlelo." 
               target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                <span class="material-symbols-outlined text-base">chat</span>
                WhatsApp
            </a>
            <?php endif; ?>

            <!-- Email Action -->
            <?php if (!empty($email) && $email !== 'N/A'): ?>
            <a href="mailto:<?= htmlspecialchars($email) ?>?subject=Farmlelo%20Inquiry%20Response" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky hover:bg-sky-d text-white font-bold text-xs shadow-md transition-all">
                <span class="material-symbols-outlined text-base">mail</span>
                Email Reply
            </a>
            <?php endif; ?>

            <!-- Delete Action -->
            <form method="POST" action="<?= url('admin/contact-inquiries') ?>" onsubmit="return confirm('Permanently delete this inquiry?');" class="inline-block">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="encrypted_id" value="<?= htmlspecialchars($encId) ?>">
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-all">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Sender Details Card -->
    <div class="bg-white rounded-2xl border border-border p-6 shadow-sm">
        <h2 class="text-xs font-bold uppercase tracking-wider text-muted mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-sky text-base">person</span>
            Sender Contact Information
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface rounded-xl p-4 border border-border/60">
                <span class="text-[11px] font-bold text-muted uppercase tracking-wider block mb-1">Full Name</span>
                <span class="text-sm font-bold text-ink flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-sky-l text-sky flex items-center justify-center font-black text-xs">
                        <?= htmlspecialchars($initial) ?>
                    </span>
                    <?= htmlspecialchars($fullName) ?>
                </span>
            </div>

            <div class="bg-surface rounded-xl p-4 border border-border/60">
                <span class="text-[11px] font-bold text-muted uppercase tracking-wider block mb-1">Email Address</span>
                <span class="text-sm font-bold text-ink break-all">
                    <?= htmlspecialchars($email) ?>
                </span>
            </div>

            <div class="bg-surface rounded-xl p-4 border border-border/60">
                <span class="text-[11px] font-bold text-muted uppercase tracking-wider block mb-1">Phone Number</span>
                <span class="text-sm font-bold text-ink">
                    <?= htmlspecialchars($phone) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Message Content Card -->
    <div class="bg-white rounded-2xl border border-border p-6 shadow-sm space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-muted flex items-center gap-2">
            <span class="material-symbols-outlined text-sky text-base">mail</span>
            Message Content
        </h2>

        <div class="bg-surface-xl rounded-xl p-5 border border-sky/15">
            <p class="text-sm leading-relaxed text-ink whitespace-pre-line font-medium">
                <?= htmlspecialchars($message) ?>
            </p>
        </div>

        <div class="pt-4 border-t border-border flex items-center justify-between text-xs text-muted">
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm text-sky">schedule</span>
                Received: <?= htmlspecialchars($createdAt) ?>
            </span>
            <span class="px-2.5 py-1 rounded-md bg-sky-l text-sky font-bold uppercase tracking-wider text-[10px]">
                Active Inquiry
            </span>
        </div>
    </div>

</div>

<?php
include __DIR__ . "/../Includes/admin_footer.php";
?>
