<?php 
include __DIR__ . "/../Includes/header.php"; 
$userData = $userData ?? ['name' => $user['name'] ?? 'Guest', 'profile_image' => ''];
$user = $user ?? ['name' => '', 'email' => '', 'phone' => '', 'status' => 'active', 'created_at' => date('Y-m-d')];
$booking_count = $booking_count ?? 0;
?>

<style>
.prof-page-wrap {
  min-height: calc(100vh - 72px);
  padding-top: 72px;
  background: #F7F3EA;
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #24312A;
}

/* Profile Hero */
.prof-hero-banner {
  background: #ffffff;
  border-bottom: 1px solid #E2DBD0;
  padding: 36px 0;
  position: relative;
  overflow: hidden;
}

.prof-hero-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.prof-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #EAF1EB;
  color: #173C2D;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 4px 12px;
  border-radius: 20px;
  margin-bottom: 8px;
}

.prof-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 32px;
  font-weight: 900;
  color: #24312A;
  margin: 0 0 4px;
  letter-spacing: -0.02em;
}

.prof-subtitle {
  font-size: 14px;
  color: #57685F;
  margin: 0;
}

/* Profile Layout */
.prof-main-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 24px 60px;
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 32px;
  align-items: start;
}

/* Card Boxes */
.prof-card {
  background: #ffffff;
  border: 1px solid #E2DBD0;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  margin-bottom: 24px;
}

.prof-card-header {
  padding: 20px 28px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 12px;
}

.prof-card-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: #EAF1EB;
  color: #173C2D;
  display: flex;
  align-items: center;
  justify-content: center;
}

.prof-card-icon.red {
  background: #fee2e2;
  color: #dc2626;
}

.prof-card-header h2 {
  font-size: 17px;
  font-weight: 800;
  color: #24312A;
  margin: 0;
}

.prof-card-header p {
  font-size: 12px;
  color: #57685F;
  margin: 2px 0 0;
}

.prof-card-body {
  padding: 28px;
}

/* Avatar Upload Row */
.prof-avatar-row {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 18px;
  background: #F7F3EA;
  border: 1px solid #E2DBD0;
  border-radius: 18px;
  margin-bottom: 24px;
}

.prof-avatar-preview {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  object-fit: cover;
  border: 2px solid #ffffff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.prof-upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #00AEEF;
  color: #ffffff;
  font-size: 12px;
  font-weight: 700;
  padding: 8px 16px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.prof-upload-btn:hover {
  background: #173C2D;
}

/* Inputs */
.prof-group {
  margin-bottom: 18px;
}

.prof-label {
  display: block;
  font-size: 11px;
  font-weight: 800;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
}

.prof-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.prof-icon {
  position: absolute;
  left: 14px;
  color: #94a3b8;
  font-size: 18px;
  pointer-events: none;
}

.prof-input {
  width: 100%;
  height: 48px;
  padding: 0 14px 0 42px;
  border: 1.5px solid #E2DBD0;
  border-radius: 12px;
  font-size: 13.5px;
  font-family: inherit;
  color: #24312A;
  background: #ffffff;
  outline: none;
  transition: all 0.2s ease;
}

.prof-input:focus {
  border-color: #00AEEF;
  box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.18);
}

.prof-input.readonly {
  background: #f1f5f9;
  color: #57685F;
  cursor: not-allowed;
  border-color: #E2DBD0;
}

/* Submit Button */
.prof-submit-btn {
  width: 100%;
  height: 48px;
  background: linear-gradient(135deg, #00AEEF 0%, #173C2D 100%);
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
  box-shadow: 0 8px 20px rgba(22, 165, 222, 0.3);
  transition: all 0.2s ease;
  margin-top: 10px;
}

.prof-submit-btn:hover {
  background: #173C2D;
  transform: translateY(-1px);
}

/* Sidebar Widgets */
.prof-stat-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  background: #F7F3EA;
  border: 1px solid #E2DBD0;
  border-radius: 16px;
  margin-bottom: 12px;
}

.prof-stat-num {
  font-size: 26px;
  font-weight: 900;
  color: #24312A;
  line-height: 1;
}

.prof-stat-label {
  font-size: 11px;
  font-weight: 700;
  color: #57685F;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 4px;
}

.prof-nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  border-radius: 12px;
  color: #334155;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 700;
  transition: all 0.15s ease;
  margin-bottom: 4px;
}

.prof-nav-item:hover {
  background: rgba(0, 174, 239, 0.08);
  color: #00AEEF;
}

.prof-nav-item .material-symbols-outlined {
  font-size: 20px;
  color: #94a3b8;
}

.prof-nav-item:hover .material-symbols-outlined {
  color: #00AEEF;
}

.prof-danger-box {
  background: #fef2f2;
  border: 1px solid #fee2e2;
  border-radius: 20px;
  padding: 20px;
}

