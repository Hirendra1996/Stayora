</div><!-- /#fl-content -->

    <!-- ══════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════ -->
    <footer id="fl-footer">
        <style>
        #fl-footer {
            background: #fff;
            border-top: 1px solid #E2DBD0;
            padding: 1.1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            font-family: 'Open Sans', sans-serif;
        }
        .fl-footer-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .fl-footer-brand img {
            width: 22px;
            height: 22px;
            object-fit: contain;
            border-radius: 4px;
        }
        .fl-footer-brand-text {
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: .85rem;
            color: #1F4D3A;
        }
        .fl-footer-copy {
            font-size: .72rem;
            color: #57685F;
        }
        .fl-footer-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .fl-footer-links a {
            font-size: .72rem;
            color: #57685F;
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }
        .fl-footer-links a:hover { color: #1F4D3A; }
        .fl-footer-status {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            color: #1AAD6B;
            font-weight: 600;
        }
        .fl-status-dot {
            width: 7px;
            height: 7px;
            background: #1AAD6B;
            border-radius: 50%;
            animation: fl-pulse 2s infinite;
        }
        @keyframes fl-pulse { 0%,100%{opacity:1;} 50%{opacity:.35;} }
        @media(max-width: 640px) { .fl-footer-links { display: none; } }
        </style>

        <!-- Left: Brand -->
        <div class="fl-footer-brand">
            <img src="<?= asset('assets/images/logo.webp') ?>" alt="Farmlelo"/>
            <span class="fl-footer-brand-text">Farmlelo</span>
            <span class="fl-footer-copy">&copy; <?php echo date('Y'); ?> Admin Panel</span>
        </div>

        <!-- Center: Links -->
        <div class="fl-footer-links">
            <a href="<?= url('admin/dashboard') ?>">Dashboard</a>
            <a href="<?= url('admin/managefarmhouses') ?>">Properties</a>
            <a href="<?= url('admin/booking-requests') ?>">Bookings</a>
            <a href="<?= url('admin/profile') ?>">Settings</a>
        </div>

        <!-- Right: Status -->
        <div class="fl-footer-status">
            <span class="fl-status-dot"></span>
            All systems operational
        </div>
    </footer>

</div><!-- /#fl-main-wrap -->

<!-- ══════════════════════════════════════════
     SHARED SCRIPTS
══════════════════════════════════════════ -->
<script>
(function () {
    // ── Sidebar toggle (mobile) ──
    const sidebar = document.getElementById('fl-sidebar');
    const overlay = document.getElementById('fl-overlay');
    const menuBtn = document.getElementById('fl-menu-btn');

    function openSidebar()  { sidebar.classList.add('open');    overlay.style.display = 'block'; }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.style.display = 'none';  }

    menuBtn?.addEventListener('click', openSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // ── Profile dropdown ──
    const profileBtn  = document.getElementById('fl-profile-btn');
    const profileDrop = document.getElementById('fl-profile-dropdown');

    profileBtn?.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = profileDrop?.classList.toggle('open');
        profileBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    document.addEventListener('click', function () {
        profileDrop?.classList.remove('open');
        profileBtn?.setAttribute('aria-expanded', 'false');
    });

    // Close sidebar on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) closeSidebar();
    });
})();
</script>
</body>
</html>