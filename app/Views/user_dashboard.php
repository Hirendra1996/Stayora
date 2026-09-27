<!DOCTYPE html>
<html class="light scroll-smooth" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard | Harvest & Hearth</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0d631b",
                        "primary-container": "#2e7d32",
                        "on-primary": "#ffffff",
                        "background": "#fbfbe2",
                        "surface": "#fbfbe2",
                        "surface-container": "#efefd7",
                        "surface-container-low": "#f5f5dc",
                        "surface-container-high": "#eaead1",
                        "on-background": "#1b1d0e",
                        "on-surface": "#1b1d0e",
                        "on-surface-variant": "#40493d",
                        "tertiary": "#8d3f00",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        "headline": ["Epilogue", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                        "label": ["Inter", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .filled-icon { font-variation-settings: 'FILL' 1; }
        body { overflow-x: hidden; }
        
        /* Mobile Sidebar Transition */
        #sidebar { transition: transform 0.3s ease-in-out; }
        .sidebar-open #sidebar { transform: translateX(0); }
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%); z-index: 100; }
        }
    </style>
</head>

<body class="bg-background font-body text-on-background">

    <!-- Mobile AppBar -->
    <header class="lg:hidden fixed top-0 w-full z-[60] bg-white/90 backdrop-blur-md px-6 py-4 flex justify-between items-center border-b border-stone-200">
        <h1 class="text-sm font-black text-primary font-headline uppercase tracking-widest">H & H</h1>
        <button onclick="toggleSidebar()" class="p-2 bg-stone-100 rounded-lg text-primary">
            <span class="material-symbols-outlined" id="menu-icon">menu</span>
        </button>
    </header>

    <!-- Overlay for mobile sidebar -->
    <div id="overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-black/40 z-[90] lg:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="h-screen w-64 fixed left-0 top-0 overflow-y-auto bg-stone-50 lg:bg-stone-100 border-r border-stone-200 lg:border-none flex flex-col p-6 shadow-2xl lg:shadow-none">
        <div class="mb-10 pt-2 lg:pt-0">
            <h1 class="text-lg font-black text-primary font-headline uppercase tracking-widest">Harvest & Hearth</h1>
        </div>
        
        <div class="flex items-center gap-4 mb-10 p-3 bg-white lg:bg-transparent rounded-2xl">
            <img class="w-12 h-12 rounded-full border-2 border-primary/20 object-cover" alt="User" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBIHgnoqbZEWSkQ0FzUYZgp7zA8nuDyRAj3tjtVQloIK8l299mIlQWizji--npN5iMeGS6mn5CnqpGpIZOjB67GolQpcHxQj3E3Kb5v20-ASJyH8yIIMnhgD4Fj912g-guCYChoY0noo7FAa10Khekji_dvkHZHS_M7n9uCKPYlSeW8UyFzmq3uSOp4Ijq__S2LRNCyhZdNQH79YDAZ0eH8z2fvJ2WXrlstoUPSUhWAsnzZu0ybxYs2iu_fRSdTc1zJYLgJCcCiqD7E"/>
            <div>
                <p class="text-[10px] font-black uppercase text-stone-400">Welcome</p>
                <p class="font-headline font-bold text-on-background">Vishal S.</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 text-primary bg-primary/10 font-bold rounded-xl" href="#">
                <span class="material-symbols-outlined filled-icon">dashboard</span>
                <span class="font-label text-sm">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-stone-500 hover:bg-stone-200 transition-all rounded-xl" href="#">
                <span class="material-symbols-outlined">favorite</span>
                <span class="font-label text-sm">Wishlist</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-stone-500 hover:bg-stone-200 transition-all rounded-xl" href="#">
                <span class="material-symbols-outlined">history</span>
                <span class="font-label text-sm">Recently Viewed</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-stone-500 hover:bg-stone-200 transition-all rounded-xl" href="#">
                <span class="material-symbols-outlined">person</span>
                <span class="font-label text-sm">Profile</span>
            </a>
        </nav>

        <div class="mt-auto border-t border-stone-200 pt-4">
            <a class="flex items-center gap-3 px-4 py-3 text-error font-bold rounded-xl hover:bg-error/5 transition-all" href="#">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label text-sm">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="lg:ml-64 min-h-screen p-4 sm:p-8 lg:p-16 pt-24 lg:pt-16 transition-all duration-300">
        <!-- Welcome Message -->
        <header class="mb-10">
            <h2 class="text-3xl sm:text-4xl lg:text-6xl font-headline font-black tracking-tight text-on-background mb-4">
                Welcome back, Vishal!
            </h2>
            <p class="text-lg sm:text-xl text-on-surface-variant font-body leading-relaxed max-w-2xl opacity-80">
                Ready for your next agrarian escape? We've curated fresh harvest hideaways just for you.
            </p>
        </header>

        <!-- Stats Grid -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-16 md:mb-20">
            <div class="bg-surface-container-low p-6 sm:p-8 rounded-3xl flex flex-col justify-between h-40 transition-all hover:shadow-xl hover:translate-y-[-4px] cursor-default border border-primary/5">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-tertiary">Saved Properties</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-headline font-black text-on-background">12</span>
                    <span class="text-stone-400 font-bold text-xs uppercase tracking-widest">Farms</span>
                </div>
            </div>
            <div class="bg-primary/5 p-6 sm:p-8 rounded-3xl flex flex-col justify-between h-40 transition-all hover:shadow-xl hover:translate-y-[-4px] border border-primary/10">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Recent History</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-headline font-black text-on-background">05</span>
                    <span class="text-stone-400 font-bold text-xs uppercase tracking-widest">Visited</span>
                </div>
            </div>
            <div class="bg-surface-container-low p-6 sm:p-8 rounded-3xl flex flex-col justify-between h-40 transition-all hover:shadow-xl hover:translate-y-[-4px] border border-primary/5">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-secondary">Open Enquiries</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-headline font-black text-on-background">03</span>
                    <span class="text-stone-400 font-bold text-xs uppercase tracking-widest">Active</span>
                </div>
            </div>
        </section>

        <!-- Recently Viewed Section -->
        <section class="mb-20">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8">
                <div>
                    <h3 class="text-2xl font-headline font-black text-on-background tracking-tight">Continue Exploring</h3>
                    <p class="text-on-surface-variant text-sm font-medium">Continue from your last interests.</p>
                </div>
                <a class="text-primary font-bold hover:translate-x-2 transition-transform flex items-center gap-1 group text-sm uppercase tracking-widest" href="#">
                    View all <span class="material-symbols-outlined text-sm font-black">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8">
                <!-- Card 1 -->
                <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-stone-100">
                    <div class="relative h-64 overflow-hidden">
                        <img alt="Farm" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" src="https://images.unsplash.com/photo-1500382017468-9049fee78a6c?auto=format&fit=crop&q=80&w=800"/>
                        <div class="absolute top-4 right-4">
                            <button class="w-10 h-10 rounded-full bg-white/80 backdrop-blur text-error transition-all hover:scale-110">
                                <span class="material-symbols-outlined filled-icon">favorite</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-xl font-headline font-black text-on-background leading-tight">Veridian Valley Farm</h4>
                            <div class="flex items-center gap-1 bg-stone-50 border border-stone-200 px-2 py-1 rounded-lg">
                                <span class="material-symbols-outlined text-sm text-amber-600 filled-icon">star</span>
                                <span class="text-xs font-bold">4.8</span>
                            </div>
                        </div>
                        <p class="text-stone-400 font-bold uppercase tracking-widest text-[10px] flex items-center gap-1 mb-6">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span> Indore, MP
                        </p>
                        <div class="flex items-center justify-between mb-8">
                            <p class="text-2xl font-black font-headline text-primary leading-none">₹4,500 <span class="text-[10px] text-stone-300 font-bold tracking-widest uppercase ml-1">night</span></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="flex items-center justify-center gap-2 py-3.5 bg-stone-50 text-stone-600 rounded-2xl font-black text-[10px] tracking-widest uppercase hover:bg-stone-200 transition-all">
                                <span class="material-symbols-outlined text-lg">call</span> Call
                            </button>
                            <button class="flex items-center justify-center gap-2 py-3.5 bg-primary text-on-primary rounded-2xl font-black text-[10px] tracking-widest uppercase hover:bg-primary-container transition-all shadow-lg shadow-primary/20">
                                <span class="material-symbols-outlined text-lg">forum</span> WhatsApp
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Repeat other cards (Optimized for space) -->
                <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all border border-stone-100 sm:block hidden lg:hidden xl:block">
                    <div class="relative h-64">
                        <img alt="Farm" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" src="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&q=80&w=800"/>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-headline font-black text-on-background mb-4 leading-tight">Old Oak Homestead</h4>
                        <div class="flex items-center gap-1 bg-primary/10 text-primary w-fit px-3 py-1 rounded-full text-xs font-bold mb-8">₹6,200 / night</div>
                        <div class="grid grid-cols-2 gap-3">
                             <div class="col-span-2 p-3 bg-stone-100 rounded-2xl text-[10px] font-black uppercase text-center text-stone-400">View In Details</div>
                        </div>
                    </div>
                </div>

                 <div class="group bg-white rounded-3xl overflow-hidden shadow-sm transition-all border border-stone-100 md:block hidden">
                    <div class="relative h-64">
                        <img alt="Farm" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" src="https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?auto=format&fit=crop&q=80&w=800"/>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-headline font-black text-on-background mb-4 leading-tight">Azure Lake Estate</h4>
                        <div class="flex items-center gap-1 bg-primary/10 text-primary w-fit px-3 py-1 rounded-full text-xs font-bold mb-8">₹8,500 / night</div>
                        <div class="grid grid-cols-2 gap-3">
                             <div class="col-span-2 p-3 bg-stone-100 rounded-2xl text-[10px] font-black uppercase text-center text-stone-400">View In Details</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Wishlist Highlights -->
        <section class="pb-10">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h3 class="text-2xl font-headline font-black text-on-background tracking-tight">Wishlist Gems</h3>
                    <p class="text-on-surface-variant text-sm font-medium">Top picks for your next journey.</p>
                </div>
                <a class="text-stone-400 hover:text-primary transition-colors text-[10px] font-black uppercase tracking-widest" href="#">Manage <span class="material-symbols-outlined text-[14px]">arrow_forward</span></a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Large Horizontal Card 1 -->
                <div class="flex flex-col sm:flex-row bg-surface-container-low rounded-3xl overflow-hidden hover:shadow-lg transition-all group">
                    <div class="w-full sm:w-2/5 h-48 sm:h-auto">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-all duration-700" alt="Farm" src="https://images.unsplash.com/photo-1444858291040-58f756a3bcd6?auto=format&fit=crop&q=80&w=600"/>
                    </div>
                    <div class="p-8 flex flex-col justify-center gap-4 flex-1">
                        <div class="flex justify-between">
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-tertiary">Premium Stay</span>
                            <span class="material-symbols-outlined text-error filled-icon text-lg">favorite</span>
                        </div>
                        <h4 class="text-2xl font-headline font-bold text-on-background leading-none">Saffron Orchard</h4>
                        <p class="text-stone-400 font-bold text-xs uppercase">Pampore, J&K</p>
                        <button class="w-full bg-white text-primary border border-primary/10 py-3 rounded-2xl font-black text-[10px] tracking-widest uppercase mt-4 hover:bg-primary hover:text-white transition-all">Check Dates</button>
                    </div>
                </div>

                <!-- Large Horizontal Card 2 -->
                <div class="flex flex-col sm:flex-row bg-surface-container-low rounded-3xl overflow-hidden hover:shadow-lg transition-all group">
                    <div class="w-full sm:w-2/5 h-48 sm:h-auto">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-all duration-700" alt="Farm" src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&q=80&w=600"/>
                    </div>
                    <div class="p-8 flex flex-col justify-center gap-4 flex-1">
                        <div class="flex justify-between">
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-tertiary">Eco Retreat</span>
                            <span class="material-symbols-outlined text-error filled-icon text-lg">favorite</span>
                        </div>
                        <h4 class="text-2xl font-headline font-bold text-on-background leading-none">Cloud Peak</h4>
                        <p class="text-stone-400 font-bold text-xs uppercase">Manali, HP</p>
                        <button class="w-full bg-white text-primary border border-primary/10 py-3 rounded-2xl font-black text-[10px] tracking-widest uppercase mt-4 hover:bg-primary hover:text-white transition-all">Check Dates</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Floating Help Button (Optimized for Mobile) -->
    <div class="fixed bottom-6 right-6 sm:bottom-10 sm:right-10 z-[100]">
        <button class="w-16 h-16 rounded-full bg-stone-950 text-on-primary shadow-2xl flex items-center justify-center transition-all hover:rotate-[30deg] hover:bg-primary">
            <span class="material-symbols-outlined text-3xl">psychiatry</span>
        </button>
    </div>

    <!-- Toggle Logic for Sidebar -->
    <script>
        const sidebar = document.body;
        const menuIcon = document.getElementById('menu-icon');
        const overlay = document.getElementById('overlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('sidebar-open');
            const isOpen = sidebar.classList.contains('sidebar-open');
            menuIcon.innerText = isOpen ? 'close' : 'menu';
            overlay.classList.toggle('hidden');
            
            // Lock scrolling when sidebar is open on mobile
            if(window.innerWidth < 1024) {
                document.body.classList.toggle('overflow-hidden');
            }
        }
    </script>
</body>

</html>