.prof-danger-title {
  font-size: 11px;
  font-weight: 800;
  color: #dc2626;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

@media (max-width: 900px) {
  .prof-main-container {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="prof-page-wrap">

  <!-- ── Sticky User Sub-Nav Hub ── -->
  <div style="background:#ffffff;border-bottom:1px solid #E2DBD0;position:sticky;top:72px;z-index:40;">
    <div style="max-width:1200px;margin:0 auto;padding:0 24px;display:flex;align-items:center;gap:8px;overflow-x:auto;scrollbar-width:none;">
      <a href="<?= url('dashboard') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;">grid_view</span>
        <span>Overview</span>
      </a>
      <a href="<?= url('user/my-bookings') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;">calendar_month</span>
        <span>My Bookings</span>
      </a>
      <a href="<?= url('my-wishlist') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;color:#ec4899;">favorite</span>
        <span>Saved Farms</span>
      </a>
      <a href="<?= url('user/profile') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#00AEEF;text-decoration:none;border-bottom:2px solid #00AEEF;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;">manage_accounts</span>
        <span>Profile &amp; Settings</span>
      </a>
      <a href="<?= url('contact') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;">support_agent</span>
        <span>Concierge Support</span>
      </a>
    </div>
  </div>

  <!-- ── Page Hero ── -->
  <div class="prof-hero-banner">
    <div class="prof-hero-container">
      <div>
        <div class="prof-tag">
          <span class="material-symbols-outlined" style="font-size:14px;">manage_accounts</span>
          <span>Account Settings</span>
        </div>
        <h1 class="prof-title">Account Center</h1>
        <p class="prof-subtitle">Manage your profile information, password, and contact preferences.</p>
      </div>

      <a href="<?= url('dashboard') ?>" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:#173C2D;text-decoration:none;background:#f0f9ff;padding:8px 16px;border-radius:12px;border:1px solid #D4E4DC;">
        <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span>
        <span>Back to Dashboard</span>
      </a>
    </div>
  </div>

  <div class="prof-main-container">

    <!-- ── Left Column: Forms ── -->
    <div>

      <!-- Flash feedback -->
      <?php if (isset($_SESSION['success'])): ?>
        <div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;padding:12px 16px;border-radius:14px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;margin-bottom:20px;">
          <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
          <span><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
        </div>
      <?php endif; ?>



      <!-- ── Profile Details Form ── -->
      <form action="<?= url('profile/update') ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateProfile(this)" class="prof-card">
        <div class="prof-card-header">
          <div class="prof-card-icon">
            <span class="material-symbols-outlined">person</span>
          </div>
          <div>
            <h2>Personal Information</h2>
            <p>Update your public name and direct contact details.</p>
          </div>
        </div>

        <div class="prof-card-body">

          <!-- Avatar Row -->
          <div class="prof-avatar-row">
            <img id="avatarPreview" class="prof-avatar-preview"
                 src="<?= !empty($user['profile_image']) ? asset('assets/images/uploads/profiles/' . htmlspecialchars($user['profile_image'])) : 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'User') . '&background=00AEEF&color=fff' ?>"
                 alt="Profile Photo">
            <div>
              <p style="font-size:13px;font-weight:800;color:#24312A;margin:0 0 6px;">Profile Photo</p>
              <label class="prof-upload-btn">
                <span class="material-symbols-outlined" style="font-size:16px;">upload</span>
                <span>Change Image</span>
                <input type="file" name="profile_image" accept="image/*" onchange="previewAvatar(event)" style="display:none;">
              </label>
              <p style="font-size:11px;color:#94a3b8;margin:6px 0 0;">JPG, PNG, or WEBP (Max 2MB)</p>
            </div>
          </div>

          <!-- Full Name -->
          <div class="prof-group">
            <label class="prof-label">Full Name</label>
            <div class="prof-input-wrap">
              <span class="material-symbols-outlined prof-icon">badge</span>
              <input type="text" name="name" id="nameInput" class="prof-input" value="<?= htmlspecialchars($user['name']) ?>" placeholder="e.g. Rahul Sharma" required>
            </div>
          </div>

          <!-- Email (Read-only) -->
          <div class="prof-group">
            <label class="prof-label">Email Address <span style="text-transform:none;color:#94a3b8;font-weight:500;">(Primary Account Identifier)</span></label>
            <div class="prof-input-wrap">
              <span class="material-symbols-outlined prof-icon">mail</span>
              <input type="email" name="email" class="prof-input readonly" value="<?= htmlspecialchars($user['email']) ?>" readonly>
            </div>
          </div>

          <!-- Phone Number -->
          <div class="prof-group">
            <label class="prof-label">Mobile Number</label>
            <div class="prof-input-wrap">
              <span class="material-symbols-outlined prof-icon">phone</span>
              <input type="tel" name="phone" id="phoneInput" class="prof-input" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
            </div>
          </div>

          <button type="submit" class="prof-submit-btn">
            <span class="material-symbols-outlined" style="font-size:18px;">save</span>
            <span>Save Profile Changes</span>
          </button>

        </div>
      </form>

      <!-- ── Password Security Form ── -->
      <form action="<?= url('profile/change-password') ?>" method="POST" onsubmit="return validatePassword(this)" class="prof-card">
        <div class="prof-card-header">
          <div class="prof-card-icon red">
            <span class="material-symbols-outlined">lock_reset</span>
          </div>
          <div>
            <h2>Security &amp; Password</h2>
            <p>Update your password to keep your account protected.</p>
          </div>
        </div>

        <div class="prof-card-body">
          <div class="prof-group">
            <label class="prof-label">Current Password</label>
            <div class="prof-input-wrap">
              <span class="material-symbols-outlined prof-icon">key</span>
              <input type="password" name="current_password" class="prof-input" placeholder="Enter current password" required>
            </div>
          </div>

          <div class="prof-group">
            <label class="prof-label">New Password</label>
            <div class="prof-input-wrap">
              <span class="material-symbols-outlined prof-icon">lock</span>
              <input type="password" name="new_password" id="newPass" class="prof-input" placeholder="At least 6 characters" required>
            </div>
          </div>

          <button type="submit" class="prof-submit-btn" style="background:#24312A;">
            <span class="material-symbols-outlined" style="font-size:18px;">shield</span>
            <span>Update Password</span>
          </button>
        </div>
      </form>

    </div>

    <!-- ── Right Column: Sidebar ── -->
    <div>

      <!-- Account Summary Card -->
      <div class="prof-card">
        <div class="prof-card-header">
          <div class="prof-card-icon">
            <span class="material-symbols-outlined">insights</span>
          </div>
          <div>
            <h2>Account Overview</h2>
          </div>
        </div>
        <div class="prof-card-body">
          <div class="prof-stat-box">
            <div>
              <div class="prof-stat-num"><?= sprintf("%02d", $booking_count) ?></div>
              <div class="prof-stat-label">Farm Stays</div>
            </div>
            <span class="material-symbols-outlined" style="font-size:28px;color:#173C2D;">villa</span>
          </div>

          <div class="prof-stat-box">
            <div>
              <div class="prof-stat-label" style="margin-top:0;margin-bottom:4px;">Status</div>
              <span style="display:inline-block;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:800;text-transform:uppercase;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">
                <?= htmlspecialchars(strtoupper($user['status'] ?? 'ACTIVE')) ?>
              </span>
            </div>
            <span class="material-symbols-outlined" style="font-size:28px;color:#22c55e;">verified</span>
          </div>

          <div class="prof-stat-box" style="margin-bottom:0;">
            <div>
              <div class="prof-stat-label" style="margin-top:0;margin-bottom:2px;">Member Since</div>
              <div style="font-size:14px;font-weight:800;color:#24312A;"><?= date('F Y', strtotime($user['created_at'] ?? 'now')) ?></div>
            </div>
            <span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">calendar_today</span>
          </div>
        </div>
      </div>

      <!-- Quick Navigation -->
      <div class="prof-card">
        <div class="prof-card-body" style="padding:16px;">
          <a href="<?= url('dashboard') ?>" class="prof-nav-item">
            <span class="material-symbols-outlined">grid_view</span>
            <span>Dashboard</span>
          </a>
          <a href="<?= url('user/my-bookings') ?>" class="prof-nav-item">
            <span class="material-symbols-outlined">calendar_month</span>
            <span>My Bookings</span>
          </a>
          <a href="<?= url('farmhouses') ?>" class="prof-nav-item">
            <span class="material-symbols-outlined">explore</span>
            <span>Explore Farms</span>
          </a>
          <a href="<?= url('contact') ?>" class="prof-nav-item">
            <span class="material-symbols-outlined">support_agent</span>
            <span>Concierge Support</span>
          </a>
        </div>
      </div>

      <!-- Sign Out Box -->
      <div class="prof-danger-box">
        <div class="prof-danger-title">Session Management</div>
        <a href="<?= url('logout') ?>" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:800;color:#dc2626;text-decoration:none;">
          <span class="material-symbols-outlined" style="font-size:18px;">logout</span>
          <span>Sign Out of Account</span>
        </a>
      </div>

    </div>

  </div>

</div>

<script>
function previewAvatar(event) {
  const reader = new FileReader();
  reader.onload = () => {
    document.getElementById('avatarPreview').src = reader.result;
  };
  if (event.target.files[0]) {
    reader.readAsDataURL(event.target.files[0]);
  }
}

document.getElementById('phoneInput').addEventListener('input', function () {
  this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
});

function validateProfile(form) {
  if (form.name.value.trim().length < 3) {
    alert('Name must be at least 3 letters.');
    return false;
  }
  if (form.phone.value.length !== 10) {
    alert('Phone number must be exactly 10 digits.');
    return false;
  }
  return true;
}

function validatePassword(form) {
  if (form.new_password.value.length < 6) {
    alert('New password must be at least 6 characters.');
    return false;
  }
  return true;
}
</script>

<?php include __DIR__ . "/../Includes/footer.php"; ?>