</main>

    <!-- Mobile Bottom Tab Navigation -->
    <!--<nav class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-stone-100 flex items-center justify-around h-20 px-2 py-3 z-50 rounded-t-3xl shadow-2xl">-->
    <!--    <button class="flex flex-col items-center gap-1 text-primary">-->
    <!--        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">grid_view</span>-->
    <!--        <span class="text-[10px] font-bold">Dashboard</span>-->
    <!--    </button>-->
    <!--    <button class="flex flex-col items-center gap-1 text-on-surface-variant">-->
    <!--        <span class="material-symbols-outlined">calendar_today</span>-->
    <!--        <span class="text-[10px] font-bold">Bookings</span>-->
    <!--    </button>-->
    <!--    <button class="flex flex-col items-center gap-1 text-on-surface-variant">-->
    <!--        <span class="material-symbols-outlined">person</span>-->
    <!--        <span class="text-[10px] font-bold">Account</span>-->
    <!--    </button>-->
    <!--</nav>-->

    <!-- Drawer Trigger JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('sidebar-open');
            overlay.classList.toggle('hidden');
        }
        document.getElementById('sidebar-overlay').onclick = toggleSidebar;
    </script>
</body>
</html>