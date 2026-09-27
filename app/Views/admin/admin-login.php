<?php
if (session_status() === PHP_SESSION_NONE) session_start();
use App\Helpers\CryptoHelper;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal | Farmlelo</title>
  <link rel="icon" type="image/webp" href="<?= asset('assets/images/logo.webp') ?>"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #24312A;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      color: #24312A;
      position: relative;
      overflow-x: hidden;
    }

    .admin-bg-mesh {
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 20% 20%, rgba(22, 165, 222, 0.15) 0%, transparent 50%),
                  radial-gradient(circle at 80% 80%, rgba(2, 132, 199, 0.12) 0%, transparent 50%);
      pointer-events: none;
    }

    .admin-card {
      width: 100%;
      max-width: 420px;
      background: #ffffff;
      border: 1px solid #E2DBD0;
      border-radius: 24px;
      padding: 36px 32px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
      position: relative;
      z-index: 2;
    }

    .admin-logo-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .admin-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #f1f5f9;
      border: 1px solid #E2DBD0;
      color: #475569;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 4px 12px;
      border-radius: 20px;
      margin-bottom: 12px;
    }

    .admin-heading {
      text-align: center;
      margin-bottom: 24px;
    }

    .admin-heading h1 {
      font-family: 'Epilogue', sans-serif;
      font-size: 22px;
      font-weight: 900;
      color: #24312A;
      margin-bottom: 4px;
    }

    .admin-heading p {
      font-size: 13px;
      color: #57685F;
    }

    .admin-group {
      margin-bottom: 16px;
    }

    .admin-label {
      display: block;
      font-size: 11px;
      font-weight: 800;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }

    .admin-input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }

    .admin-icon {
      position: absolute;
      left: 14px;
      color: #94a3b8;
      font-size: 18px;
      pointer-events: none;
    }

    .admin-input {
      width: 100%;
      height: 46px;
      padding: 0 14px 0 42px;
      border: 1.5px solid #E2DBD0;
      border-radius: 12px;
      font-size: 13.5px;
      font-family: inherit;
      color: #24312A;
      background: #F7F3EA;
      outline: none;
      transition: all 0.2s ease;
    }

    .admin-input:focus {
      background: #ffffff;
      border-color: #1F4D3A;
      box-shadow: 0 0 0 3px rgba(22, 165, 222, 0.15);
    }

    .admin-input.has-eye {
      padding-right: 42px;
    }

    .admin-eye-btn {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .admin-eye-btn:hover {
      color: #24312A;
    }

    .admin-btn {
      width: 100%;
      height: 48px;
      background: #24312A;
      color: #ffffff;
      border: none;
      border-radius: 14px;
      font-size: 14px;
      font-weight: 800;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.25);
      transition: all 0.25s ease;
      margin-top: 20px;
    }

    .admin-btn:hover {
      background: #1F4D3A;
      transform: translateY(-2px);
      box-shadow: 0 12px 25px rgba(22, 165, 222, 0.35);
    }

    .admin-alert {
      background: #fef2f2;
      border: 1.5px solid #fca5a5;
      color: #991b1b;
      padding: 12px 14px;
      border-radius: 12px;
      font-size: 12.5px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 18px;
    }
  </style>
</head>
<body>

  <div class="admin-bg-mesh"></div>

  <div class="admin-card">
    <div class="admin-logo-row">
      <img src="<?= asset('assets/images/logo-dark.webp') ?>" alt="Farm Lelo Logo" style="height:40px;width:auto;object-fit:contain;">
    </div>

    <div class="admin-heading">
      <div class="admin-badge">
        <span class="material-symbols-outlined" style="font-size:15px;color:#1F4D3A;">admin_panel_settings</span>
        <span>Secure Administration</span>
      </div>
      <h1>Admin Portal</h1>
      <p>Sign in to manage listings, bookings, and platform settings.</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="admin-alert">
        <span class="material-symbols-outlined" style="font-size:18px;">error</span>
        <span><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= url('admin/login-submit') ?>" method="POST">
      <input type="hidden" name="role" value="<?= CryptoHelper::encrypt('admins') ?>">

      <div class="admin-group">
        <label class="admin-label">Admin Email</label>
        <div class="admin-input-wrap">
          <span class="material-symbols-outlined admin-icon">mail</span>
          <input type="email" name="email" class="admin-input" placeholder="admin@farmlelo.com" required autofocus>
        </div>
      </div>

      <div class="admin-group">
        <label class="admin-label">Password</label>
        <div class="admin-input-wrap">
          <span class="material-symbols-outlined admin-icon">lock</span>
          <input type="password" name="password" id="admin-pass" class="admin-input has-eye" placeholder="••••••••" required>
          <button type="button" class="admin-eye-btn" onclick="togglePassVisibility('admin-pass', this)" aria-label="Toggle password view">
            <span class="material-symbols-outlined" style="font-size:18px;">visibility_off</span>
          </button>
        </div>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin:12px 0 16px;">
        <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:#57685F;cursor:pointer;">
          <input type="checkbox" name="remember" style="accent-color:#1F4D3A;cursor:pointer;">
          <span>Remember me</span>
        </label>
        <a href="<?= url('home') ?>" style="font-size:12px;font-weight:700;color:#1F4D3A;text-decoration:none;">Back to Website</a>
      </div>

      <button type="submit" class="admin-btn">
        <span>Sign In to Dashboard</span>
        <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
      </button>
    </form>

    <p style="text-align:center;font-size:11px;color:#94a3b8;margin-top:24px;font-weight:600;">
      © <?= date('Y') ?> Farmlelo. Authorized Personnel Only.
    </p>
  </div>

  <script>
    function togglePassVisibility(inputId, btn) {
      const inp = document.getElementById(inputId);
      if (!inp) return;
      const icon = btn.querySelector('.material-symbols-outlined');
      if (inp.type === 'password') {
        inp.type = 'text';
        if (icon) icon.textContent = 'visibility';
      } else {
        inp.type = 'password';
        if (icon) icon.textContent = 'visibility_off';
      }
    }
  </script>

</body>
</html>