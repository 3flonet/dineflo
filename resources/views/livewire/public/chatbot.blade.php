<div class="fixed bottom-6 right-6 z-[9999] font-sans" x-data="{ 
    isOpen: @entangle('isOpen'),
    init() {
        window.addEventListener('chatbot-redirect', event => {
            setTimeout(() => {
                window.open(event.detail.url, '_blank');
            }, 500);
        });
    }
}">
    <!-- Floating Button -->
    <button @click="isOpen = !isOpen" 
            class="flex items-center gap-3 bg-white dark:bg-gray-800 p-3 pr-6 rounded-full shadow-2xl border border-gray-100 dark:border-white/10 hover:shadow-primary-500/20 transition-all transform hover:scale-105 active:scale-95 group">
        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-lg animate-bounce group-hover:animate-none">
            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 0 5.414 0 12.05c0 2.123.552 4.197 1.603 6.042L0 24l6.102-1.602a11.834 11.834 0 005.944 1.602h.005c6.634 0 12.048-5.414 12.048-12.05 0-3.219-1.253-6.241-3.53-8.513z"/></svg>
        </div>
        <div>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest leading-none mb-1">Butuh Bantuan?</p>
            <p class="text-sm font-black text-gray-800 dark:text-white leading-none">WhatsApp Kami</p>
        </div>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-16 right-0 w-[350px] h-[500px] bg-white dark:bg-gray-900 rounded-[1.5rem] shadow-3xl overflow-hidden border border-gray-100 dark:border-white/10 flex flex-col"
         x-cloak>
        
        <!-- Header (Fixed) -->
        <div class="bg-[#075e54] text-white p-5 flex justify-between items-center shadow-lg shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full bg-white/20 overflow-hidden border-2 border-white/50">
                        @if($settings->chatbot_avatar)
                            <img src="{{ Storage::url($settings->chatbot_avatar) }}" alt="Bot" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($settings->chatbot_name) }}&background=0D8ABC&color=fff" alt="Bot" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 rounded-full border-2 border-[#075e54]"></div>
                </div>
                <div>
                    <h4 class="font-bold text-lg leading-none mb-1">{{ $settings->chatbot_name }}</h4>
                    <p class="text-[10px] text-white/70 font-medium">Balasan dalam 1 menit</p>
                </div>
            </div>
            <button @click="isOpen = false" class="text-white/70 hover:text-white transition group">
                <svg class="w-6 h-6 transform group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Chat Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto no-scrollbar relative bg-[#e5ddd5] dark:bg-gray-950/50" 
             id="chat-body" 
             x-init="$watch('$wire.messages', () => $nextTick(() => $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' }))); 
                     $watch('$wire.isTyping', () => $nextTick(() => $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' })));"
             x-effect="$nextTick(() => { $el.scrollTop = $el.scrollHeight })">
            
            <div class="relative min-h-full">
                {{-- Background Layer --}}
                <div class="absolute inset-0 opacity-[0.1] dark:opacity-[0.05] pointer-events-none z-0" 
                     style="background-image: url('{{ $settings->chatbot_background_image ? Storage::url($settings->chatbot_background_image) : 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\' viewBox=\'0 0 80 80\'%3E%3Cg fill=\'%236e6e6e\' fill-opacity=\'0.2\'%3E%3Ccircle cx=\'10\' cy=\'10\' r=\'1\'/%3E%3Ccircle cx=\'40\' cy=\'40\' r=\'1.2\'/%3E%3Ccircle cx=\'70\' cy=\'10\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E' }}'); 
                            background-size: {{ $settings->chatbot_background_image ? 'cover' : '80px' }}; 
                            background-repeat: repeat;"></div>
                
                {{-- Messages Layer --}}
                <div class="relative z-10 p-4 space-y-3">
                    @foreach($messages as $msg)
                        <div wire:key="{{ $msg['id'] }}" class="flex {{ $msg['type'] == 'user' ? 'justify-end' : 'justify-start' }} animate-in fade-in slide-in-from-bottom-2 duration-300">
                            <div class="max-w-[85%] p-3 rounded-lg shadow-sm relative {{ $msg['type'] == 'user' ? 'bg-[#dcf8c6] dark:bg-emerald-900' : 'bg-white dark:bg-gray-800' }}">
                                <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed font-medium whitespace-pre-line">{{ $msg['text'] }}</p>
                                <p class="text-[9px] text-gray-400 dark:text-gray-500 text-right mt-1 font-bold">{{ $msg['time'] }}</p>
                                
                                {{-- Perfect WhatsApp Tail --}}
                                <span class="absolute top-0 {{ $msg['type'] == 'user' ? '-right-[8px]' : '-left-[8px]' }} w-[12px] h-[12px] {{ $msg['type'] == 'user' ? 'text-[#dcf8c6] dark:text-emerald-900' : 'text-white dark:text-gray-800' }}">
                                    <svg viewBox="0 0 10 10" class="w-full h-full fill-current {{ $msg['type'] == 'user' ? 'transform scale-x-[-1]' : '' }}">
                                        <path d="M10 0 L0 0 L10 10 Z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @endforeach

                    {{-- Typing Indicator --}}
                    @if($isTyping)
                    <div class="flex justify-start animate-in fade-in slide-in-from-bottom-2 duration-300">
                        <div class="bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-sm flex gap-1.5 items-center relative">
                             {{-- Bot Tail for typing --}}
                             <span class="absolute top-0 -left-[8px] w-[12px] h-[12px] text-white dark:text-gray-800">
                                <svg viewBox="0 0 10 10" class="w-full h-full fill-current transform">
                                    <path d="M10 0 L0 0 L10 10 Z" />
                                </svg>
                            </span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer / Input (Fixed at Bottom) -->
        <form wire:submit.prevent="sendMessage" class="p-4 bg-[#f0f2f5] dark:bg-gray-800 flex gap-2 items-center shrink-0 border-t dark:border-white/5">
            <input type="text" 
                   wire:model.live="userInput"
                   placeholder="Tuliskan Pesan Anda" 
                   class="flex-1 bg-white dark:bg-gray-900 border-none rounded-full px-5 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 dark:text-white shadow-sm"
                   {{ $step >= 5 || $isTyping ? 'disabled' : '' }}>
            
            <button type="submit" 
                    class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-emerald-600 transition-all active:scale-90 disabled:opacity-50"
                    {{ $step >= 5 || $isTyping || empty(trim($userInput)) ? 'disabled' : '' }}>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </form>
    </div>
</div>
