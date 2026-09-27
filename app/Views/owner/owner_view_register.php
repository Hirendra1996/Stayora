<?php
$pageTitle       = "List Your Farmhouse on FarmLelo | Earn Steady Rental Income";
$pageDescription = "Partner with FarmLelo to list your private farmhouse, pool villa, or holiday retreat. Reach verified guests, enjoy hassle-free bookings, and maximize your property earnings.";
$pageKeywords    = "list farmhouse, partner with farmlelo, rent out villa, farmhouse owner registration, list property india";
$canonicalUrl    = absolute_url('list_your_farm');
include __DIR__ . "/../Includes/header.php"; 
?>

<style>
/* ═══════════════════════════════════════════════════════════
   LIST YOUR FARM — LUXURY HOST PARTNERSHIP PORTAL
   ═══════════════════════════════════════════════════════════ */

.lyf-page-wrap {
  min-height: calc(100vh - 72px);
  padding-top: 72px;
  background: #F7F3EA;
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #24312A;
  overflow-x: hidden;
}

/* ── Hero Section ── */
.lyf-hero {
  position: relative;
  background: 
    linear-gradient(180deg, rgba(15, 23, 42, 0.80) 0%, rgba(15, 23, 42, 0.52) 45%, rgba(15, 23, 42, 0.90) 100%),
    url('<?= asset('assets/images/uploads/luxury_pool_hero.jpg') ?>') center 35% / cover no-repeat;
  color: #ffffff;
  padding: 85px 0 105px;
  overflow: hidden;
  box-shadow: inset 0 0 100px rgba(0,0,0,0.5);
}

.lyf-hero-mesh {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 80% 20%, rgba(22, 165, 222, 0.22) 0%, transparent 50%),
              radial-gradient(circle at 20% 80%, rgba(2, 132, 199, 0.15) 0%, transparent 50%);
  pointer-events: none;
}

.lyf-hero-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  position: relative;
  z-index: 2;
  text-align: center;
}

.lyf-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(15, 23, 42, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  color: #C9A227;
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  padding: 6px 18px;
  border-radius: 30px;
  margin-bottom: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.lyf-hero-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 48px;
  font-weight: 900;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin: 0 auto 18px;
  max-width: 920px;
  color: #ffffff;
  text-shadow: 0 2px 20px rgba(0,0,0,0.6);
}

.lyf-hero-title span {
  background: linear-gradient(135deg, #C9A227 0%, #1F4D3A 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.lyf-hero-sub {
  font-size: 17px;
  color: #E2DBD0;
  line-height: 1.6;
  max-width: 740px;
  margin: 0 auto 36px;
  font-weight: 500;
  text-shadow: 0 1px 10px rgba(0,0,0,0.5);
}

.lyf-hero-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 56px;
}

.lyf-hero-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #1F4D3A;
  color: #ffffff;
  font-size: 15px;
  font-weight: 800;
  padding: 14px 30px;
  border-radius: 16px;
  text-decoration: none;
  box-shadow: 0 10px 25px rgba(22, 165, 222, 0.4);
  transition: all 0.25s ease;
}

.lyf-hero-btn-primary:hover {
  background: #173C2D;
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(22, 165, 222, 0.5);
}

.lyf-hero-btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.1);
  border: 1.5px solid rgba(255, 255, 255, 0.25);
  color: #ffffff;
  font-size: 15px;
  font-weight: 700;
  padding: 14px 26px;
  border-radius: 16px;
  text-decoration: none;
  backdrop-filter: blur(8px);
  transition: all 0.25s ease;
}

.lyf-hero-btn-outline:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: #C9A227;
  color: #C9A227;
  transform: translateY(-2px);
}

.lyf-hero-btn-wa {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #25d366;
  color: #ffffff;
  font-size: 15px;
  font-weight: 800;
  padding: 14px 28px;
  border-radius: 16px;
  text-decoration: none;
  box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
  transition: all 0.25s ease;
}

.lyf-hero-btn-wa:hover {
  background: #1eb956;
  transform: translateY(-2px);
}

/* ── Hero Metric Pills ── */
.lyf-metrics-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  max-width: 1040px;
  margin: 0 auto;
}

.lyf-metric-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 20px 16px;
  text-align: center;
}

