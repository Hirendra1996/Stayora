<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Add Property | The Elevated Estate</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1" rel="stylesheet" />
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0d631b",
                        "primary-container": "#2e7d32",
                        "secondary": "#3c6842",
                        "tertiary-container": "#b25200",
                        "on-tertiary-container": "#ffeee6",
                        "on-primary": "#ffffff",
                        "background": "#fbfbe2",
                        "surface": "#fbfbe2",
                        "surface-container": "#efefd7",
                        "surface-container-low": "#f5f5dc",
                        "surface-container-high": "#eaead1",
                        "surface-container-highest": "#e4e4cc",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#1b1d0e",
                        "on-surface-variant": "#40493d",
                        "outline": "#707a6c",
                        "outline-variant": "#bfcaba",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        "headline": ["Epilogue", "sans-serif"],
                        "body": ["Inter", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
    </style>
</head>

<body class="bg-surface text-on-surface">
    <div class="flex min-h-screen">
        
        <!-- SIDEBAR (DESKTOP) -->
        <aside class="hidden lg:flex flex-col h-screen w-72 bg-white fixed left-0 top-0 z-50 border-r border-stone-200/50 p-6">
            <div class="mb-10 px-2">
                <h1 class="text-2xl font-black text-primary font-headline tracking-tighter italic">Elevated</h1>
                <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest mt-1">Host Portal</p>
            </div>
            <nav class="flex-1 space-y-1">
                <a class="flex items-center px-4 py-3 rounded-2xl text-stone-500 hover:bg-surface-container transition-all group" href="#">
                    <span class="material-symbols-outlined mr-3">dashboard</span>
                    <span class="font-bold text-sm">Dashboard</span>
                </a>
                <a class="flex items-center px-4 py-3 rounded-2xl text-stone-500 hover:bg-surface-container transition-all" href="#">
                    <span class="material-symbols-outlined mr-3" style="font-variation-settings: 'FILL' 1;">gite</span>
                    <span class="font-bold text-sm">My Listings</span>
                </a>
                <a class="flex items-center px-4 py-3 rounded-2xl text-stone-500 hover:bg-surface-container transition-all" href="#">
                    <span class="material-symbols-outlined mr-3">calendar_month</span>
                    <span class="font-bold text-sm">Bookings</span>
                </a>
                <a class="flex items-center px-4 py-3 rounded-2xl text-stone-500 hover:bg-surface-container transition-all" href="#">
                    <span class="material-symbols-outlined mr-3">payments</span>
                    <span class="font-bold text-sm">Earnings</span>
                </a>
            </nav>
            <div class="pt-6 border-t border-stone-100">
                <a href="#" class="w-full bg-primary/10 text-primary py-4 rounded-2xl font-bold flex items-center justify-center gap-2 bg-primary text-white shadow-lg shadow-primary/20 transition-all pointer-events-none">
                    <span class="material-symbols-outlined">add</span>
                    <span>New Listing</span>
                </a>
            </div>
        </aside>

        <!-- MAIN AREA -->
        <main class="flex-1 lg:ml-72 min-h-screen">
            
            <!-- HEADER -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-lg border-b border-stone-100 lg:px-12 px-4 py-4">
                <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
                    <!-- Mobile Logo (shown only on mobile/tablet) -->
                    <div class="lg:hidden text-primary font-black font-headline text-xl italic tracking-tighter">
                        E.
                    </div>

                    <div class="flex-1 max-w-lg lg:ml-0 ml-4 hidden sm:block">
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">search</span>
                            <input class="w-full bg-stone-100 border-none rounded-2xl pl-12 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 placeholder-stone-400" placeholder="Search hosts portal..." type="text"/>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 md:gap-4 ml-auto sm:ml-0">
                        <button class="w-10 h-10 flex items-center justify-center text-stone-500 hover:bg-stone-100 rounded-full relative transition-colors">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full ring-2 ring-white"></span>
                        </button>
                        <div class="h-6 w-px bg-stone-200 hidden md:block"></div>
                        <div class="flex items-center gap-3">
                            <img alt="User" class="w-9 h-9 md:w-10 md:h-10 rounded-2xl object-cover ring-2 ring-stone-50 shadow-md" src="https://i.pravatar.cc/150?u=4"/>
                            <div class="hidden sm:block leading-none">
                                <p class="text-sm font-bold">R. Kumar</p>
                                <span class="text-[10px] font-black uppercase text-stone-400">Gold Host</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- FORM CONTENT -->
            <div class="lg:p-12 md:p-8 p-4 max-w-5xl mx-auto pb-32 lg:pb-12">
                
                <!-- Dynamic Header Block -->
                <div class="mb-10">
                    <div class="flex items-center gap-2 text-[10px] font-bold text-stone-400 uppercase tracking-[0.2em] mb-3">
                        <a href="#" class="hover:text-primary transition-colors">Properties</a>
                        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                        <span class="text-primary">Add New</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-on-surface font-headline tracking-tighter leading-none mb-3">Add Property</h2>
                    <p class="text-stone-500 font-medium">Expand your portfolio. Submissions are approved manually within 24 hrs.</p>
                </div>

                <!-- ALERTS (Successfully created / Or Error Messages triggered via Controller) -->
                <?php if (!empty($successMsg)): ?>
                    <div class="mb-8 bg-[#fbfbe2]/60 border border-primary/20 border-l-8 border-l-primary text-primary px-6 py-5 rounded-[1.5rem] flex items-center gap-4 shadow-sm animate-pulse-once">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="font-black text-sm"><?= htmlspecialchars($successMsg) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMsg)): ?>
                    <div class="mb-8 bg-error/5 border border-error/20 border-l-8 border-l-error text-error px-6 py-5 rounded-[1.5rem] flex items-center gap-4 shadow-sm">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">error</span>
                        <span class="font-black text-sm"><?= htmlspecialchars($errorMsg) ?></span>
                    </div>
                <?php endif; ?>

                <!-- DYNAMIC ADD FARMHOUSE FORM -->
                <form method="POST" action="" class="bg-white rounded-[2rem] p-6 lg:p-10 border border-stone-100 shadow-2xl shadow-primary/5 space-y-10" enctype="multipart/form-data">

                    <!-- SECTION 1: Base Details -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-headline font-black text-xl mb-1">General Details</h3>
                            <p class="text-xs text-stone-400 font-bold uppercase tracking-widest">Public Information</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title (Spans both cols on big screens, full on mobile) -->
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-stone-600">Property Title <span class="text-error">*</span></label>
                                <input name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" placeholder="e.g. The Highland Cottage" type="text"
                                       class="w-full bg-stone-50/50 border border-stone-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none font-medium transition-all" />
                            </div>

                            <!-- Property Category -->
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-stone-600">Property Category <span class="text-error">*</span></label>
                                <select name="category" required
                                        class="w-full bg-stone-50/50 border border-stone-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none font-medium transition-all">
                                    <?php 
                                    $postCat = $_POST['category'] ?? 'Farmhouse';
                                    foreach (['Guest House', 'Resort', 'Farmhouse', 'Villa'] as $c): ?>
                                        <option value="<?= $c ?>" <?= ($postCat === $c) ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Location -->
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-stone-600">City / Location <span class="text-error">*</span></label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">pin_drop</span>
                                    <input name="location" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" placeholder="e.g. Udaipur" type="text"
                                           class="w-full bg-stone-50/50 border border-stone-200 rounded-2xl pl-12 pr-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none font-medium transition-all" />
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-stone-600">Nightly Price (₹) <span class="text-error">*</span></label>
                                <div class="flex gap-4 items-center">
                                    <div class="relative flex-1 group">
                                        <span class="font-headline font-black text-stone-400 absolute left-4 top-1/2 -translate-y-1/2">₹</span>
                                        <input name="price" required step="0.01" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" placeholder="5000" type="number"
                                               class="w-full bg-stone-50/50 border border-stone-200 rounded-2xl pl-10 pr-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none font-medium transition-all" />
                                    </div>
                                    <label class="flex items-center gap-3 cursor-pointer shrink-0 border border-stone-200 rounded-2xl px-4 py-4 hover:bg-stone-50 transition-colors">
                                        <input type="checkbox" name="is_negotiable" <?= isset($_POST['is_negotiable']) ? 'checked' : '' ?>
                                               class="rounded-md border-stone-300 text-primary focus:ring-primary h-5 w-5" />
                                        <span class="text-sm font-bold text-stone-600">Negotiable</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-stone-600">About the property</label>
                                <textarea name="description" rows="4" placeholder="Highlight features, unique setups..."
                                          class="w-full bg-stone-50/50 border border-stone-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none font-medium transition-all resize-none"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-stone-600">Exact Full Address</label>
                                <textarea name="address" rows="2" placeholder="Unit/Street name, District..."
                                          class="w-full bg-stone-50/50 border border-stone-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-primary/10 focus:border-primary focus:bg-white outline-none font-medium transition-all resize-none"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="border-stone-100" />

                    <!-- SECTION 2: Dynamic Amenities Mapping directly against DB table elements -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-headline font-black text-xl mb-1">Features & Amenities</h3>
                            <p class="text-xs text-stone-400 font-bold uppercase tracking-widest">Help users filter exactly what they need</p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- PHP DB Foreach Render for mapping cleanly against database `id` requirements perfectly cleanly synced! -->
                            <?php foreach ($availableAmenities ??[] as $amenity): ?>
                                <!-- Utilize tailwinds native grouping :checked utilities natively triggering UI transformations securely on specific blocks -->
                                <?php $isChecked = isset($_POST['amenities']) && in_array($amenity['id'], $_POST['amenities']); ?>
                                
                                <label class="group relative flex items-center justify-between p-4 border border-stone-200 rounded-2xl cursor-pointer hover:border-primary/50 transition-all has-[:checked]:bg-primary/5 has-[:checked]:border-primary/50">
                                    <span class="font-bold text-sm text-stone-600 group-has-[:checked]:text-primary transition-colors"><?= htmlspecialchars($amenity['name']) ?></span>
                                    <!-- Using modern UI pattern checking without heavy JS toggles setup here native correctly applied  -->
                                    <div class="w-6 h-6 rounded border border-stone-300 group-has-[:checked]:border-primary group-has-[:checked]:bg-primary flex items-center justify-center transition-all shadow-inner">
                                         <span class="material-symbols-outlined text-white text-[16px] opacity-0 group-has-[:checked]:opacity-100 transition-opacity">check</span>
                                    </div>
                                    <!-- Hidden checkbox input handling functional mapping explicitly in backend! -->
                                    <input type="checkbox" name="amenities[]" value="<?= $amenity['id'] ?>" class="hidden peer" <?= $isChecked ? 'checked' : '' ?> />
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="border-stone-100" />

                    <!-- SUBMIT BLOCK -->
                    <div class="flex items-center justify-end gap-4 pt-4">
                        <button type="button" onclick="window.history.back()" class="px-6 py-4 rounded-2xl font-bold text-sm text-stone-600 bg-white hover:bg-stone-50 border border-transparent hover:border-stone-200 transition-colors">
                            Cancel
                        </button>
                        
                        <button type="submit" class="bg-primary text-white px-10 py-4 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-xl shadow-primary/20 hover:bg-primary-container active:scale-95 transition-all text-sm uppercase tracking-widest">
                            <span class="material-symbols-outlined text-[18px]">publish</span>
                            Publish Listing
                        </button>
                    </div>

                </form>

                <!-- Footer text inside Form Space purely formatting layouts accurately!  -->
                <p class="text-center text-[10px] text-stone-400 font-bold uppercase tracking-widest mt-10">By listing property you agree to elevated hosts Terms & Services agreement rules context map limits apply</p>
                
            </div>
        </main>
    </div>

    <!-- BOTTOM MOBILE NAVIGATION (Pivot identical mapping unchanged correctly retained context!) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md px-6 pt-3 pb-8 flex justify-around items-center z-[100] border-t border-stone-100 shadow-[0_-20px_40px_rgba(0,0,0,0.03)]">
        <a class="flex flex-col items-center gap-1.5 text-stone-400" href="#">
            <span class="material-symbols-outlined text-[26px]">space_dashboard</span>
            <span class="text-[8px] font-black uppercase tracking-widest">Dash</span>
        </a>
        <a class="flex flex-col items-center gap-1.5 text-stone-400" href="#">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">gite</span>
            <span class="text-[8px] font-black uppercase tracking-widest">Properties</span>
        </a>
        <div class="relative">
            <button class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center shadow-xl shadow-primary/40 -translate-y-5 border-4 border-white transition-transform active:scale-90">
                <span class="material-symbols-outlined text-3xl">add</span>
            </button>
        </div>
        <a class="flex flex-col items-center gap-1.5 text-stone-400" href="#">
            <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
            <span class="text-[8px] font-black uppercase tracking-widest">Earn</span>
        </a>
        <a class="flex flex-col items-center gap-1.5 text-stone-400" href="#">
            <span class="material-symbols-outlined text-[26px]">tune</span>
            <span class="text-[8px] font-black uppercase tracking-widest">Set</span>
        </a>
    </nav>
</body>
</html>