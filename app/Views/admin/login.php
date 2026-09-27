<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Admin Login | Harvest &amp; Hearth</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
  
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-background": "#1b1d0e",
            "tertiary-fixed-dim": "#ffb68d",
            "surface-dim": "#dbdcc3",
            "on-primary-container": "#cbffc2",
            "tertiary": "#8d3f00",
            "surface-container-lowest": "#ffffff",
            "surface-container-low": "#f5f5dc",
            "on-error-container": "#93000a",
            "on-primary": "#ffffff",
            "inverse-surface": "#303221",
            "on-secondary": "#ffffff",
            "on-secondary-container": "#426e47",
            "on-error": "#ffffff",
            "surface-bright": "#fbfbe2",
            "secondary-fixed": "#bdefbe",
            "on-primary-fixed": "#002204",
            "primary-fixed": "#a3f69c",
            "primary-fixed-dim": "#88d982",
            "on-secondary-fixed-variant": "#24502c",
            "on-surface": "#1b1d0e",
            "secondary": "#3c6842",
            "tertiary-fixed": "#ffdbc9",
            "primary-container": "#2e7d32",
            "secondary-container": "#bdefbe",
            "surface": "#fbfbe2",
            "on-tertiary-fixed-variant": "#763400",
            "on-tertiary-fixed": "#321200",
            "background": "#fbfbe2",
            "error": "#ba1a1a",
            "on-secondary-fixed": "#002109",
            "error-container": "#ffdad6",
            "outline-variant": "#bfcaba",
            "inverse-primary": "#88d982",
            "tertiary-container": "#b25200",
            "surface-container": "#efefd7",
            "surface-container-highest": "#e4e4cc",
            "on-tertiary": "#ffffff",
            "on-surface-variant": "#40493d",
            "primary": "#0d631b",
            "on-primary-fixed-variant": "#005312",
            "inverse-on-surface": "#f2f2d9",
            "surface-variant": "#e4e4cc",
            "on-tertiary-container": "#ffeee6"
          },
          fontFamily: {
            "headline": ["Open Sans", "sans-serif"],
            "body": ["Open Sans", "sans-serif"],
            "label": ["Open Sans", "sans-serif"]
          },
          borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
        },
      },
    }
  </script>
  
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body, html, input, button, select, textarea, a, h1, h2, h3, p {
      font-family: 'Open Sans', sans-serif !important;
    }
    .bg-stone-pattern {
      background-color: #f5f5dc;
      background-image: radial-gradient(#dbdcc3 0.5px, transparent 0.5px);
      background-size: 24px 24px;
    }
  </style>
</head>

<body class="bg-surface text-on-background min-h-screen flex flex-col bg-stone-pattern selection:bg-primary selection:text-white">
  
  <!-- Header Component -->
  <header class="w-full flex justify-center items-center py-6 sm:py-8 shrink-0">
    <div class="font-headline tracking-tight text-xl sm:text-2xl font-bold text-green-900 text-center px-4">
      The Elevated Estate
    </div>
  </header>

  <!-- Main Content Wrapper -->
  <div class="flex-grow flex items-center justify-center w-full px-4 sm:px-6 py-4 sm:py-8">
    <main class="w-full max-w-md">
      <!-- Login Card -->
      <div class="bg-surface-container-lowest rounded-xl p-6 sm:p-8 md:p-12 border-none shadow-sm transition-all duration-300 w-full">
        <!-- Header Section -->
        <div class="mb-8 sm:mb-10 text-center">
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-on-background mb-2 sm:mb-3">Admin Login</h1>
          <p class="text-on-surface-variant leading-relaxed font-body text-sm sm:text-base">
            Access your dashboard to manage farmhouses and inquiries.
          </p>

        </div>
        
        <!-- Login Form -->
        <!-- Admin Login -->
        
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-outline uppercase tracking-tighter">System Access</h1>
        <p class="text-xs font-bold text-outline-variant uppercase tracking-widest">Administrator Portal</p>
    </div>

    <!-- Error Message Display -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 text-[10px] font-black uppercase tracking-widest border border-red-100">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= url('login-process') ?>" method="POST" class="space-y-6">
        <!-- ROLE IDENTIFIER -->
        <input type="hidden" name="role" value="admins">

        <div class="space-y-4">
            <div class="group">
                <label class="block text-[10px] font-black tracking-widest text-outline uppercase mb-2 ml-1">Admin Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">shield_person</span>
                    <input class="w-full pl-12 pr-4 py-4 rounded-2xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none text-sm font-medium" name="email" type="email" required />
                </div>
            </div>
            <div class="group">
                <label class="block text-[10px] font-black tracking-widest text-outline uppercase mb-2 ml-1">Secure Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">admin_panel_settings</span>
                    <input class="w-full pl-12 pr-4 py-4 rounded-2xl border-none bg-surface-container-low focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none text-sm font-medium" name="password" type="password" required />
                </div>
            </div>
        </div>

        <button class="w-full py-4 bg-black text-white font-bold rounded-2xl shadow-xl hover:bg-neutral-800 transition-all flex items-center justify-center gap-3" type="submit">
            <span>Verify & Enter</span>
            <span class="material-symbols-outlined text-xl">verified_user</span>
        </button>
    </form>
</div>
        <!-- Admin Note -->
        <div class="mt-8 sm:mt-10 pt-6 sm:pt-8 border-t border-surface-container text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container rounded-full w-max max-w-full overflow-hidden">
            <span class="material-symbols-outlined text-sm text-tertiary shrink-0">verified_user</span>
            <span class="text-[10px] sm:text-xs font-medium text-on-surface-variant font-label truncate">
              Only authorized admin can access this panel.
            </span>
          </div>
        </div>
      </div>
      
      <!-- Decorative Background Element -->
      <div class="mt-8 sm:mt-12 flex justify-center opacity-10">
        <img alt="Agrarian Motif" class="w-12 h-12 sm:w-16 sm:h-16" data-alt="Abstract wheat icon as a watermark" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxEL5tZb4Xv-lU69t881OfVoYKzvupQha4KDrN_I_1n-hWunsPhW22rb9LDQjXLwLuN78XBzwICqmCLUVoLXHvzPhAIdIKnmHGn5oBB5qtwXLpQjvunKaPdi5lLXaAdDIq-n7-0pbCfQObM6-dVWa-FQpzaG8mJtZ_S6oZ29vul9O7qo7HvsSjRlx0Np2RIgLi-kQuCFlaDsZl4WWo1_x2RNBOzH2VB6iVHYhvDBf6jNdbGEsvcY-0oaNCs_HXIDindp48teyP1uyr" />
      </div>
    </main>
  </div>

  <!-- Footer Component -->
  <footer class="w-full flex justify-center items-center py-6 sm:py-8 shrink-0 text-center px-4">
    <div class="font-body text-xs sm:text-sm tracking-wide uppercase text-green-900/40">
      © 2024 The Elevated Estate. All rights reserved.
    </div>
  </footer>

</body>
</html>