<?php
include __DIR__ . "/../Includes/header.php"; 
use App\Helpers\CryptoHelper;

if (session_status() === PHP_SESSION_NONE) session_start();
$activeTab = $activeTab ?? (isset($_GET['tab']) ? htmlspecialchars($_GET['tab']) : ((stripos($_SERVER['REQUEST_URI'] ?? '', 'owner/register') !== false) ? 'register' : 'login'));
?>

<style>
/* ═══════════════════════════════════════════════════════════
   OWNER LOGIN & WORKSPACE PORTAL — LUXURY DESIGN SYSTEM
   ═══════════════════════════════════════════════════════════ */

.owner-auth-page {
  min-height: calc(100vh - 72px);
  padding-top: 72px;
  background: #F7F3EA;
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #24312A;
  display: flex;
  align-items: stretch;
}

/* ── Left Branding Panel ── */
.owner-hero-panel {
  width: 45%;
  background: 
    linear-gradient(180deg, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.65) 45%, rgba(15, 23, 42, 0.92) 100%),
    url('<?= asset('assets/images/uploads/luxury_pool_hero.jpg') ?>') center center / cover no-repeat;
  color: #ffffff;
  padding: 60px 48px;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: inset 0 0 100px rgba(0, 0, 0, 0.6);
}

