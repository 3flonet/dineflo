<div class="w-full mt-16 mb-8 relative group">
    {{-- Decorative Background Gradients --}}
    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 rounded-[2rem] blur-xl group-hover:blur-2xl transition-all duration-300 opacity-70"></div>
    
    <div class="relative bg-[#111827] rounded-[2rem] p-8 sm:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.5)] border border-white/5 flex flex-col md:flex-row items-center gap-10 overflow-hidden">
        
        {{-- Inner Decorative Elements --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full mix-blend-screen filter blur-3xl opacity-70 translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-amber-500/10 rounded-full mix-blend-screen filter blur-3xl opacity-70 -translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

        {{-- Left: Text Content --}}
        <div class="w-full md:w-1/2 relative z-10 text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 mb-6 backdrop-blur-sm">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <span class="text-[11px] font-bold tracking-wider uppercase text-gray-300">Gabung Partner Dineflo</span>
            </div>
            
            <h3 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-4 tracking-tight">
                Kembangkan <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">Bisnis Kuliner Anda</span>
            </h3>
            
            <p class="text-gray-400 font-medium text-base sm:text-lg leading-relaxed mb-6">
                Kembangkan Bisnis Kuliner Anda. Bergabunglah dengan ratusan pelaku usaha kuliner lainnya. Mulai digitalisasi operasional dan tingkatkan omset dengan ekosistem terpadu dari Dineflo.
            </p>
        </div>

        {{-- Right: Form --}}
        <div class="w-full md:w-1/2 relative z-10">
            <div class="bg-[#0B0F19]/50 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-white/5 shadow-inner">
                <form wire:submit.prevent="startRegistration" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        {{-- Name Input --}}
                        <div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="name"
                                placeholder="Nama Restoran Anda" 
                                class="w-full px-5 py-4 rounded-xl border-white/10 bg-white/5 text-white placeholder-gray-500 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 shadow-sm font-medium"
                            >
                        </div>

                        {{-- Slug Input & Status --}}
                        <div class="relative">
                            <div class="flex items-center bg-white/5 rounded-xl border-white/10 border focus-within:ring-1 focus-within:ring-amber-500 focus-within:border-amber-500 transition-all duration-200">
                                <span class="pl-5 pr-1 text-gray-400 text-sm font-mono select-none">dineflo.com/</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="slug"
                                    placeholder="slug-restoran" 
                                    class="flex-1 pr-12 py-4 border-none bg-transparent text-white placeholder-gray-600 focus:ring-0 shadow-none font-mono text-sm"
                                >
                            </div>
                            
                            {{-- Availability Indicator --}}
                            <div class="absolute right-4 top-1/2 -translate-y-1/2">
                                @if($isAvailable === true)
                                    <div class="flex items-center text-emerald-400 bg-emerald-400/10 border border-emerald-400/20 p-1.5 rounded-lg" title="Slug tersedia!">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4.13-5.69Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @elseif($isAvailable === false)
                                    <div class="flex items-center text-rose-400 bg-rose-400/10 border border-rose-400/20 p-1.5 rounded-lg" title="Maaf, slug sudah dipakai">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        @if($isAvailable !== true) disabled @endif
                        class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 flex items-center justify-center gap-2 group"
                    >
                        Daftar & Bangun Sistem Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    @if($isAvailable === false)
                        <p class="text-[13px] text-center text-rose-400 font-medium">Ops! Nama tersebut sudah digunakan bisnis lain.</p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
