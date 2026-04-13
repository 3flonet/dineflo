@php 
    $settings = $settings ?? app(\App\Settings\GeneralSettings::class); 
    $siteName = $settings->site_name ?? config('app.name', 'Dineflo');
    
    // Dynamize text
    $dynamicTitle = str_replace([':site_name', 'Dineflo'], $siteName, $feature->title);
    $dynamicShort = str_replace([':site_name', 'Dineflo'], $siteName, $feature->short_description);
    $dynamicLong  = str_replace([':site_name', 'Dineflo'], $siteName, $feature->long_description);
@endphp
<div class="min-h-screen bg-white dark:bg-[#080B14] transition-colors duration-500 font-sans selection:bg-indigo-500/30">
    {{-- High-End Mesh Background --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-500/10 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute top-[20%] -right-[10%] w-[30%] h-[50%] bg-purple-500/10 blur-[120px] rounded-full" style="animation: pulse 8s infinite"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[50%] h-[30%] bg-blue-500/10 blur-[120px] rounded-full opacity-50"></div>
    </div>

    {{-- Sticky Minimal Nav (Back) --}}
    <div class="sticky top-0 z-50 backdrop-blur-md bg-white/70 dark:bg-[#080B14]/70 border-b border-gray-100 dark:border-white/5 transition-all">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('features') }}" class="group flex items-center gap-2 text-sm font-semibold text-gray-400 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-white transition-all">
                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center group-hover:-translate-x-1 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </div>
                Kembali
            </a>
            <div class="flex items-center gap-4">
                <span class="hidden md:block text-xs font-bold tracking-widest uppercase text-gray-400 dark:text-gray-600">Feature Spotlight</span>
                <a href="{{ route('home') }}#pricing" class="px-5 py-2 bg-indigo-600 text-white rounded-full text-xs font-bold hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-500/20">Coba Gratis</a>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="max-w-7xl mx-auto px-6 pt-16 pb-24">
        <div class="grid lg:grid-cols-12 gap-16 items-start">
            
            {{-- Content Column --}}
            <div class="lg:col-span-7 space-y-12">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 mb-8">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        <span class="text-[10px] font-bold tracking-widest uppercase text-indigo-600 dark:text-indigo-400">{{ $feature->badge }} FEATURE</span>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white leading-[1.1] tracking-tight mb-8">
                        {{ $dynamicTitle }}
                    </h1>
                    
                    <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                        {{ $dynamicShort }}
                    </p>
                </div>

                {{-- Key Capabilities --}}
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach($normalizedBullets as $bullet)
                    @php 
                        $bText = is_array($bullet) ? ($bullet['bullet'] ?? '') : $bullet;
                        $bIcon = is_array($bullet) ? ($bullet['icon'] ?? 'star') : 'star';
                        
                        // Clean up icon name 
                        $bIcon = str_replace(['fi ', 'fi-rr-', 'fi-rs-', 'fi-br-', 'fi-sr-'], '', $bIcon);
                        $svgPath = public_path("vendor/uicons-regular-rounded/svg/fi-rr-{$bIcon}.svg");

                        // Dynamize bullet text too
                        $bText = str_replace([':site_name', 'Dineflo'], $siteName, $bText);
                    @endphp
                    <div class="p-8 rounded-[32px] bg-white/50 dark:bg-white/5 border border-gray-100 dark:border-white/5 hover:border-indigo-500/30 hover:bg-white dark:hover:bg-white/10 transition-all duration-500 group shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 active:scale-[0.98]">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-6 group-hover:rotate-12 transition-transform duration-500">
                             @if(file_exists($svgPath))
                                <div class="w-6 h-6 fill-current">
                                    {!! str_replace('<svg ', '<svg class="w-full h-full" ', file_get_contents($svgPath)) !!}
                                </div>
                             @else
                                <i class="fi fi-rr-star text-2xl"></i>
                             @endif
                        </div>
                        <p class="text-[15px] font-bold text-gray-900 dark:text-gray-100 leading-relaxed">{{ $bText }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Long Description --}}
                <div class="prose prose-lg md:prose-xl prose-indigo dark:prose-invert max-w-none 
                    prose-headings:text-gray-900 dark:prose-headings:text-white
                    prose-headings:font-black prose-headings:tracking-tight 
                    prose-p:text-gray-600 dark:prose-p:text-gray-400 prose-p:leading-relaxed
                    prose-li:text-gray-600 dark:prose-li:text-gray-400
                    prose-img:rounded-3xl prose-img:shadow-2xl prose-strong:text-indigo-600 dark:prose-strong:text-indigo-400">
                    <div class="h-1.5 w-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full mb-12"></div>
                    {!! $dynamicLong ?: '<p class="italic opacity-50">Explorasi mendalam untuk fitur ini sedang disiapkan...</p>' !!}
                </div>
            </div>

            {{-- Visual Column (Sticky) --}}
            <div class="lg:col-span-5 lg:sticky lg:top-32">
                <div class="relative group">
                    {{-- Premium Device Mockup Container --}}
                    <div class="relative mx-auto max-w-[400px]">
                        {{-- Intense Glow Backdrop --}}
                        <div class="absolute -inset-20 bg-gradient-to-tr from-indigo-500/20 via-purple-500/20 to-blue-500/20 blur-[120px] rounded-full opacity-40 group-hover:opacity-70 transition-opacity duration-1000"></div>
                        
                        {{-- The Mockup Frame --}}
                        <div class="relative rounded-[3rem] p-3 md:p-4 bg-gray-900 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5),0_30px_60px_-30px_rgba(0,0,0,0.3)] ring-1 ring-white/20 transform transition-all duration-1000 ease-out group-hover:rotate-1 group-hover:scale-[1.05] group-hover:-translate-y-4">
                            {{-- Inner Glass Layer --}}
                            <div class="relative rounded-[2.2rem] overflow-hidden bg-white dark:bg-[#0B0F19] aspect-[9/16] md:aspect-[3/4] flex flex-col">
                                @if($feature->image_url)
                                    <img src="{{ Storage::url($feature->image_url) }}" alt="{{ $feature->title }}" class="w-full h-full object-cover">
                                @else
                                    {{-- Realistic Component-based Placeholder --}}
                                    <div class="flex-grow bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-[#0B0F19] dark:via-[#111827] dark:to-[#0B0F19] p-8 flex flex-col items-center justify-center text-center">
                                        {{-- Central Icon/Logo --}}
                                        <div class="w-24 h-24 rounded-[2rem] bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 shadow-2xl flex items-center justify-center text-5xl mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700">
                                            {{ $feature->tab === 'order' ? '🛒' : ($feature->tab === 'kitchen' ? '🍳' : '✨') }}
                                        </div>
                                        
                                        {{-- Dynamic Title & Branding --}}
                                        <div class="space-y-4">
                                            <div class="h-1.5 w-12 bg-indigo-500/30 rounded-full mx-auto"></div>
                                            <h4 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight leading-none px-4">
                                                {{ $feature->title }}
                                            </h4>
                                            <p class="text-[10px] uppercase font-black tracking-[0.3em] text-indigo-500/60 dark:text-indigo-400/40">Visual Interface Preview</p>
                                        </div>

                                        {{-- Abstract UI Elements for "Mockup" feel --}}
                                        <div class="mt-12 w-full space-y-3 px-4">
                                            <div class="h-2 w-full bg-gray-200/50 dark:bg-white/5 rounded-full"></div>
                                            <div class="h-2 w-[80%] bg-gray-200/50 dark:bg-white/5 rounded-full mx-auto"></div>
                                            <div class="mt-8 grid grid-cols-2 gap-3">
                                                <div class="h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20"></div>
                                                <div class="h-12 rounded-xl bg-purple-500/10 border border-purple-500/20"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Reflection Glass Effect Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-white/10 pointer-events-none"></div>
                            </div>
                            
                            {{-- Camera Hole / Notch Simulation --}}
                            <div class="absolute top-8 left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-black/40 ring-1 ring-white/10"></div>
                        </div>

                        {{-- Floating Status Badge --}}
                        <div class="absolute -bottom-8 -left-8 p-6 rounded-[2rem] bg-indigo-950 text-white shadow-2xl border border-white/10 hidden md:block group-hover:translate-x-4 group-hover:-translate-y-2 transition-all duration-700">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-indigo-300">Technology Status</p>
                                    <p class="text-xs font-bold">Ready to Deploy</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing/Action Card --}}
                    <div class="mt-20 p-8 rounded-[40px] bg-indigo-600 text-white shadow-2xl shadow-indigo-500/30 relative overflow-hidden group/card active:scale-[0.99] transition-transform">
                        {{-- Bubbly Background Decoration --}}
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full translate-x-1/2 -translate-y-1/2 blur-3xl group-hover/card:scale-150 transition-transform duration-1000"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-400/20 rounded-full -translate-x-1/2 translate-y-1/2 blur-2xl"></div>
                        
                        <h3 class="text-2xl font-black mb-1">Pilih Paket Bisnis</h3>
                        <p class="text-indigo-100/80 mb-8 text-sm font-medium italic">Transformasi operasional Anda mulai hari ini.</p>
                        
                        <div class="space-y-3 mb-10 relative z-10">
                            @foreach($plans as $plan)
                            @php 
                                $isExplicitlyIncluded = in_array($feature->title, (array)($plan->features ?? []));
                                
                                // Smart Logic: 
                                // 1. Strategy Plan always includes everything
                                // 2. Standard features are included in all paid plans (Price > 0)
                                // 3. Otherwise, check explicit inclusion from database
                                $isAvailable = $isExplicitlyIncluded || 
                                               (strtolower($plan->name) === 'strategy') || 
                                               (isset($plan->price) && $plan->price > 0 && $feature->badge === 'Standar');
                            @endphp
                            <div class="flex items-center justify-between p-4 rounded-3xl transition-all duration-300 {{ $isAvailable ? 'bg-white/15 border border-white/20 shadow-inner' : 'bg-black/10 opacity-40 grayscale' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $isAvailable ? 'bg-white text-indigo-600' : 'bg-white/10 text-white' }} flex items-center justify-center shadow-sm">
                                        @if($isAvailable)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        @endif
                                    </div>
                                    <span class="font-black text-sm tracking-tight">{{ $plan->name }}</span>
                                </div>
                                <span class="text-[10px] font-black tracking-widest uppercase {{ $isAvailable ? 'text-white' : 'text-white/40' }}">
                                    {{ $isAvailable ? 'Included' : 'Unavailable' }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        <a href="https://wa.me/{{ $settings->site_phone ?? '628' }}" class="relative z-10 block w-full py-5 text-center bg-white text-indigo-600 rounded-[2rem] font-black text-sm hover:scale-105 active:scale-95 transition-all shadow-xl shadow-black/10 group/btn">
                            <span class="inline-flex items-center gap-2">
                                HUBUNGI KAMI 
                                <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Keyframes and custom animations --}}
    <style>
        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 4s infinite ease-in-out;
        }
        .animate-pulse-slow {
            animation: pulse-slow 3s infinite ease-in-out;
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(0.98); }
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        /* Fix for bullet points in long description */
        .prose ul {
            list-style-type: none !important;
            padding-left: 0 !important;
        }
        .prose li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1rem;
        }
        .prose li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4f46e5;
            font-weight: 900;
        }
        .prose ol {
            list-style-type: decimal !important;
            padding-left: 1.6em !important;
        }
    </style>
    <x-footer-premium />
</div>

