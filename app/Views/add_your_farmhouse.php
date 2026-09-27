<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Add Listing | Harvest & Hearth</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1" rel="stylesheet" />
    
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
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#1b1d0e",
                        "on-surface-variant": "#40493d",
                        "outline-variant": "#bfcaba",
                        "tertiary": "#8d3f00"
                    },
                    fontFamily: {
                        "headline": ["Epilogue", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                        "label": ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        /* Smooth transitions for inputs */
        input, textarea {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="text-on-surface bg-background font-body antialiased">

    <!-------------------Header------------------>
    
    

    <main class="pt-24 pb-20 max-w-7xl mx-auto lg:flex gap-8 px-4 md:px-8">
        
        <!-- Mobile Step Indicator (Hidden on Desktop) -->
        <div class="lg:hidden mb-8 bg-surface-container rounded-2xl p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold text-sm">1</div>
                <div>
                    <h2 class="text-sm font-bold font-headline">Property Info</h2>
                    <p class="text-[10px] uppercase tracking-wider text-on-surface-variant">Step 1 of 5</p>
                </div>
            </div>
            <button class="text-primary text-sm font-bold">Details</button>
        </div>

        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden lg:flex flex-col w-72 p-6 gap-6 bg-surface-container-low border border-outline-variant/20 rounded-2xl h-[fit-content] sticky top-24">
            <div>
                <h2 class="text-xl font-extrabold text-primary font-headline">Create Listing</h2>
                <div class="mt-2 w-full bg-outline-variant/30 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary w-1/5 h-full"></div>
                </div>
                <p class="mt-2 text-[10px] text-on-surface-variant font-bold uppercase tracking-widest">Progress: 20%</p>
            </div>
            
            <nav class="flex flex-col gap-1">
                <a class="flex items-center gap-3 p-3 bg-white text-primary font-bold shadow-sm rounded-xl" href="#">
                    <span class="material-symbols-outlined">home_work</span>
                    <span class="text-sm">Property Info</span>
                </a>
                <a class="flex items-center gap-3 p-3 text-on-surface-variant hover:text-primary hover:bg-white/50 rounded-xl transition-all" href="#">
                    <span class="material-symbols-outlined">location_on</span>
                    <span class="text-sm font-semibold">Location</span>
                </a>
                <a class="flex items-center gap-3 p-3 text-on-surface-variant hover:text-primary hover:bg-white/50 rounded-xl transition-all" href="#">
                    <span class="material-symbols-outlined">grid_view</span>
                    <span class="text-sm font-semibold">Amenities</span>
                </a>
                <a class="flex items-center gap-3 p-3 text-on-surface-variant hover:text-primary hover:bg-white/50 rounded-xl transition-all" href="#">
                    <span class="material-symbols-outlined">add_a_photo</span>
                    <span class="text-sm font-semibold">Photos</span>
                </a>
                <a class="flex items-center gap-3 p-3 text-on-surface-variant hover:text-primary hover:bg-white/50 rounded-xl transition-all" href="#">
                    <span class="material-symbols-outlined">payments</span>
                    <span class="text-sm font-semibold">Pricing</span>
                </a>
            </nav>
            
            <button class="w-full py-4 border-2 border-primary/20 text-primary rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-primary hover:text-white transition-all">
                Save Draft
            </button>
        </aside>

        <!-- Main Form Section -->
        <section class="flex-1 space-y-8 lg:space-y-12">
            <header>
                <h1 class="text-3xl md:text-5xl font-extrabold text-primary tracking-tight mb-4 leading-tight">
                    List your slice <br class="hidden md:block" />of agrarian paradise.
                </h1>
                <p class="text-base md:text-lg text-on-surface-variant leading-relaxed max-w-2xl">
                    Whether it's a rustic cottage or an expansive orchard estate, connect with travelers seeking authentic nature escapes.
                </p>
            </header>

            <form class="space-y-8 lg:space-y-10">
                
                <!-- Section 1: Basic Information -->
                <div class="bg-surface-container-lowest p-6 md:p-10 rounded-[2rem] border border-outline-variant/20 shadow-sm">
                    <h3 class="text-xl font-bold mb-8 flex items-center gap-3 text-primary font-headline">
                        <span class="material-symbols-outlined bg-primary/10 p-2 rounded-lg">description</span>
                        Basic Details
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="group">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant mb-2 ml-1">Listing Title</label>
                            <input class="w-full bg-surface-container border-0 border-b-2 border-transparent rounded-xl p-4 md:p-5 focus:ring-0 focus:border-primary focus:bg-white transition-all text-sm" 
                                   placeholder="e.g. Sunset Willow Organic Estate" type="text" name="title"/>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant mb-2 ml-1">Property Category</label>
                            <select name="category" class="w-full bg-surface-container border-0 border-b-2 border-transparent rounded-xl p-4 md:p-5 focus:ring-0 focus:border-primary focus:bg-white transition-all text-sm font-semibold">
                                <option value="Guest House">Guest House</option>
                                <option value="Resort">Resort</option>
                                <option value="Farmhouse" selected>Farmhouse</option>
                                <option value="Villa">Villa</option>
                            </select>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant mb-2 ml-1">Soulful Description</label>
                            <textarea class="w-full bg-surface-container border-0 border-b-2 border-transparent rounded-2xl p-4 md:p-5 focus:ring-0 focus:border-primary focus:bg-white transition-all text-sm" 
                                      placeholder="Tell guests about the sounds of nature, local harvests, and your farm's history..." rows="5" name="description"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pricing & Location -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <!-- Pricing Card -->
                    <div class="bg-surface-container-lowest p-6 md:p-10 rounded-[2rem] border border-outline-variant/20">
                        <h3 class="text-xl font-bold mb-8 flex items-center gap-3 text-primary font-headline">
                            <span class="material-symbols-outlined bg-primary/10 p-2 rounded-lg">payments</span>
                            Value
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2 ml-1">Price / Night (USD)</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold">$</span>
                                    <input class="w-full bg-surface-container border-0 rounded-xl py-4 md:py-5 pl-10 pr-4 focus:ring-2 focus:ring-primary/20 text-sm font-bold" placeholder="0.00" type="number"/>
                                </div>
                            </div>
                            <label class="flex items-center gap-4 cursor-pointer select-none">
                                <input class="w-6 h-6 rounded-lg border-outline-variant text-primary focus:ring-primary/30" type="checkbox"/>
                                <span class="text-sm font-semibold text-on-surface-variant">Allow price negotiations</span>
                            </label>
                        </div>
                    </div>

                    <!-- Location Card -->
                    <div class="bg-surface-container-lowest p-6 md:p-10 rounded-[2rem] border border-outline-variant/20">
                        <h3 class="text-xl font-bold mb-8 flex items-center gap-3 text-primary font-headline">
                            <span class="material-symbols-outlined bg-primary/10 p-2 rounded-lg">location_on</span>
                            Where
                        </h3>
                        <div class="space-y-4">
                            <input class="w-full bg-surface-container border-0 rounded-xl p-4 md:p-5 focus:ring-2 focus:ring-primary/20 text-sm" placeholder="Province / State" type="text"/>
                            <input class="w-full bg-surface-container border-0 rounded-xl p-4 md:p-5 focus:ring-2 focus:ring-primary/20 text-sm" placeholder="Street Address" type="text"/>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Amenities Grid -->
                <div class="bg-surface-container-lowest p-6 md:p-10 rounded-[2rem] border border-outline-variant/20 shadow-sm">
                    <h3 class="text-xl font-bold mb-8 flex items-center gap-3 text-primary font-headline">
                        <span class="material-symbols-outlined bg-primary/10 p-2 rounded-lg">grid_view</span>
                        Farm Comforts
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        <!-- Icon Checkbox Item -->
                        <label class="group relative flex flex-col items-center gap-3 p-4 md:p-6 rounded-2xl bg-surface hover:bg-primary/5 border-2 border-transparent peer-checked:border-primary transition-all cursor-pointer">
                            <input class="absolute inset-0 opacity-0 peer cursor-pointer" type="checkbox"/>
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform peer-checked:bg-primary peer-checked:text-on-primary">
                                <span class="material-symbols-outlined text-2xl">pool</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant peer-checked:text-primary">Pool</span>
                        </label>

                        <label class="group relative flex flex-col items-center gap-3 p-4 md:p-6 rounded-2xl bg-surface hover:bg-primary/5 border-2 border-transparent peer-checked:border-primary transition-all cursor-pointer">
                            <input class="absolute inset-0 opacity-0 peer cursor-pointer" type="checkbox" checked/>
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm peer-checked:bg-primary peer-checked:text-on-primary transition-all">
                                <span class="material-symbols-outlined text-2xl">park</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant peer-checked:text-primary">Gardens</span>
                        </label>

                        <label class="group relative flex flex-col items-center gap-3 p-4 md:p-6 rounded-2xl bg-surface hover:bg-primary/5 border-2 border-transparent peer-checked:border-primary transition-all cursor-pointer">
                            <input class="absolute inset-0 opacity-0 peer cursor-pointer" type="checkbox"/>
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm peer-checked:bg-primary peer-checked:text-on-primary transition-all">
                                <span class="material-symbols-outlined text-2xl">wifi</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant peer-checked:text-primary">WiFi</span>
                        </label>

                        <label class="group relative flex flex-col items-center gap-3 p-4 md:p-6 rounded-2xl bg-surface hover:bg-primary/5 border-2 border-transparent peer-checked:border-primary transition-all cursor-pointer">
                            <input class="absolute inset-0 opacity-0 peer cursor-pointer" type="checkbox"/>
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm peer-checked:bg-primary peer-checked:text-on-primary transition-all">
                                <span class="material-symbols-outlined text-2xl">local_fire_department</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant peer-checked:text-primary">Fireplace</span>
                        </label>

                        <label class="group relative flex flex-col items-center gap-3 p-4 md:p-6 rounded-2xl bg-surface hover:bg-primary/5 border-2 border-transparent peer-checked:border-primary transition-all cursor-pointer">
                            <input class="absolute inset-0 opacity-0 peer cursor-pointer" type="checkbox"/>
                            <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm peer-checked:bg-primary peer-checked:text-on-primary transition-all">
                                <span class="material-symbols-outlined text-2xl">pets</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant peer-checked:text-primary">Pets Welcome</span>
                        </label>
                    </div>
                </div>

                <!-- Section 4: Image Upload Area -->
                <div class="bg-surface-container-lowest p-6 md:p-10 rounded-[2rem] border border-outline-variant/20 shadow-sm">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-3 text-primary font-headline">
                        <span class="material-symbols-outlined bg-primary/10 p-2 rounded-lg">photo_camera</span>
                        The Visual Story
                    </h3>
                    
                    <div class="border-3 border-dashed border-outline-variant rounded-3xl p-10 lg:p-20 text-center hover:bg-primary/5 hover:border-primary/50 transition-all cursor-pointer relative">
                        <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" multiple />
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl">upload_file</span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-base font-bold text-on-surface">Click or drag images to upload</p>
                                <p class="text-xs text-on-surface-variant font-medium">Capture the landscape, interiors, and local flora (max 20MB/image)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="bg-primary p-8 md:p-12 rounded-[2.5rem] text-center md:text-left md:flex items-center justify-between gap-8 text-on-primary overflow-hidden relative shadow-2xl">
                    <!-- Background decor -->
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary-container rounded-full opacity-30"></div>
                    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-on-surface-variant/10 rounded-full opacity-20"></div>

                    <div class="relative z-10 max-w-lg">
                        <h2 class="text-2xl md:text-3xl font-bold font-headline mb-4 tracking-tight">Ready to join our collective?</h2>
                        <p class="text-white/80 text-sm leading-relaxed mb-8 md:mb-0">Your property will undergo a swift quality check before going live to our 50k+ daily nature-seekers.</p>
                    </div>

                    <div class="relative z-10">
                        <button type="submit" class="w-full md:w-auto px-10 py-5 bg-white text-primary rounded-full font-extrabold text-sm uppercase tracking-[0.2em] shadow-lg hover:bg-surface transition-transform hover:-translate-y-1 active:translate-y-0">
                            Submit for Review
                        </button>
                    </div>
                </div>

            </form>
        </section>
    </main>

    <!-----------Footer--------------->
    

</body>
</html>