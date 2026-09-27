<?php 
include __DIR__ . "/../Includes/header.php"; 
use App\Helpers\CryptoHelper;
$userData     = $userData ?? ['name' => 'Guest', 'profile_image' => ''];
$userRequests = $userRequests ?? []; 
$currentStatus = $_GET['status'] ?? 'all';
?>

<style>
.bk-page-wrap {
  min-height: calc(100vh - 72px);
  padding-top: 72px;
  background: #F7F3EA;
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #24312A;
}

/* Page Hero */
.bk-hero-banner {
  background: #ffffff;
  border-bottom: 1px solid #E2DBD0;
  padding: 36px 0;
}

.bk-hero-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.bk-tag {
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

.bk-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 32px;
  font-weight: 900;
  color: #24312A;
  margin: 0 0 4px;
  letter-spacing: -0.02em;
}

.bk-subtitle {
  font-size: 14px;
  color: #57685F;
  margin: 0;
}

/* Main Container */
.bk-main-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 24px 60px;
}

/* Filter & Search Bar */
.bk-filter-bar {
  background: #ffffff;
  border: 1px solid #E2DBD0;
  border-radius: 20px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 28px;
  flex-wrap: wrap;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.bk-status-tabs {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.bk-status-tab {
  padding: 8px 16px;
  border-radius: 12px;
  font-size: 12.5px;
  font-weight: 700;
  text-decoration: none;
  color: #57685F;
  background: #F7F3EA;
  border: 1px solid #E2DBD0;
  transition: all 0.2s ease;
}

.bk-status-tab:hover {
  background: #f1f5f9;
  color: #24312A;
}

.bk-status-tab.active {
  background: #00AEEF;
  color: #ffffff;
  border-color: #00AEEF;
  box-shadow: 0 4px 14px rgba(0, 174, 239, 0.35);
}

.bk-search-form {
  position: relative;
  min-width: 240px;
  flex: 1;
  max-width: 320px;
}

.bk-search-input {
  width: 100%;
  height: 40px;
  padding: 0 14px 0 38px;
  border: 1.5px solid #E2DBD0;
  border-radius: 12px;
  font-size: 13px;
  font-family: inherit;
  background: #F7F3EA;
  outline: none;
}

.bk-search-input:focus {
  border-color: #00AEEF;
  background: #ffffff;
}

.bk-search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 18px;
}

/* Booking Cards Grid */
.bk-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 24px;
}

.bk-card {
  background: #ffffff;
  border: 1px solid #E2DBD0;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
  transition: all 0.25s ease;
}

.bk-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

.bk-card-media {
  position: relative;
  height: 190px;
  overflow: hidden;
  background: #24312A;
}

.bk-card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.bk-card:hover .bk-card-img {
  transform: scale(1.05);
}

.bk-status-pill {
  position: absolute;
  top: 14px;
  right: 14px;
  padding: 5px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  backdrop-filter: blur(8px);
}

.bk-status-pill.pending {
  background: rgba(254, 240, 138, 0.95);
  color: #854d0e;
}

.bk-status-pill.approved {
  background: rgba(220, 252, 231, 0.95);
  color: #166534;
}

.bk-status-pill.completed {
  background: rgba(224, 242, 254, 0.95);
  color: #133225;
}

.bk-status-pill.rejected,
.bk-status-pill.cancelled {
  background: rgba(254, 226, 226, 0.95);
  color: #991b1b;
}

.bk-card-content {
  padding: 22px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  flex: 1;
}

.bk-farm-name {
  font-family: 'Epilogue', sans-serif;
  font-size: 18px;
  font-weight: 900;
  color: #24312A;
  margin: 0 0 4px;
  line-height: 1.3;
}

.bk-location {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12.5px;
  font-weight: 600;
  color: #57685F;
  margin: 0;
}

.bk-dates-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background: #F7F3EA;
  border: 1px solid #E2DBD0;
  border-radius: 14px;
  padding: 10px 14px;
}

.bk-date-col {
  display: flex;
  flex-direction: column;
}

.bk-date-label {
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  color: #94a3b8;
  letter-spacing: 0.5px;
}

.bk-date-val {
  font-size: 13px;
  font-weight: 800;
  color: #24312A;
  margin-top: 2px;
}

.bk-actions-row {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid #f1f5f9;
}