.lyf-metric-val {
  font-family: 'Epilogue', sans-serif;
  font-size: 26px;
  font-weight: 900;
  color: #C9A227;
  line-height: 1;
  margin-bottom: 6px;
}

.lyf-metric-lbl {
  font-size: 11.5px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ── Value Propositions (6 Pillars) ── */
.lyf-pillars-section {
  max-width: 1200px;
  margin: 80px auto;
  padding: 0 24px;
}

.lyf-section-heading {
  text-align: center;
  max-width: 680px;
  margin: 0 auto 48px;
}

.lyf-section-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #EAF1EB;
  color: #173C2D;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  padding: 4px 14px;
  border-radius: 20px;
  margin-bottom: 12px;
}

.lyf-section-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 34px;
  font-weight: 900;
  color: #24312A;
  margin: 0 0 10px;
  letter-spacing: -0.02em;
}

.lyf-section-sub {
  font-size: 15px;
  color: #57685F;
  margin: 0;
}

.lyf-pillars-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.lyf-pillar-card {
  background: #ffffff;
  border: 1px solid #E2DBD0;
  border-radius: 22px;
  padding: 30px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
  transition: all 0.25s ease;
}

.lyf-pillar-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
  border-color: #cbd5e1;
}

.lyf-pillar-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: #EAF1EB;
  color: #173C2D;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 18px;
}

.lyf-pillar-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #24312A;
  margin: 0 0 8px;
}

.lyf-pillar-desc {
  font-size: 13.5px;
  color: #57685F;
  line-height: 1.6;
  margin: 0;
}

/* ── 3-Step Process ── */
.lyf-steps-section {
  background: #ffffff;
  border-top: 1px solid #E2DBD0;
  border-bottom: 1px solid #E2DBD0;
  padding: 80px 0;
  margin-bottom: 80px;
}

.lyf-steps-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 24px;
}

.lyf-steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
  position: relative;
}

.lyf-step-card {
  text-align: center;
  position: relative;
}

.lyf-step-num-badge {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #1F4D3A;
  color: #ffffff;
  font-family: 'Epilogue', sans-serif;
  font-size: 22px;
  font-weight: 900;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  box-shadow: 0 8px 20px rgba(22, 165, 222, 0.35);
}

.lyf-step-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #24312A;
  margin: 0 0 8px;
}

.lyf-step-desc {
  font-size: 13.5px;
  color: #57685F;
  line-height: 1.6;
  margin: 0;
}

/* ── Host Protection & Callout Card ── */
.lyf-cta-box-section {
  max-width: 1100px;
  margin: 0 auto 90px;
  padding: 0 24px;
}

