<div>
    @if($isValid && !request()->routeIs('filament.restaurant.pages.my-subscription'))
        <a href="{{ route('filament.restaurant.pages.my-subscription', ['tenant' => $tenantSlug]) }}" 
           class="hidden md:flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-full hover:bg-amber-100 transition duration-150 shadow-sm ml-4">
            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-4 h-4 text-amber-600 dark:text-amber-400 animate-pulse" />
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-300 whitespace-nowrap">
                Langganan: {{ $daysLeft }} Hari Lagi
            </span>
        </a>
    @endif
</div>