.bk-details-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #00AEEF 0%, #173C2D 100%);
  color: #ffffff;
  font-size: 12.5px;
  font-weight: 800;
  padding: 9px 18px;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 4px 14px rgba(0, 174, 239, 0.3);
}

.bk-details-btn:hover {
  background: linear-gradient(135deg, #173C2D 0%, #133225 100%);
  transform: translateY(-1px);
}

.bk-cancel-btn {
  background: transparent;
  border: 1.5px solid #fca5a5;
  color: #dc2626;
  font-size: 12px;
  font-weight: 700;
  padding: 8px 14px;
  border-radius: 12px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
}

.bk-cancel-btn:hover {
  background: #fee2e2;
}

.bk-empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 70px 20px;
  background: #ffffff;
  border: 2px dashed #E2DBD0;
  border-radius: 24px;
}

@media (max-width: 640px) {
  .bk-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="bk-page-wrap">

  <!-- ── Sticky User Sub-Nav Hub ── -->
  <div style="background:#ffffff;border-bottom:1px solid #E2DBD0;position:sticky;top:72px;z-index:40;">
    <div style="max-width:1200px;margin:0 auto;padding:0 24px;display:flex;align-items:center;gap:8px;overflow-x:auto;scrollbar-width:none;">
      <a href="<?= url('dashboard') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;">grid_view</span>
        <span>Overview</span>
      </a>
      <a href="<?= url('user/my-bookings') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#00AEEF;text-decoration:none;border-bottom:2px solid #00AEEF;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;">calendar_month</span>
        <span>My Bookings</span>
      </a>
      <a href="<?= url('my-wishlist') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
        <span class="material-symbols-outlined" style="font-size:18px;color:#ec4899;">favorite</span>
        <span>Saved Farms</span>
      </a>
      <a href="<?= url('user/profile') ?>" style="display:inline-flex;align-items:center;gap:8px;padding:14px 16px;font-size:13px;font-weight:700;color:#57685F;text-decoration:none;border-bottom:2px solid transparent;white-space:nowrap;">
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
  <div class="bk-hero-banner">
    <div class="bk-hero-container">
      <div>
        <div class="bk-tag">
          <span class="material-symbols-outlined" style="font-size:14px;">calendar_month</span>
          <span>Reservation History</span>
        </div>
        <h1 class="bk-title">My Bookings</h1>
        <p class="bk-subtitle">View and monitor the confirmation status of your farmhouse stays.</p>
      </div>

      <a href="<?= url('farmhouses') ?>" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg, #00AEEF 0%, #173C2D 100%);color:#ffffff;font-size:13px;font-weight:800;padding:10px 20px;border-radius:14px;text-decoration:none;box-shadow:0 4px 14px rgba(0,174,239,0.35);">
        <span class="material-symbols-outlined" style="font-size:18px;">add_circle</span>
        <span>Book Another Farm</span>
      </a>
    </div>
  </div>

  <div class="bk-main-container">

    <!-- ── Filter & Search Bar ── -->
    <div class="bk-filter-bar">
      <div class="bk-status-tabs">
        <a href="?status=all<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="bk-status-tab <?= ($currentStatus === 'all') ? 'active' : '' ?>">All Requests</a>
        <a href="?status=approved<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="bk-status-tab <?= ($currentStatus === 'approved') ? 'active' : '' ?>">Confirmed</a>
        <a href="?status=completed<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="bk-status-tab <?= ($currentStatus === 'completed') ? 'active' : '' ?>">Completed</a>
        <a href="?status=pending<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="bk-status-tab <?= ($currentStatus === 'pending') ? 'active' : '' ?>">Pending</a>
        <a href="?status=cancelled<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="bk-status-tab <?= ($currentStatus === 'cancelled') ? 'active' : '' ?>">Cancelled</a>
        <a href="?status=rejected<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="bk-status-tab <?= ($currentStatus === 'rejected') ? 'active' : '' ?>">Rejected</a>
      </div>

      <form method="GET" action="" class="bk-search-form">
        <?php if ($currentStatus !== 'all'): ?>
          <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
        <?php endif; ?>
        <span class="material-symbols-outlined bk-search-icon">search</span>
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search by farm title…" class="bk-search-input">
      </form>
    </div>

    <!-- ── Bookings Grid ── -->
    <div class="bk-grid">
      <?php if (empty($userRequests)): ?>
        <div class="bk-empty-state">
          <span class="material-symbols-outlined" style="font-size:54px;color:#94a3b8;margin-bottom:14px;display:block;">event_busy</span>
          <h3 style="font-size:20px;font-weight:900;color:#24312A;margin:0 0 6px;">No bookings found</h3>
          <p style="font-size:13.5px;color:#57685F;max-width:360px;margin:0 auto 20px;">
            <?= ($currentStatus !== 'all') ? 'No booking requests found under the selected status filter.' : 'You have not submitted any farmhouse booking requests yet.' ?>
          </p>
          <a href="<?= url('farmhouses') ?>" class="bk-details-btn" style="display:inline-flex;">
            <span class="material-symbols-outlined" style="font-size:16px;">explore</span>
            <span>Explore Farmhouses</span>
          </a>
        </div>
      <?php else: ?>
        <?php foreach ($userRequests as $request):
          $status = strtolower($request['status'] ?? 'pending');
          $encFarmId = CryptoHelper::encrypt($request['farmhouse_id'] ?? 0);
          $start = isset($request['start_date']) ? date('d M Y', strtotime($request['start_date'])) : 'N/A';
          $end   = isset($request['end_date'])   ? date('d M Y', strtotime($request['end_date']))   : 'N/A';
          $reqImg = farmhouse_img_url($request['main_image'] ?? null, 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?q=80&w=400');
        ?>
          <div class="bk-card">
            <div class="bk-card-media">
              <img src="<?= $reqImg ?>" 
                   alt="<?= htmlspecialchars($request['farm_title'] ?? 'Farmhouse') ?>" class="bk-card-img">
              <div class="bk-status-pill <?= $status ?>">
                ● <?= htmlspecialchars(ucfirst($status)) ?>
              </div>
            </div>

            <div class="bk-card-content">
              <div>
                <span style="background:#EAF1EB;color:#133225;font-size:10px;font-weight:800;padding:2px 8px;border-radius:12px;text-transform:uppercase;margin-bottom:6px;display:inline-block;">
                  <?= htmlspecialchars($request['farm_category'] ?? 'Farmhouse') ?>
                </span>
                <h3 class="bk-farm-name"><?= htmlspecialchars($request['farm_title'] ?? 'Farmhouse') ?></h3>
                <?php if (!empty($request['location'])): ?>
                  <p class="bk-location">
                    <span class="material-symbols-outlined" style="font-size:16px;color:#00AEEF;">location_on</span>
                    <span><?= htmlspecialchars($request['location']) ?></span>
                  </p>
                <?php endif; ?>
              </div>

              <div class="bk-dates-row">
                <div class="bk-date-col">
                  <span class="bk-date-label">Check In</span>
                  <span class="bk-date-val"><?= $start ?></span>
                </div>
                <div class="bk-date-col" style="border-left:1px solid #E2DBD0;padding-left:14px;">
                  <span class="bk-date-label">Check Out</span>
                  <span class="bk-date-val"><?= $end ?></span>
                </div>
              </div>

              <?php if (!empty($request['message'])): ?>
                <div style="font-size:12px;color:#57685F;background:#F7F3EA;padding:8px 12px;border-radius:10px;border:1px solid #E2DBD0;font-style:italic;">
                  "<?= htmlspecialchars($request['message']) ?>"
                </div>
              <?php endif; ?>

              <div class="bk-actions-row">
                <?php if ($status === 'pending'): ?>
                  <form action="<?= url('user/my-bookings') ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking request?')">
                    <input type="hidden" name="request_id" value="<?= $request['id'] ?? '' ?>">
                    <button type="submit" class="bk-cancel-btn">
                      <span class="material-symbols-outlined" style="font-size:15px;">close</span>
                      <span>Cancel</span>
                    </button>
                  </form>
                <?php else: ?>
                  <span style="font-size:11px;font-weight:700;color:#94a3b8;">
                    Ref #<?= htmlspecialchars($request['id'] ?? '') ?>
                  </span>
                <?php endif; ?>

                <a href="<?= url('user/booking?id=' . ($request['id'] ?? 0)) ?>" class="bk-details-btn">
                  <span>View Details</span>
                  <span class="material-symbols-outlined" style="font-size:16px;">arrow_forward</span>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>

</div>

<?php include __DIR__ . "/../Includes/footer.php"; ?>