.lyf-cta-box {
  background: linear-gradient(135deg, #24312A 0%, #24312A 100%);
  color: #ffffff;
  border-radius: 28px;
  padding: 50px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
  position: relative;
  overflow: hidden;
}

.lyf-cta-box::before {
  content: '';
  position: absolute;
  top: -50px;
  right: -50px;
  width: 250px;
  height: 250px;
  background: radial-gradient(circle, rgba(22, 165, 222, 0.3) 0%, transparent 70%);
  pointer-events: none;
}

.lyf-cta-box-title {
  font-family: 'Epilogue', sans-serif;
  font-size: 28px;
  font-weight: 900;
  margin: 0 0 10px;
  color: #ffffff;
}

.lyf-cta-box-sub {
  font-size: 14.5px;
  color: #94a3b8;
  line-height: 1.6;
  max-width: 540px;
  margin: 0;
}

.lyf-cta-box-btns {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
  flex-shrink: 0;
  position: relative;
  z-index: 2;
}

/* ── FAQ Section ── */
.lyf-faq-section {
  max-width: 860px;
  margin: 0 auto 100px;
  padding: 0 24px;
}

.lyf-faq-item {
  background: #ffffff;
  border: 1px solid #E2DBD0;
  border-radius: 16px;
  margin-bottom: 12px;
  overflow: hidden;
  transition: all 0.2s ease;
}

.lyf-faq-q {
  padding: 18px 20px;
  font-size: 15px;
  font-weight: 800;
  color: #24312A;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: space-between;
  user-select: none;
}

.lyf-faq-q .material-symbols-outlined {
  color: #94a3b8;
  transition: transform 0.2s ease;
}

.lyf-faq-a {
  padding: 0 20px 18px;
  font-size: 13.5px;
  color: #57685F;
  line-height: 1.6;
  display: none;
}

.lyf-faq-item.active .lyf-faq-a {
  display: block;
}

.lyf-faq-item.active .lyf-faq-q .material-symbols-outlined {
  transform: rotate(180deg);
  color: #1F4D3A;
}

/* Responsive */
@media (max-width: 1024px) {
  .lyf-hero-title { font-size: 38px; }
  .lyf-metrics-row { grid-template-columns: repeat(2, 1fr); }
  .lyf-pillars-grid { grid-template-columns: repeat(2, 1fr); }
  .lyf-cta-box { flex-direction: column; text-align: center; }
  .lyf-cta-box-btns { justify-content: center; }
}

@media (max-width: 640px) {
  .lyf-hero-title { font-size: 30px; }
  .lyf-metrics-row { grid-template-columns: 1fr; }
  .lyf-pillars-grid { grid-template-columns: 1fr; }
  .lyf-steps-grid { grid-template-columns: 1fr; }
  .lyf-cta-box { padding: 32px 24px; }
}
</style>

<div class="lyf-page-wrap">

  <!-- ── 1. Hero Section ── -->
  <section class="lyf-hero">
    <div class="lyf-hero-mesh"></div>
    <div class="lyf-hero-container">
      <div class="lyf-hero-badge">
        <span class="material-symbols-outlined" style="font-size:16px;">villa</span>
        <span>Farmlelo Host Partnership</span>
      </div>

      <h1 class="lyf-hero-title">
        Turn Your Luxury Farmhouse Into a <span>High-Yield Asset</span>
      </h1>

      <p class="lyf-hero-sub">
        Join India's premier network of private pool villas, nature retreats, and estates. Welcome verified families, corporate guests, and maximize your weekend rental revenue with zero upfront listing fees.
      </p>

      <div class="lyf-hero-actions">
        <a href="<?= url('owner/login') ?>" class="lyf-hero-btn-primary">
          <span>Start Free Listing</span>
          <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
        </a>

        <a href="<?= url('why-choose-us') ?>" class="lyf-hero-btn-outline">
          <span class="material-symbols-outlined" style="font-size:18px;">military_tech</span>
          <span>Why Choose Us</span>
        </a>

        <a href="https://wa.me/919876543210?text=Hello%20Farmlelo%20Team,%20I%20want%20to%20list%20my%20farmhouse" target="_blank" class="lyf-hero-btn-wa">
          <svg style="width:18px;height:18px;fill:currentColor;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
          <span>Chat on WhatsApp</span>
        </a>
      </div>

      <!-- Metric Pills -->
      <div class="lyf-metrics-row">
        <div class="lyf-metric-card">
          <div class="lyf-metric-val">₹2.5L+</div>
          <div class="lyf-metric-lbl">Avg. Monthly Host Earnings</div>
        </div>
        <div class="lyf-metric-card">
          <div class="lyf-metric-val">100%</div>
          <div class="lyf-metric-lbl">Verified Screened Guests</div>
        </div>
        <div class="lyf-metric-card">
          <div class="lyf-metric-val">₹0</div>
          <div class="lyf-metric-lbl">Upfront Listing Fee</div>
        </div>
        <div class="lyf-metric-card">
          <div class="lyf-metric-val">24/7</div>
          <div class="lyf-metric-lbl">Dedicated Host Manager</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 2. Why List With Farmlelo (6 Pillars) ── -->
  <section class="lyf-pillars-section">
    <div class="lyf-section-heading">
      <div class="lyf-section-tag">
        <span class="material-symbols-outlined" style="font-size:14px;">verified</span>
        <span>Host Advantages</span>
      </div>
      <h2 class="lyf-section-title">Why Farmhouse Owners Choose Farmlelo</h2>
      <p class="lyf-section-sub">We treat your estate like our own. Here is why over 500+ luxury property owners trust our platform.</p>
    </div>

    <div class="lyf-pillars-grid">
      <!-- 1 -->
      <div class="lyf-pillar-card">
        <div class="lyf-pillar-icon">
          <span class="material-symbols-outlined" style="font-size:26px;">shield</span>
        </div>
        <h3 class="lyf-pillar-title">Strict Guest Screening</h3>
        <p class="lyf-pillar-desc">We enforce strict government ID verification and check party intent before approving guests to safeguard your premises.</p>
      </div>

      <!-- 2 -->
      <div class="lyf-pillar-card">
        <div class="lyf-pillar-icon">
          <span class="material-symbols-outlined" style="font-size:26px;">account_balance</span>
        </div>
        <h3 class="lyf-pillar-title">Guaranteed On-Time Payouts</h3>
        <p class="lyf-pillar-desc">Enjoy transparent, direct-to-bank settlements before guest check-in. Zero delayed payouts or hidden deductions.</p>
      </div>

      <!-- 3 -->
      <div class="lyf-pillar-card">
        <div class="lyf-pillar-icon">
          <span class="material-symbols-outlined" style="font-size:26px;">photo_camera</span>
        </div>
        <h3 class="lyf-pillar-title">Complimentary Pro Photoshoot</h3>
        <p class="lyf-pillar-desc">Our media team conducts professional HD photography and drone footage to showcase your property in breathtaking detail.</p>
      </div>

      <!-- 4 -->
      <div class="lyf-pillar-card">
        <div class="lyf-pillar-icon">
          <span class="material-symbols-outlined" style="font-size:26px;">chat</span>
        </div>
        <h3 class="lyf-pillar-title">Direct WhatsApp Inquiries</h3>
        <p class="lyf-pillar-desc">Connect directly with prospective guests on WhatsApp for quick inquiries, custom menu planning, and prompt confirmations.</p>
      </div>

      <!-- 5 -->
      <div class="lyf-pillar-card">
        <div class="lyf-pillar-icon">
          <span class="material-symbols-outlined" style="font-size:26px;">event_available</span>
        </div>
        <h3 class="lyf-pillar-title">100% Calendar Freedom</h3>
        <p class="lyf-pillar-desc">Block dates anytime for personal family use with one click. You retain total control over your pricing and availability.</p>
      </div>

      <!-- 6 -->
      <div class="lyf-pillar-card">
        <div class="lyf-pillar-icon">
          <span class="material-symbols-outlined" style="font-size:26px;">support_agent</span>
        </div>
        <h3 class="lyf-pillar-title">Dedicated Host Concierge</h3>
        <p class="lyf-pillar-desc">Your dedicated Host Manager coordinates inquiries, assists with guest check-in arrangements, and resolves questions 24/7.</p>
      </div>
    </div>
  </section>

  <!-- ── 3. Simple 3-Step Process ── -->
  <section class="lyf-steps-section">
    <div class="lyf-steps-container">
      <div class="lyf-section-heading">
        <div class="lyf-section-tag">
          <span class="material-symbols-outlined" style="font-size:14px;">rocket_launch</span>
          <span>Simple Onboarding</span>
        </div>
        <h2 class="lyf-section-title">How It Works in 3 Easy Steps</h2>
        <p class="lyf-section-sub">Get your farmhouse listed and live in less than 48 hours.</p>
      </div>

      <div class="lyf-steps-grid">
        <div class="lyf-step-card">
          <div class="lyf-step-num-badge">1</div>
          <h3 class="lyf-step-title">Sign In &amp; Register</h3>
          <p class="lyf-step-desc">Create your host account in seconds and submit your property details through our portal.</p>
        </div>

        <div class="lyf-step-card">
          <div class="lyf-step-num-badge">2</div>
          <h3 class="lyf-step-title">Verification &amp; Shoot</h3>
          <p class="lyf-step-desc">Our team visits for a quick physical audit, verifies amenities, and captures high-res media.</p>
        </div>

        <div class="lyf-step-card">
          <div class="lyf-step-num-badge">3</div>
          <h3 class="lyf-step-title">Go Live &amp; Earn</h3>
          <p class="lyf-step-desc">Your listing is published across our network and starts receiving verified booking requests.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 4. Callout Box with Direct Login Action ── -->
  <section class="lyf-cta-box-section">
    <div class="lyf-cta-box">
      <div>
        <div class="lyf-hero-badge" style="margin-bottom:12px;">
          <span class="material-symbols-outlined" style="font-size:14px;">security</span>
          <span>Zero Risk. Total Control.</span>
        </div>
        <h2 class="lyf-cta-box-title">Ready to Host Elite Guests?</h2>
        <p class="lyf-cta-box-sub">
          Start listing your property today. Sign in to your Farmlelo account to submit your property details, or chat with our host onboarding specialist.
        </p>
      </div>

      <div class="lyf-cta-box-btns">
        <a href="<?= url('owner/login') ?>" class="lyf-hero-btn-primary">
          <span>Start Free Listing</span>
          <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
        </a>

        <a href="<?= url('why-choose-us') ?>" class="lyf-hero-btn-outline">
          <span class="material-symbols-outlined" style="font-size:18px;">military_tech</span>
          <span>Why Choose Us</span>
        </a>

        <a href="https://wa.me/919876543210?text=Hello%20Farmlelo,%20I%20want%20to%20list%20my%20farmhouse" target="_blank" class="lyf-hero-btn-wa">
          <svg style="width:18px;height:18px;fill:currentColor;" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.405.836 3.125 1.352 4.953 1.353 5.174 0 9.389-4.215 9.391-9.389.001-2.507-.974-4.864-2.747-6.638s-4.132-2.747-6.638-2.747c-5.176 0-9.391 4.215-9.393 9.39-.001 1.893.562 3.736 1.629 5.308l-.999 3.646 3.737-.981-.132-.084z"/></svg>
          <span>Chat on WhatsApp</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ── 5. Host FAQs ── -->
  <section class="lyf-faq-section">
    <div class="lyf-section-heading">
      <div class="lyf-section-tag">
        <span class="material-symbols-outlined" style="font-size:14px;">help</span>
        <span>Frequently Asked Questions</span>
      </div>
      <h2 class="lyf-section-title">Got Questions About Hosting?</h2>
      <p class="lyf-section-sub">Here are answers to the most common questions from farmhouse owners.</p>
    </div>

    <div class="lyf-faq-item active">
      <div class="lyf-faq-q" onclick="toggleFaq(this)">
        <span>How much does it cost to list my farmhouse on Farmlelo?</span>
        <span class="material-symbols-outlined">expand_more</span>
      </div>
      <div class="lyf-faq-a">
        Listing your property on Farmlelo is 100% free! There are zero registration fees, listing charges, or annual renewals. We charge a minimal transparent commission only when you successfully receive a paid booking.
      </div>
    </div>

    <div class="lyf-faq-item">
      <div class="lyf-faq-q" onclick="toggleFaq(this)">
        <span>How and when do I receive payouts for bookings?</span>
        <span class="material-symbols-outlined">expand_more</span>
      </div>
      <div class="lyf-faq-a">
        Payouts are processed directly to your registered bank account or UPI before or upon guest check-in. You never have to chase payments or worry about delays.
      </div>
    </div>

    <div class="lyf-faq-item">
      <div class="lyf-faq-q" onclick="toggleFaq(this)">
        <span>Can I use my farmhouse for personal family weekends?</span>
        <span class="material-symbols-outlined">expand_more</span>
      </div>
      <div class="lyf-faq-a">
        Yes, absolutely! You have 100% control over your property calendar. You can block any dates for personal family use directly from your dashboard or by notifying your Host Concierge.
      </div>
    </div>

    <div class="lyf-faq-item">
      <div class="lyf-faq-q" onclick="toggleFaq(this)">
        <span>How does Farmlelo ensure guests are verified?</span>
        <span class="material-symbols-outlined">expand_more</span>
      </div>
      <div class="lyf-faq-a">
        Every booking request requires government photo ID verification and mandatory confirmation of guest count and purpose of stay (family, birthday, corporate offsite) to ensure rowdy elements are prevented.
      </div>
    </div>

    <div class="lyf-faq-item">
      <div class="lyf-faq-q" onclick="toggleFaq(this)">
        <span>Who handles guest check-in and housekeeping?</span>
        <span class="material-symbols-outlined">expand_more</span>
      </div>
      <div class="lyf-faq-a">
        Your on-site caretaker or host team handles on-ground key handover. Our platform coordinates guest timings, directions, menu preferences, and advance payment collections.
      </div>
    </div>
  </section>

</div>

<script>
function toggleFaq(el) {
  const item = el.closest('.lyf-faq-item');
  if (item) {
    item.classList.toggle('active');
  }
}
</script>

<?php
include __DIR__ . "/../Includes/footer.php"; 
?>
