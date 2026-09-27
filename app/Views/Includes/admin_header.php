<?php
// header.php — Include at top of every page
// Usage: <?php include 'header.php';
// Set $pageTitle before including, e.g.: $pageTitle = "Dashboard";
if(session_status() === PHP_SESSION_NONE) session_start();
$pageTitle  = $pageTitle  ?? 'Farmlelo';
$activePage = $activePage ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="robots" content="noindex, nofollow, noarchive"/>
    <title><?php echo htmlspecialchars($pageTitle); ?> | Farmlelo Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="<?= asset('assets/images/logo.webp') ?>"/>
    <link rel="shortcut icon" href="<?= asset('assets/images/logo.webp') ?>"/>
    <link rel="apple-touch-icon" href="<?= asset('assets/images/logo.webp') ?>"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    sky: '#1F4D3A',
                    'sky-d': '#173C2D',
                    'sky-l': '#E8F0EC',
                    'sky-xl': '#F7F3EA',
                    ink: '#24312A',
                    muted: '#57685F',
                    border: '#E2DBD0',
                    surface: '#F7F3EA',
                    white:    '#FFFFFF',
                    danger:   '#E03E3E',
                    success:  '#1AAD6B',
                },
                fontFamily: {
                    sans:    ['Open Sans', 'sans-serif'],
                    display: ['Open Sans', 'sans-serif'],
                    body:    ['Open Sans', 'sans-serif'],
                    headline:['Open Sans', 'sans-serif'],
                },
            }
        }
    };
    </script>

    <style>
        /* ── Global Reset & Font ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body, input, button, select, textarea, a, h1, h2, h3, h4, h5, h6, p, div, label, table, td, th {
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background: #F7F3EA;
            color: #24312A;
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── Material Symbols & Icons Protection ── */
        .material-symbols-outlined,
        span.material-symbols-outlined,
        i.material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .fa, .fas, .far, .fal, .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }

        /* ── Rich Text Description Editor & Dossier Preview Formatting ── */
        #desc-rich-editor,
        .desc-rich-editor,
        #prop_dossier_description {
            line-height: 1.65;
            color: #24312A;
        }
        #desc-rich-editor ul,
        .desc-rich-editor ul,
        #prop_dossier_description ul {
            list-style-type: disc !important;
            list-style-position: outside !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            display: block !important;
        }
        #desc-rich-editor ol,
        .desc-rich-editor ol,
        #prop_dossier_description ol {
            list-style-type: decimal !important;
            list-style-position: outside !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            display: block !important;
        }
        #desc-rich-editor li,
        .desc-rich-editor li,
        #prop_dossier_description li {
            display: list-item !important;
            list-style: inherit !important;
            margin-bottom: 0.25rem !important;
        }
        #desc-rich-editor p,
        .desc-rich-editor p,
        #prop_dossier_description p {
            margin-bottom: 0.5rem !important;
        }
        #desc-rich-editor b,
        #desc-rich-editor strong,
        .desc-rich-editor b,
        .desc-rich-editor strong,
        #prop_dossier_description b,
        #prop_dossier_description strong {
            font-weight: 700 !important;
        }
        #desc-rich-editor i,
        #desc-rich-editor em,
        .desc-rich-editor i,
        .desc-rich-editor em,
        #prop_dossier_description i,
        #prop_dossier_description em {
            font-style: italic !important;
        }

        /* ══════════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════════ */
        #fl-sidebar {
            width: 260px;
            flex-shrink: 0;
            background: #fff;
            border-right: 1px solid #E2DBD0;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 50;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }

        /* ── Logo block ── */
        .fl-sidebar-logo {
            padding: 1.25rem 1.5rem 1.1rem;
            border-bottom: 1px solid #E6F6FD;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .fl-sidebar-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: .5rem;
            flex-shrink: 0;
        }
        .fl-logo-wordmark {
            font-family: 'Open Sans', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            color: #1F4D3A;
            letter-spacing: -.02em;
            line-height: 1.1;
        }
        .fl-logo-sub {
            font-size: .6rem;
            color: #57685F;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        /* ── Nav labels & links ── */
        .fl-nav-label {
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #57685F;
            padding: 1rem 1.5rem .35rem;
        }
        .fl-nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .575rem 1.25rem;
            margin: .1rem .75rem;
            border-radius: .65rem;
            font-size: .82rem;
            font-weight: 500;
            color: #57685F;
            text-decoration: none;
            transition: background .15s, color .15s;
            position: relative;
        }
        .fl-nav-link:hover {
            background: #E8F0EC;
            color: #173C2D;
        }
        .fl-nav-link.active {
            background: linear-gradient(135deg, #E8F0EC 0%, #D4E4DC 100%);
            color: #1F4D3A;
            font-weight: 600;
        }
        .fl-nav-link.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: #1F4D3A;
            border-radius: 0 3px 3px 0;
        }
        .fl-nav-link .material-symbols-outlined { font-size: 20px; flex-shrink: 0; }

        /* ── Sidebar footer CTA ── */
        .fl-sidebar-footer {
            margin-top: auto;
            padding: 1rem 1rem 1.25rem;
            border-top: 1px solid #E6F6FD;
        }
        .fl-add-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            padding: .75rem 1rem;
            background: linear-gradient(135deg, #1F4D3A 0%, #173C2D 100%);
            color: #fff;
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: .75rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            border-radius: .75rem;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(22,165,222,.35);
            transition: box-shadow .2s, transform .15s;
        }
        .fl-add-btn:hover {
            box-shadow: 0 6px 22px rgba(22,165,222,.5);
            transform: translateY(-1px);
        }
        .fl-add-btn .material-symbols-outlined { font-size: 18px; }

        /* ══════════════════════════════════════════
           HEADER
        ══════════════════════════════════════════ */
        #fl-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid #E2DBD0;
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
        }
        .fl-header-title {
            font-family: 'Open Sans', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: #24312A;
            white-space: nowrap;
        }
        .fl-spacer { flex: 1; }

        /* Search bar */
        .fl-search {
            display: flex;
            align-items: center;
            gap: .5rem;
            background: #F7F3EA;
            border: 1.5px solid #E2DBD0;
            border-radius: 2rem;
            padding: .38rem 1rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .fl-search:focus-within {
            border-color: #1F4D3A;
            box-shadow: 0 0 0 3px rgba(22,165,222,.12);
        }
        .fl-search input {
            background: none;
            border: none;
            outline: none;
            font-family: 'Open Sans', sans-serif;
            font-size: .82rem;
            color: #24312A;
            width: 160px;
        }
        .fl-search input::placeholder { color: #57685F; }
        .fl-search .material-symbols-outlined { font-size: 18px; color: #57685F; }

        /* Notification button */
        .fl-notif-btn {
            position: relative;
            cursor: pointer;
            padding: .45rem;
            border-radius: 50%;
            border: 1.5px solid #E2DBD0;
            background: #F7F3EA;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, border-color .15s;
        }
        .fl-notif-btn:hover { background: #E8F0EC; border-color: #1F4D3A; }
        .fl-notif-btn .material-symbols-outlined { font-size: 20px; color: #57685F; }
        .fl-notif-badge {
            position: absolute;
            top: 1px; right: 1px;
            width: 8px; height: 8px;
            background: #E03E3E;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Profile chip */
        .fl-profile-chip {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .35rem .9rem .35rem .35rem;
            border-radius: 2rem;
            border: 1.5px solid #E2DBD0;
            cursor: pointer;
            transition: border-color .15s, background .15s, box-shadow .15s;
            position: relative;
            background: #fff;
        }
        .fl-profile-chip:hover {
            border-color: #1F4D3A;
            background: #F0FAFF;
            box-shadow: 0 2px 10px rgba(22,165,222,.12);
        }
        .fl-profile-chip img {
            width: 32px; height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #E6F6FD;
        }
        .fl-profile-chip .fl-profile-name {
            font-size: .8rem;
            font-weight: 600;
            color: #24312A;
            max-width: 110px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .fl-profile-chip .material-symbols-outlined { font-size: 16px; color: #57685F; }

        /* Dropdown */
        .fl-profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 210px;
            background: #fff;
            border: 1px solid #E2DBD0;
            border-radius: .9rem;
            box-shadow: 0 10px 40px rgba(11,31,46,.14);
            padding: .5rem 0;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px) scale(.97);
            transition: opacity .2s, transform .2s;
            transform-origin: top right;
        }
        .fl-profile-dropdown.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        /* Dropdown header (user info) */
        .fl-dropdown-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1.1rem 1rem;
        }
        .fl-dropdown-header img {
            width: 38px; height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #E6F6FD;
        }
        .fl-dropdown-header-info .fl-dropdown-user-name {
            font-size: .85rem;
            font-weight: 700;
            color: #24312A;
            line-height: 1.2;
        }
        .fl-dropdown-header-info .fl-dropdown-user-role {
            font-size: .7rem;
            color: #57685F;
            font-weight: 500;
        }

        .fl-dropdown-item {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .6rem 1.1rem;
            font-family: 'Open Sans', sans-serif;
            font-size: .82rem;
            font-weight: 500;
            color: #24312A;
            text-decoration: none;
            transition: background .12s;
        }
        .fl-dropdown-item:hover { background: #F7F3EA; }
        .fl-dropdown-item .material-symbols-outlined { font-size: 18px; color: #57685F; }
        .fl-dropdown-item.danger { color: #E03E3E; }
        .fl-dropdown-item.danger .material-symbols-outlined { color: #E03E3E; }
        .fl-dropdown-item.danger:hover { background: #FEF2F2; }
        .fl-dropdown-divider { height: 1px; background: #E8F0EC; margin: .35rem 0; }

        /* ── Mobile hamburger ── */
        #fl-menu-btn {
            display: none;
            cursor: pointer;
            padding: .45rem;
            border-radius: .5rem;
            border: 1.5px solid #E2DBD0;
            background: #F7F3EA;
            align-items: center;
            justify-content: center;
            transition: background .15s;
            flex-shrink: 0;
        }
        #fl-menu-btn:hover { background: #E8F0EC; }

        /* ── Sidebar overlay ── */
        #fl-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(11,31,46,.45);
            z-index: 45;
            backdrop-filter: blur(3px);
        }

        /* ── Main content wrapper ── */
        #fl-main-wrap {
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        #fl-content { flex: 1; }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 768px) {
            #fl-sidebar { transform: translateX(-100%); }
            #fl-sidebar.open { transform: translateX(0); }
            #fl-menu-btn { display: flex; }
            #fl-main-wrap { margin-left: 0; }
            .fl-search { display: none; }
            .fl-header-title { font-size: .95rem; }
        }

        /* ══════════════════════════════════════════
           PAGE-LEVEL UTILITIES
        ══════════════════════════════════════════ */
        .fl-page-header { padding: 1.5rem 1.5rem .5rem; }
        .fl-page-header h1 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: #24312A;
        }
        .fl-page-header p { font-size: .875rem; color: #57685F; margin-top: .2rem; }

        .fl-card {
            background: #fff;
            border: 1px solid #E2DBD0;
            border-radius: 1rem;
            padding: 1.5rem;
        }
    </style>
</head>
<body>

<!-- ── Sidebar Overlay ── -->
<div id="fl-overlay"></div>

<!-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ -->
<nav id="fl-sidebar">

    <!-- Logo -->
    <div class="fl-sidebar-logo">
        <img src="<?= asset('assets/images/logo.webp') ?>" alt="Farmlelo Logo"/>
        <div>
            <div class="fl-logo-wordmark">Farmlelo</div>
            <div class="fl-logo-sub">Admin Portal</div>
        </div>
    </div>

    <!-- Nav links -->
    <div style="overflow-y:auto;flex:1;padding-top:.5rem;">

        <div class="fl-nav-label">Main</div>
        <a href="<?= url('admin/dashboard') ?>" class="fl-nav-link <?php echo $activePage==='dashboard' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='dashboard'?1:0; ?>">dashboard</span>
            Dashboard
        </a>
        <a href="<?= url('admin/users') ?>" class="fl-nav-link <?php echo $activePage==='users' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='users'?1:0; ?>">manage_accounts</span>
            Users
        </a>
        <a href="<?= url('admin/owners') ?>" class="fl-nav-link <?php echo $activePage==='owners' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='owners'?1:0; ?>">group</span>
            Owners
        </a>
        <a href="<?= url('admin/managefarmhouses') ?>" class="fl-nav-link <?php echo $activePage==='properties' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='properties'?1:0; ?>">fence</span>
            Properties
        </a>
        <a href="<?= url('admin/property-types') ?>" class="fl-nav-link <?php echo $activePage==='property_types' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='property_types'?1:0; ?>">category</span>
            Property Types
        </a>

        <div class="fl-nav-label">Operations</div>
        <a href="<?= url('admin/booking-requests') ?>" class="fl-nav-link <?php echo $activePage==='bookings' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='bookings'?1:0; ?>">calendar_today</span>
            Bookings
        </a>
        <a href="<?= url('admin/payments') ?>" class="fl-nav-link <?php echo $activePage==='payments' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='payments'?1:0; ?>">payments</span>
            Payment Verification
        </a>
        <a href="<?= url('admin/blocked-dates') ?>" class="fl-nav-link <?php echo $activePage==='blocked_dates' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='blocked_dates'?1:0; ?>">event_busy</span>
            Date Locks
        </a>
        <a href="<?= url('admin/inquiries') ?>" class="fl-nav-link <?php echo $activePage==='inquiries' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='inquiries'?1:0; ?>">forum</span>
            Property Inquiries
        </a>
        <a href="<?= url('admin/reviews') ?>" class="fl-nav-link <?php echo $activePage==='reviews' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='reviews'?1:0; ?>">stars</span>
            Guest Reviews
        </a>
        <a href="<?= url('admin/contact-inquiries') ?>" class="fl-nav-link <?php echo $activePage==='contact_inquiries' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='contact_inquiries'?1:0; ?>">contact_mail</span>
            Contact Messages
        </a>
        <a href="<?= url('admin/managerules') ?>" class="fl-nav-link <?php echo $activePage==='rules' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='rules'?1:0; ?>">gavel</span>
            Important Rules
        </a>
        <a href="<?= url('admin/manageamenities') ?>" class="fl-nav-link <?php echo $activePage==='amenities' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='amenities'?1:0; ?>">hotel_class</span>
            Amenities
        </a>
        <a href="<?= url('admin/coupons') ?>" class="fl-nav-link <?php echo $activePage==='coupons' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='coupons'?1:0; ?>">confirmation_number</span>
            Coupons & Discounts
        </a>

        <div class="fl-nav-label">Settings</div>
        <a href="<?= url('admin/settings') ?>" class="fl-nav-link <?php echo $activePage==='settings' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?php echo $activePage==='settings'?1:0; ?>">settings</span>
            System Settings
        </a>

    </div>

    <!-- Sidebar footer CTA -->
    <div class="fl-sidebar-footer">
        <a href="<?= url('admin/addfarm') ?>" class="fl-add-btn">
            <span class="material-symbols-outlined">add_home_work</span>
            Add New Estate
        </a>
    </div>
</nav>

<!-- ══════════════════════════════════════════
     MAIN WRAPPER
══════════════════════════════════════════ -->
<div id="fl-main-wrap">

    <!-- ── Top Header ── -->
    <header id="fl-header">

        <!-- Hamburger (mobile only) -->
        <button id="fl-menu-btn" aria-label="Open menu">
            <span class="material-symbols-outlined" style="font-size:22px;color:#57685F">menu</span>
        </button>

        <!-- Page title -->
        <div class="fl-header-title"><?php echo htmlspecialchars($pageTitle); ?></div>

        <div class="fl-spacer"></div>


        <!-- Notifications Bell (F56) -->
        <a href="<?= url('notifications') ?>" style="position:relative;width:38px;height:38px;border-radius:10px;border:1px solid #E2DBD0;background:#fff;display:flex;align-items:center;justify-content:center;color: #24312A;text-decoration:none;transition:background .15s;margin-right:8px;" title="Notifications">
            <span class="material-symbols-outlined" style="font-size:20px;color: #57685F;">notifications</span>
            <span style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#1F4D3A;"></span>
        </a>

        <!-- Profile chip -->
        <div class="fl-profile-chip" id="fl-profile-btn" role="button" aria-haspopup="true" aria-expanded="false">
            <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'Admin'); ?>&background=16A5DE&color=fff&size=64"
                alt="Avatar"
            />
            <span class="fl-profile-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
            <span class="material-symbols-outlined">expand_more</span>

            <!-- Dropdown -->
            <div class="fl-profile-dropdown" id="fl-profile-dropdown" role="menu">

                <!-- User info header -->
                <div class="fl-dropdown-header">
                    <img
                        src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'Admin'); ?>&background=16A5DE&color=fff&size=64"
                        alt="Avatar"
                    />
                    <div class="fl-dropdown-header-info">
                        <div class="fl-dropdown-user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></div>
                        <div class="fl-dropdown-user-role">Administrator</div>
                    </div>
                </div>

                <div class="fl-dropdown-divider"></div>

                <a href="<?= url('admin/profile') ?>" class="fl-dropdown-item" role="menuitem">
                    <span class="material-symbols-outlined">person</span>
                    My Profile
                </a>
                <a href="<?= url('admin/settings') ?>" class="fl-dropdown-item" role="menuitem">
                    <span class="material-symbols-outlined">settings</span>
                    System Settings
                </a>
                <div class="fl-dropdown-divider"></div>

                <a href="<?= url('logout') ?>" class="fl-dropdown-item danger" role="menuitem">
                    <span class="material-symbols-outlined">logout</span>
                    Sign Out
                </a>
            </div>
        </div>

    </header>

    <!-- Page content starts here -->
    <div id="fl-content" class="p-4 md:p-6">