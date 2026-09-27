<?php include __DIR__ . "/Includes/header.php"; ?>
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<main class="flex-grow flex items-center justify-center min-h-screen pt-20 bg-surface">
  <div class="w-full max-w-md p-8 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/20">
    <h2 class="text-2xl font-bold mb-6 text-on-surface">Set New Password</h2>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="/reset-password-process" method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
      <input type="hidden" name="role"  value="<?= htmlspecialchars($_GET['role']  ?? 'users') ?>">
      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">New Password</label>
        <input type="password" name="password" required minlength="8" class="w-full border border-outline-variant rounded-xl px-4 py-3 text-sm">
      </div>
      <div class="mb-6">
        <label class="block text-sm font-medium mb-1">Confirm Password</label>
        <input type="password" name="confirm" required minlength="8" class="w-full border border-outline-variant rounded-xl px-4 py-3 text-sm">
      </div>
      <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold text-sm">Reset Password</button>
    </form>
  </div>
</main>

<?php include __DIR__ . "/Includes/footer.php"; ?>