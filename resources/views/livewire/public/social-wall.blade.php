@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<div class="bg-gray-50 dark:bg-[#0B0F19] text-gray-400 dark:text-gray-300 min-h-screen font-sans overflow-x-hidden transition-colors duration-300" x-data="{ mobileMenu: false }">
    
    @include('components.public.navbar')

    <div class="max-w-7xl mx-auto pt-32 pb-20 px-4">
        <!-- Header -->
        <div class="text-center mb-16 space-y-4">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
                Our <span class="text-emerald-500">Community</span> Wall
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Discover how leading restaurants are transforming their operations and delighting customers with {{ $settings->site_name }}.
            </p>
            <div class="flex justify-center gap-4 mt-8">
                <div class="px-6 py-2 bg-white dark:bg-gray-900 rounded-full shadow-sm border border-gray-100 dark:border-white/10 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Curated Moments</span>
                </div>
            </div>
        </div>

        <!-- Masonry Grid -->
        <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
            @forelse($posts as $post)
                <div class="break-inside-avoid group">
                    <div class="relative bg-white/70 dark:bg-gray-900/40 backdrop-blur-xl rounded-2xl border border-white/20 dark:border-white/5 shadow-2xl overflow-hidden p-2 transition-all duration-500 hover:scale-[1.02] hover:shadow-emerald-500/10">
                        <!-- Platform Badge -->
                        <div class="absolute top-4 right-4 z-20 flex items-center gap-2 pointer-events-none">
                            <div class="px-3 py-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-full shadow-lg border border-gray-100 dark:border-white/10 flex items-center gap-1.5">
                                @if($post->platform == 'instagram')
                                    <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.058-1.69-.072-4.949-.072zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                @endif
                                <span class="text-[10px] font-black uppercase text-gray-700 dark:text-gray-300">{{ $post->platform }}</span>
                            </div>
                        </div>

                        <!-- Embed Content -->
                        <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800">
                             {!! $post->embed_code !!}
                        </div>

                        @if($post->caption)
                            <!-- Optional Custom Caption -->
                            <div class="p-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 italic">
                                    "{{ $post->caption }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="mb-6 inline-flex w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Coming Soon</h3>
                    <p class="text-gray-500 dark:text-gray-400">Our community posts are currently being curated. Stay tuned!</p>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- Footer --}}
    <x-footer-premium />

    <!-- Instagram Embed Script (Loaded once) -->
    <script async src="//www.instagram.com/embed.js"></script>

    <style>
        /* Specific tweaks for Instagram embed in our grid */
        .instagram-media {
            margin: 0 !important;
            min-width: 100% !important;
            width: 100% !important;
            border-radius: 1rem !important;
            border: none !important;
        }

        /* Ensure masonry works properly on columns */
        .break-inside-avoid {
            -webkit-column-break-inside: avoid;
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</div>
