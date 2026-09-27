<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Farm Lelo - Owner Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="icon" type="image/webp" href="<?= asset('assets/images/logo.webp') ?>"/>
    <link rel="shortcut icon" href="<?= asset('assets/images/logo.webp') ?>"/>
    <link rel="apple-touch-icon" href="<?= asset('assets/images/logo.webp') ?>"/>
    
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        headline: ['"Epilogue"', 'sans-serif']
                    },
                    colors: {
                        accent: '#C9A227',
                        secondary: '#6F8F72',
                        primary: '#1F4D3A',
                        'primary-hover': '#173C2D',
                        'on-surface': '#24312A',
                        'on-surface-variant': '#57685F',
                        brand: {
                            50:  '#f0f9ff',
                            100: '#EAF1EB',
                            200: '#D4E4DC',
                            300: '#C9A227',
                            400: '#C9A227',
                            500: '#1F4D3A',
                            600: '#173C2D',
                            700: '#133225',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { 
          -webkit-tap-highlight-color: transparent; 
          font-family: 'Plus Jakarta Sans', sans-serif;
          background-color: #F7F3EA;
          color: #24312A;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Rich Text Description Editor Formatting ── */
        #desc-rich-editor,
        .desc-rich-editor {
            line-height: 1.65;
            color: #24312A;
        }
        #desc-rich-editor ul,
        .desc-rich-editor ul {
            list-style-type: disc !important;
            list-style-position: outside !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            display: block !important;
        }
        #desc-rich-editor ol,
        .desc-rich-editor ol {
            list-style-type: decimal !important;
            list-style-position: outside !important;
            padding-left: 1.5rem !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
            display: block !important;
        }
        #desc-rich-editor li,
        .desc-rich-editor li {
            display: list-item !important;
            list-style: inherit !important;
            margin-bottom: 0.25rem !important;
        }
        #desc-rich-editor p,
        .desc-rich-editor p {
            margin-bottom: 0.5rem !important;
        }
        #desc-rich-editor b,
        #desc-rich-editor strong,
        .desc-rich-editor b,
        .desc-rich-editor strong {
            font-weight: 700 !important;
        }
        #desc-rich-editor i,
        #desc-rich-editor em,
        .desc-rich-editor i,
        .desc-rich-editor em {
            font-style: italic !important;
        }

        /* ── Sidebar Styles ── */
        .owner-sidebar {
            background: linear-gradient(180deg, #24312A 0%, #24312A 100%);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.06);
        }

        .owner-nav-link { 
            position: relative; 
            transition: all .2s ease; 
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 14px;
            color: #94a3b8;
            font-size: 13.5px;
            font-weight: 700;
            text-decoration: none;
        }

        .owner-nav-link:hover { 
            background: rgba(255, 255, 255, 0.08); 
            color: #ffffff;
        }

        .owner-nav-link.active {
            background: #1F4D3A;
            color: #ffffff;
            box-shadow: 0 4px 16px rgba(22, 165, 222, 0.4);
        }

        .owner-nav-link.active .material-symbols-outlined {
            color: #ffffff;
        }

        /* ── Topbar Glass ── */
        .owner-topbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid #E2DBD0;
        }

        /* ── Mobile Bottom Nav ── */
        .mob-nav-active { color: #1F4D3A; }
        .mob-nav-active .mob-pip { opacity: 1; }
        .mob-pip { opacity: 0; }
    </style>
</head>
<body class="bg-slate-50 antialiased overflow-x-hidden">

<?php
/* ── Determine active nav item securely ── */
if (!function_exists('isOwnerRouteActive')) {
    function isOwnerRouteActive(string $path): bool {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $currentUri = strtok((string)$uri, '?') ?: '';
        return (stripos($currentUri, $path) !== false);
    }
}

/* ── Profile / owner data ── */
$owner        = $owner ?? [];
$ownerName    = htmlspecialchars($_SESSION['user_name'] ?? 'Host Partner');
$profileImage = !empty($_SESSION['profile_image'])
    ? asset('assets/images/uploads/avatars/' . htmlspecialchars($_SESSION['profile_image']))
    : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_name'] ?? 'Owner') . '&background=16a5de&color=fff&bold=true&size=128';
?>

<div class="flex h-screen w-full relative z-10 overflow-hidden">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  SIDEBAR  (desktop only)                                    -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <aside class="hidden lg:flex w-[260px] flex-shrink-0 flex-col owner-sidebar relative z-40 overflow-hidden">

        <!-- Ambient glow blobs -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-sky-500/10 rounded-full blur-[50px] -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>

        <!-- ── Brand Logo Header ── -->
        <div class="h-[72px] flex items-center px-6 border-b border-white/10 relative z-10 flex-shrink-0">
            <a href="<?= url('home') ?>" class="flex items-center gap-3 text-decoration-none group">
                <img src="<?= asset('assets/images/logo.webp') ?>" alt="Farmlelo" class="h-9 w-auto object-contain">
                <div>
                    <span class="font-headline font-black text-lg text-white leading-none tracking-tight block">
                        Farm<span class="text-primary">Lelo</span>
                    </span>
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-[0.18em] block mt-0.5">Host Workspace</span>
                </div>
            </a>
        </div>

        <!-- ── Navigation Links ── -->
        <nav class="flex-1 overflow-y-auto py-6 px-4 relative z-10 space-y-1.5">
            <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2">Management</p>

            <!-- Dashboard -->
            <a href="<?= url('owner/dashboard') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/dashboard') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">grid_view</span>
                <span>Dashboard Overview</span>
            </a>

            <!-- Bookings -->
            <a href="<?= url('owner/bookings') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/bookings') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                <span>Online Bookings</span>
            </a>

            <!-- Offline Bookings -->
            <a href="<?= url('owner/offline-bookings') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/offline-bookings') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">person_book</span>
                <span>Offline Bookings</span>
            </a>

            <!-- Date Locks -->
            <a href="<?= url('owner/blocked-dates') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/blocked-dates') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">event_busy</span>
                <span>Date Locks</span>
            </a>

            <!-- Earnings & Payouts -->
            <a href="<?= url('owner/earnings') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/earnings') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                <span>Earnings &amp; Payouts</span>
            </a>

            <!-- Inquiries & Leads -->
            <a href="<?= url('owner/inquiries') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/inquiries') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">forum</span>
                <span>Guest Inquiries</span>
            </a>

            <!-- Farmhouses -->
            <a href="<?= url('owner/farmhouses') ?>" class="owner-nav-link <?= (isOwnerRouteActive('owner/farmhouses') && !isOwnerRouteActive('owner/farmhouses/add')) ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">villa</span>
                <span>My Farmhouses</span>
            </a>

            <!-- Add Farmhouse -->
            <a href="<?= url('owner/farmhouses/add') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/farmhouses/add') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>List New Farm</span>
            </a>

            <!-- KYC & Verification -->
            <a href="<?= url('owner/kyc') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/kyc') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">verified_user</span>
                <span>KYC &amp; Verification</span>
            </a>

            <div class="pt-5 pb-1">
                <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2">Preferences</p>
            </div>

            <!-- Profile Settings -->
            <a href="<?= url('owner/profile') ?>" class="owner-nav-link <?= isOwnerRouteActive('owner/profile') ? 'active' : '' ?>">
                <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                <span>Profile &amp; Security</span>
            </a>

            <!-- Why Choose Us -->
            <a href="<?= url('why-choose-us') ?>" class="owner-nav-link" target="_blank">
                <span class="material-symbols-outlined text-[20px]">military_tech</span>
                <span>Why Choose Us</span>
            </a>
        </nav>

        <!-- ── Owner Profile Card at Sidebar Bottom ── -->
        <div class="p-4 border-t border-white/10 relative z-10 flex-shrink-0">
            <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-white/5 border border-white/10">
                <img src="<?= $profileImage ?>" alt="Profile" class="w-10 h-10 rounded-xl object-cover border border-white/20 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-[13px] truncate leading-tight"><?= $ownerName ?></p>
                    <p class="text-primary text-[10px] font-bold uppercase tracking-wider mt-0.5">Verified Host</p>
                </div>
            </div>

            <a href="<?= url('owner/logout') ?>" class="mt-2.5 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500 border border-red-500/20 hover:border-red-500 text-red-400 hover:text-white transition-all text-xs font-bold">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                <span>Sign Out</span>
            </a>
        </div>
    </aside>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MAIN COLUMN                                                -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden bg-slate-50">

        <!-- ── Desktop Topbar ── -->
        <header class="hidden lg:flex h-[72px] owner-topbar flex-shrink-0 items-center justify-between px-8 relative z-30 shadow-xs">

            <!-- Left: Breadcrumb / Status -->
            <div class="flex items-center gap-2">
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Host Workspace</span>
                <span class="text-slate-300 text-xs">•</span>
                <span id="page-title" class="text-sm font-black text-slate-800 tracking-tight">Dashboard Overview</span>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-3">
                <!-- Add New Farm Button -->
                <a href="<?= url('owner/farmhouses/add') ?>"
                   class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-4 py-2.5 rounded-xl text-xs font-extrabold shadow-sm hover:shadow-md transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>List New Farm</span>
                </a>

                <div class="w-px h-6 bg-slate-200 mx-1"></div>

                <!-- In-App Notification Bell (F56) -->
                <a href="<?= url('notifications') ?>" class="relative w-9 h-9 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 shadow-2xs flex items-center justify-center text-slate-700 transition" title="Notifications">
                    <span class="material-symbols-outlined text-[19px]">notifications</span>
                    <span id="owner-notif-badge" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-sky-500 rounded-full border-2 border-white"></span>
                </a>

                <!-- Profile Link -->
                <a href="<?= url('owner/profile') ?>"
                   class="flex items-center gap-2.5 bg-white hover:bg-slate-100 pl-2 pr-4 py-1.5 rounded-xl border border-slate-200 shadow-2xs transition-all">
                    <img src="<?= $profileImage ?>" alt="Profile" class="w-8 h-8 rounded-lg object-cover">
                    <div class="text-left">
                        <p class="text-xs font-black text-slate-800 leading-tight"><?= $ownerName ?></p>
                        <p class="text-[10px] text-primary font-bold uppercase tracking-wider">Host Partner</p>
                    </div>
                </a>

                <!-- Logout Button -->
                <a href="<?= url('owner/logout') ?>"
                   class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-500 text-red-500 hover:text-white border border-red-100 hover:border-red-500 flex items-center justify-center transition-all shadow-2xs"
                   title="Sign Out">
                    <span class="material-symbols-outlined text-[18px]">power_settings_new</span>
                </a>
            </div>
        </header>

        <!-- ── Mobile Topbar ── -->
        <header class="lg:hidden sticky top-0 z-40 flex items-center justify-between h-[64px] px-4 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-2xs flex-shrink-0">
            <a href="<?= url('owner/dashboard') ?>" class="flex items-center gap-2.5 text-decoration-none">
                <img src="<?= asset('assets/images/logo-dark.webp') ?>" alt="Farmlelo" class="h-8 w-auto object-contain">
                <span class="font-headline font-black text-base text-slate-900 leading-none">
                    Farm<span class="text-primary">Lelo</span>
                </span>
            </a>

            <div class="flex items-center gap-2">
                <a href="<?= url('owner/farmhouses/add') ?>" class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                </a>
                <a href="<?= url('owner/profile') ?>" class="w-8 h-8 rounded-lg overflow-hidden border border-slate-200 shadow-2xs">
                    <img src="<?= $profileImage ?>" alt="Profile" class="w-full h-full object-cover">
                </a>
                <a href="<?= url('owner/logout') ?>" class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-500">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                </a>
            </div>
        </header>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  PAGE CONTENT AREA                                      -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden relative z-10 pb-24 lg:pb-8">