@if(auth()->check() && (auth()->user()->hasRole('super_admin') || auth()->user()->hasFeature('Priority Support')))
<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">
    <div x-show="open" x-transition.opacity.duration.300ms class="mb-4 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-80 overflow-hidden transform origin-bottom-right">
        <div class="bg-gradient-to-r from-warning-500 to-warning-600 p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-s-star" class="w-5 h-5 text-warning-100" />
                    <span class="font-bold">Priority Support</span>
                </div>
                <button @click="open = false" class="text-white hover:text-warning-100 transition focus:outline-none">
                    <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-xs text-warning-50 mt-1">Layanan khusus untuk member VIP.</p>
        </div>
        <div class="p-6 text-center space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-300">Hubungi tim konsultan kami secara langsung melalui WhatsApp untuk respon instan.</p>
            <a href="https://wa.me/6281234567890?text=Halo%20Tim%20Dineflo,%20saya%20member%20Priority%20Support%20butuh%20bantuan." target="_blank" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-[#25D366] hover:bg-[#128C7E] text-white rounded-xl font-bold transition shadow-lg shadow-[#25D366]/30">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat via WhatsApp
            </a>
            <p class="text-xs text-gray-400">Jam Operasional: 24/7 untuk tier Anda.</p>
        </div>
    </div>
    <button @click="open = !open" 
            class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-warning-500 to-warning-600 rounded-full text-white shadow-xl shadow-warning-500/40 hover:scale-110 transition-transform focus:outline-none ring-4 ring-white dark:ring-gray-900 group">
        <x-filament::icon icon="heroicon-o-chat-bubble-left-ellipsis" class="w-6 h-6 group-hover:hidden" />
        <x-filament::icon icon="heroicon-s-star" class="w-6 h-6 text-warning-100 hidden group-hover:block" />
        <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 border-2 border-white dark:border-gray-900 rounded-full animate-pulse"></span>
    </button>
</div>
@endif