.owner-hero-mesh {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 80% 20%, rgba(22, 165, 222, 0.25) 0%, transparent 50%),
              radial-gradient(circle at 20% 80%, rgba(2, 132, 199, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.owner-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  color: #C9A227;
  font-size: 11.5px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  padding: 6px 16px;
  border-radius: 30px;
  margin-bottom: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.owner-hero-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 40px;
  font-weight: 900;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin: 0 0 16px;
  color: #ffffff;
  text-shadow: 0 2px 20px rgba(0,0,0,0.6);
}

.owner-hero-title span {
  background: linear-gradient(135deg, #C9A227 0%, #1F4D3A 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.owner-hero-desc {
  font-size: 15px;
  color: #E2DBD0;
  line-height: 1.6;
  max-width: 440px;
  margin: 0 0 36px;
  text-shadow: 0 1px 10px rgba(0,0,0,0.5);
}

/* Feature highlight cards */
.owner-features-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 460px;
}

.owner-feature-item {
  display: flex;
  align-items: center;
  gap: 14px;
  background: rgba(15, 23, 42, 0.70);
  border: 1px solid rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  padding: 14px 18px;
  border-radius: 16px;
  font-size: 13.5px;
  font-weight: 700;
  color: #E2DBD0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.owner-feature-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(22, 165, 222, 0.2);
  color: #C9A227;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ── Right Auth Panel ── */
.owner-form-panel {
  width: 55%;
  background: #ffffff;
  padding: 60px 48px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  overflow-y: auto;
}

.owner-form-container {
  width: 100%;
  max-width: 440px;
}

/* Segmented Tab Switcher */
.owner-tabs-switch {
  display: flex;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 16px;
  margin-bottom: 28px;
  border: 1px solid #E2DBD0;
}

.owner-tab-btn {
  flex: 1;
  text-align: center;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 800;
  border-radius: 12px;
  color: #57685F;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.owner-tab-btn.active {
  background: #ffffff;
  color: #24312A;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.owner-tab-btn.active .material-symbols-outlined {
  color: #1F4D3A;
}

/* Form Styles */
.owner-input-group {
  margin-bottom: 16px;
}

.owner-input-label {
  display: block;
  font-size: 11px;
  font-weight: 800;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
}

.owner-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.owner-input-icon {
  position: absolute;
  left: 14px;
  color: #94a3b8;
  font-size: 18px;
  pointer-events: none;
  transition: color 0.2s ease;
}

.owner-input {
  width: 100%;
  height: 48px;
  padding: 0 14px 0 44px;
  border: 1.5px solid #E2DBD0;
  border-radius: 14px;
  font-size: 13.5px;
  font-family: inherit;
  color: #24312A;
  background: #F7F3EA;
  outline: none;
  transition: all 0.2s ease;
}

.owner-input:focus {
  background: #ffffff;
  border-color: #1F4D3A;
  box-shadow: 0 0 0 3px rgba(22, 165, 222, 0.15);
}

.owner-input:focus ~ .owner-input-icon {
  color: #1F4D3A;
}

.owner-eye-btn {
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
  transition: color 0.2s ease;
}

.owner-eye-btn:hover {
  color: #1F4D3A;
}

/* Submit Buttons */
.owner-submit-btn {
  width: 100%;
  height: 50px;
  background: #1F4D3A;
  color: #ffffff;
  border: none;
  border-radius: 14px;
  font-size: 14.5px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-shadow: 0 8px 20px rgba(22, 165, 222, 0.35);
  transition: all 0.25s ease;
  margin-top: 18px;
}

.owner-submit-btn:hover {
  background: #173C2D;
  transform: translateY(-1px);
  box-shadow: 0 12px 25px rgba(22, 165, 222, 0.45);
}

/* Alerts */
.owner-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 16px;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 20px;
}

.owner-alert-error {
  background: #fef2f2;
  border: 1.5px solid #fca5a5;
  color: #991b1b;
}

.owner-alert-success {
  background: #f0fdf4;
  border: 1.5px solid #86efac;
  color: #166534;
}

/* Form Views Transition */
.owner-form-view {
  display: none;
  animation: fadeIn 0.25s ease forwards;
}

.owner-form-view.active {
  display: block;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 1024px) {
  .owner-hero-panel { display: none; }
  .owner-form-panel { width: 100%; padding: 40px 24px; }
}
</style>

<div class="owner-auth-page">

  <!-- ── 1. Left Branding Panel (Desktop) ── -->
  <div class="owner-hero-panel">
    <div class="owner-hero-mesh"></div>
    <div style="position:relative;z-index:2;">
      <!-- Logo -->
      <a href="<?= url('home') ?>" style="display:inline-flex;align-items:center;gap:12px;text-decoration:none;margin-bottom:32px;">
        <img src="<?= asset('assets/images/logo.webp') ?>" alt="Farmlelo" style="height:38px;width:auto;">
        <div>
          <span style="font-family:'Epilogue',sans-serif;font-size:20px;font-weight:900;color:#ffffff;line-height:1;display:block;">
            Farm<span style="color:#C9A227;">Lelo</span>
          </span>
          <span style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Host Workspace</span>
        </div>
      </a>

      <div class="owner-hero-badge">
        <span class="material-symbols-outlined" style="font-size:15px;">vpn_key</span>
        <span>Host Partner Portal</span>
      </div>

      <h1 class="owner-hero-title">
        Manage Your <span>Farmhouse Empire</span>
      </h1>

      <p class="owner-hero-desc">
        Welcome to India's most advanced farmhouse host workspace. Review incoming guest inquiries, manage seasonal rates, and track your rental earnings in real time.
      </p>

      <!-- 4 Highlight Cards -->
      <div class="owner-features-list">
        <div class="owner-feature-item">
          <div class="owner-feature-icon">
            <span class="material-symbols-outlined" style="font-size:20px;">analytics</span>
          </div>
          <div>
            <div style="font-size:13px;color:#ffffff;">Live Booking &amp; Inquiry Feed</div>
            <div style="font-size:11px;color:#94a3b8;font-weight:500;">Instant alerts with guest details &amp; stay dates</div>
          </div>
        </div>

        <div class="owner-feature-item">
          <div class="owner-feature-icon">
            <span class="material-symbols-outlined" style="font-size:20px;">payments</span>
          </div>
          <div>
            <div style="font-size:13px;color:#ffffff;">Direct Bank Payouts</div>
            <div style="font-size:11px;color:#94a3b8;font-weight:500;">Transparent settlements before guest check-in</div>
          </div>
        </div>

        <div class="owner-feature-item">
          <div class="owner-feature-icon">
            <span class="material-symbols-outlined" style="font-size:20px;">calendar_month</span>
          </div>
          <div>
            <div style="font-size:13px;color:#ffffff;">100% Calendar &amp; Rate Control</div>
            <div style="font-size:11px;color:#94a3b8;font-weight:500;">Block personal family dates &amp; adjust weekend pricing</div>
          </div>
        </div>

        <div class="owner-feature-item">
          <div class="owner-feature-icon">
            <span class="material-symbols-outlined" style="font-size:20px;">support_agent</span>
          </div>
          <div>
            <div style="font-size:13px;color:#ffffff;">Dedicated Host Concierge</div>
            <div style="font-size:11px;color:#94a3b8;font-weight:500;">24/7 on-ground assistance &amp; check-in support</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Badge -->
    <div style="position:relative;z-index:2;display:flex;align-items:center;gap:8px;font-size:11px;font-weight:700;color:#94a3b8;border-top:1px solid rgba(255,255,255,0.1);padding-top:20px;margin-top:40px;">
      <span>🇮🇳</span>
      <span>#STARTUPINDIA · 100% VERIFIED HOST NETWORK</span>
    </div>
  </div>

  <!-- ── 2. Right Auth Form Panel ── -->
  <div class="owner-form-panel">
    <div class="owner-form-container">

      <!-- Mobile Logo Header (shown on mobile/tablet) -->
      <div style="display:none;" class="lg:hidden text-center mb-6">
        <a href="<?= url('home') ?>" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <img src="<?= asset('assets/images/logo-dark.webp') ?>" alt="Farmlelo" style="height:36px;width:auto;">
          <span style="font-family:'Epilogue',sans-serif;font-size:20px;font-weight:900;color:#24312A;">
            Farm<span style="color:#1F4D3A;">Lelo</span>
          </span>
        </a>
        <div style="font-size:11px;font-weight:800;color:#173C2D;text-transform:uppercase;letter-spacing:0.8px;margin-top:4px;">Host Workspace</div>
      </div>

      <!-- Segmented Switcher -->
      <div class="owner-tabs-switch">
        <button type="button" class="owner-tab-btn <?= ($activeTab === 'login') ? 'active' : '' ?>" id="tab-btn-login" onclick="switchTab('login')">
          <span class="material-symbols-outlined" style="font-size:16px;">lock</span>
          <span>Sign In</span>
        </button>
        <button type="button" class="owner-tab-btn <?= ($activeTab === 'register') ? 'active' : '' ?>" id="tab-btn-register" onclick="switchTab('register')">
          <span class="material-symbols-outlined" style="font-size:16px;">how_to_reg</span>
          <span>Apply as Host</span>
        </button>
      </div>

      <!-- Flash Feedback -->
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="owner-alert owner-alert-error">
          <span class="material-symbols-outlined" style="font-size:20px;">error</span>
          <span><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="owner-alert owner-alert-success">
          <span class="material-symbols-outlined" style="font-size:20px;">check_circle</span>
          <span><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        </div>
      <?php endif; ?>

      <!-- ══ TAB 1: OWNER LOGIN ══ -->
      <div class="owner-form-view <?= ($activeTab === 'login') ? 'active' : '' ?>" id="view-login">
        <div style="margin-bottom:24px;">
          <h2 style="font-family:'Epilogue',sans-serif;font-size:24px;font-weight:900;color:#24312A;margin:0 0 6px;">Welcome Back, Host</h2>
          <p style="font-size:13.5px;color:#57685F;margin:0;">Sign in to your owner dashboard to manage listings &amp; bookings.</p>
        </div>

        <form action="<?= url('owner/login-submit') ?>" method="POST">
          <input type="hidden" name="role" value="<?= CryptoHelper::encrypt('owners') ?>">

          <!-- Email -->
          <div class="owner-input-group">
            <label class="owner-input-label">Host Email Address</label>
            <div class="owner-input-wrap">
              <span class="material-symbols-outlined owner-input-icon">mail</span>
              <input type="email" name="email" class="owner-input" placeholder="owner@example.com" required>
            </div>
          </div>

          <!-- Password -->
          <div class="owner-input-group">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
              <label class="owner-input-label" style="margin-bottom:0;">Password</label>
              <a href="<?= url('owner/forgot-password') ?>" style="font-size:11.5px;font-weight:700;color:#1F4D3A;text-decoration:none;">Forgot Password?</a>
            </div>
            <div class="owner-input-wrap">
              <span class="material-symbols-outlined owner-input-icon">lock</span>
              <input type="password" name="password" id="owner_login_pwd" class="owner-input" placeholder="••••••••" required style="padding-right:42px;">
              <button type="button" class="owner-eye-btn" onclick="togglePwd('owner_login_pwd', this)">
                <span class="material-symbols-outlined" style="font-size:18px;">visibility</span>
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div style="display:flex;align-items:center;justify-content:space-between;margin:12px 0 20px;">
            <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#475569;font-weight:600;cursor:pointer;">
              <input type="checkbox" name="remember" style="accent-color:#1F4D3A;width:16px;height:16px;">
              <span>Keep me signed in</span>
            </label>
          </div>

          <button type="submit" class="owner-submit-btn">
            <span>Sign In to Dashboard</span>
            <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
          </button>
        </form>

        <div style="margin-top:28px;padding-top:20px;border-top:1px solid #E2DBD0;text-align:center;">
          <p style="font-size:13px;color:#57685F;margin:0 0 10px;">
            New farmhouse owner?
            <a href="javascript:void(0)" onclick="switchTab('register')" style="color:#1F4D3A;font-weight:800;text-decoration:none;">Apply to List Property</a>
          </p>
          <a href="<?= url('why-choose-us') ?>" style="font-size:12px;font-weight:700;color:#57685F;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
            <span class="material-symbols-outlined" style="font-size:15px;color:#1F4D3A;">info</span>
            <span>Learn Why 500+ Hosts Choose Farmlelo</span>
          </a>
        </div>
      </div>

      <!-- ══ TAB 2: OWNER REGISTER / APPLY ══ -->
      <div class="owner-form-view <?= ($activeTab === 'register') ? 'active' : '' ?>" id="view-register">
        <div style="margin-bottom:24px;">
          <h2 style="font-family:'Epilogue',sans-serif;font-size:24px;font-weight:900;color:#24312A;margin:0 0 6px;">Create Host Account</h2>
          <p style="font-size:13.5px;color:#57685F;margin:0;">Apply in 2 minutes to start listing your private estate.</p>
        </div>

        <form action="<?= url('owner/register-submit') ?>" method="POST">
          <!-- Full Name -->
          <div class="owner-input-group">
            <label class="owner-input-label">Full Name *</label>
            <div class="owner-input-wrap">
              <span class="material-symbols-outlined owner-input-icon">person</span>
              <input type="text" name="name" class="owner-input" placeholder="e.g. Vikramaditya Singh" required>
            </div>
          </div>

          <!-- Phone Number -->
          <div class="owner-input-group">
            <label class="owner-input-label">WhatsApp Mobile Number *</label>
            <div class="owner-input-wrap">
              <span class="material-symbols-outlined owner-input-icon">call</span>
              <input type="tel" name="phone" class="owner-input" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}" required>
            </div>
          </div>

          <!-- Email -->
          <div class="owner-input-group">
            <label class="owner-input-label">Email Address *</label>
            <div class="owner-input-wrap">
              <span class="material-symbols-outlined owner-input-icon">mail</span>
              <input type="email" name="email" class="owner-input" placeholder="owner@example.com" required>
            </div>
          </div>

          <!-- Password & Confirm (2-Column) -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="owner-input-group">
              <label class="owner-input-label">Password *</label>
              <div class="owner-input-wrap">
                <span class="material-symbols-outlined owner-input-icon">lock</span>
                <input type="password" name="password" id="reg_pwd" class="owner-input" placeholder="Min 8 chars" required style="padding-right:36px;font-size:12.5px;">
                <button type="button" class="owner-eye-btn" onclick="togglePwd('reg_pwd', this)" style="right:8px;">
                  <span class="material-symbols-outlined" style="font-size:16px;">visibility</span>
                </button>
              </div>
            </div>

            <div class="owner-input-group">
              <label class="owner-input-label">Confirm *</label>
              <div class="owner-input-wrap">
                <span class="material-symbols-outlined owner-input-icon">lock_reset</span>
                <input type="password" name="password_confirmation" id="reg_cpwd" class="owner-input" placeholder="Re-enter" required style="padding-right:36px;font-size:12.5px;">
                <button type="button" class="owner-eye-btn" onclick="togglePwd('reg_cpwd', this)" style="right:8px;">
                  <span class="material-symbols-outlined" style="font-size:16px;">visibility</span>
                </button>
              </div>
            </div>
          </div>

          <!-- T&C -->
          <label style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#57685F;line-height:1.4;margin:8px 0 16px;cursor:pointer;">
            <input type="checkbox" required style="accent-color:#1F4D3A;margin-top:2px;">
            <span>I agree to Farmlelo's <a href="<?= url('terms_conditions') ?>" target="_blank" style="color:#1F4D3A;font-weight:700;text-decoration:none;">Host Terms</a> and <a href="<?= url('privacy') ?>" target="_blank" style="color:#1F4D3A;font-weight:700;text-decoration:none;">Privacy Policy</a>.</span>
          </label>

          <button type="submit" class="owner-submit-btn">
            <span>Submit Application</span>
            <span class="material-symbols-outlined" style="font-size:18px;">how_to_reg</span>
          </button>
        </form>

        <div style="margin-top:24px;padding-top:16px;border-top:1px solid #E2DBD0;text-align:center;">
          <p style="font-size:13px;color:#57685F;margin:0;">
            Already have a host account?
            <a href="javascript:void(0)" onclick="switchTab('login')" style="color:#1F4D3A;font-weight:800;text-decoration:none;">Sign In</a>
          </p>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
function switchTab(tab) {
  const loginBtn = document.getElementById('tab-btn-login');
  const regBtn = document.getElementById('tab-btn-register');
  const loginView = document.getElementById('view-login');
  const regView = document.getElementById('view-register');

  if (tab === 'register') {
    loginBtn.classList.remove('active');
    regBtn.classList.add('active');
    loginView.classList.remove('active');
    regView.classList.add('active');
  } else {
    regBtn.classList.remove('active');
    loginBtn.classList.add('active');
    regView.classList.remove('active');
    loginView.classList.add('active');
  }
}

function togglePwd(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon = btn.querySelector('.material-symbols-outlined');
  if (input.type === 'password') {
    input.type = 'text';
    icon.textContent = 'visibility_off';
    icon.style.color = '#1F4D3A';
  } else {
    input.type = 'password';
    icon.textContent = 'visibility';
    icon.style.color = '#94a3b8';
  }
}

// Auto-switch to register if URL has #register or ?tab=register or pathname is owner/register
if (window.location.pathname.includes('owner/register') || window.location.hash === '#register' || new URLSearchParams(window.location.search).get('tab') === 'register') {
  switchTab('register');
}
</script>

<?php 
include __DIR__ . "/../Includes/footer.php"; 
?>