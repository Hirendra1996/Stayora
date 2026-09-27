</main><!-- /page content -->

        <!-- ═══════════════════════════════════════════════════════ -->
        <!--  MOBILE BOTTOM NAVIGATION                               -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <?php
        if (!function_exists('mobOwnerActive')) {
            function mobOwnerActive(string $path): string {
                $uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
                return (stripos($uri, $path) !== false) ? 'text-primary' : 'text-slate-400';
            }
        }
        ?>
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-slate-200 flex items-center justify-around px-2 h-[64px] shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">

            <!-- Home / Dashboard -->
            <a href="<?= url('owner/dashboard') ?>" class="flex flex-col items-center gap-1 px-3 py-1 text-decoration-none <?= mobOwnerActive('owner/dashboard') ?>">
                <span class="material-symbols-outlined text-[22px]">grid_view</span>
                <span class="text-[10px] font-extrabold uppercase tracking-wide">Home</span>
            </a>

            <!-- Bookings -->
            <a href="<?= url('owner/bookings') ?>" class="flex flex-col items-center gap-1 px-3 py-1 text-decoration-none <?= mobOwnerActive('owner/bookings') ?>">
                <span class="material-symbols-outlined text-[22px]">calendar_month</span>
                <span class="text-[10px] font-extrabold uppercase tracking-wide">Bookings</span>
            </a>

            <!-- Add Farm Floating Button -->
            <a href="<?= url('owner/farmhouses/add') ?>" class="flex flex-col items-center -mt-6 text-decoration-none">
                <div class="w-13 h-13 bg-primary text-white rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30 border-4 border-white active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-[24px]">add</span>
                </div>
            </a>

            <!-- Farms -->
            <a href="<?= url('owner/farmhouses') ?>" class="flex flex-col items-center gap-1 px-3 py-1 text-decoration-none <?= (mobOwnerActive('owner/farmhouses') && !mobOwnerActive('owner/farmhouses/add')) ? 'text-primary' : 'text-slate-400' ?>">
                <span class="material-symbols-outlined text-[22px]">villa</span>
                <span class="text-[10px] font-extrabold uppercase tracking-wide">Farms</span>
            </a>

            <!-- Profile -->
            <a href="<?= url('owner/profile') ?>" class="flex flex-col items-center gap-1 px-3 py-1 text-decoration-none <?= mobOwnerActive('owner/profile') ?>">
                <span class="material-symbols-outlined text-[22px]">person</span>
                <span class="text-[10px] font-extrabold uppercase tracking-wide">Profile</span>
            </a>

        </nav>

    </div><!-- /main column -->
</div><!-- /flex layout -->

<script>
    /* Set page title in topbar dynamically */
    (function(){
        const map = {
            'owner/dashboard':        'Dashboard Overview',
            'owner/bookings':         'Guest Bookings & Stays',
            'owner/offline-bookings': 'Offline Bookings & Dossiers',
            'owner/earnings':         'Earnings & Payouts Ledger',
            'owner/inquiries':        'Property Inquiries & Leads',
            'owner/kyc':              'KYC & Ownership Verification',
            'owner/farmhouses/add':   'List New Farmhouse',
            'owner/farmhouses/create':'List New Farmhouse',
            'owner/farmhouses/edit':  'Edit Farmhouse Listing',
            'owner/farmhouses/show':  'Farmhouse Showcase',
            'owner/farmhouses':       'My Farmhouses',
            'owner/profile':          'Profile & Security',
        };
        const uri = window.location.pathname.toLowerCase();
        const el  = document.getElementById('page-title');
        if (!el) return;
        for (const [path, label] of Object.entries(map)) {
            if (uri.includes(path.toLowerCase())) { el.textContent = label; break; }
        }
    })();
</script>
</body>
</html>