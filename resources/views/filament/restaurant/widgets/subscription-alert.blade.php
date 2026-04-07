<x-filament-widgets::widget>
    @if($visible)
        @php
            $colors = [
                'danger' => [
                    'bg' => 'bg-red-500/10',
                    'border' => 'border-red-500/20',
                    'text' => 'text-red-900 dark:text-red-200',
                    'icon' => 'text-red-500',
                    'glow' => 'shadow-red-500/20',
                    'btn' => 'danger',
                    'accent' => 'bg-red-500',
                ],
                'warning' => [
                    'bg' => 'bg-amber-500/10',
                    'border' => 'border-amber-500/20',
                    'text' => 'text-amber-900 dark:text-amber-200',
                    'icon' => 'text-amber-500',
                    'glow' => 'shadow-amber-500/20',
                    'btn' => 'warning',
                    'accent' => 'bg-amber-500',
                ],
                'info' => [
                    'bg' => 'bg-indigo-500/10',
                    'border' => 'border-indigo-500/20',
                    'text' => 'text-indigo-900 dark:text-indigo-200',
                    'icon' => 'text-indigo-500',
                    'glow' => 'shadow-indigo-500/20',
                    'btn' => 'info',
                    'accent' => 'bg-indigo-500',
                ],
                'primary' => [
                    'bg' => 'bg-gray-500/10',
                    'border' => 'border-gray-500/20',
                    'text' => 'text-gray-900 dark:text-gray-200',
                    'icon' => 'text-gray-500',
                    'glow' => 'shadow-gray-500/20',
                    'btn' => 'gray',
                    'accent' => 'bg-gray-500',
                ]
            ];
            
            $c = $colors[$color] ?? $colors['primary'];
        @endphp

        <div class="relative overflow-hidden rounded-2xl border {{ $c['bg'] }} {{ $c['border'] }} p-6 shadow-lg {{ $c['glow'] }} backdrop-blur-sm transition-all duration-300 hover:shadow-xl mb-6">
            <!-- Decorative Background Element -->
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full {{ $c['accent'] }} opacity-5 blur-3xl"></div>
            
            <div class="relative flex items-center gap-4">
                <!-- Icon with Pulse Effect -->
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $c['bg'] }} ring-1 {{ $c['border'] }}">
                    <x-filament::icon
                        :icon="$icon"
                        class="h-6 w-6 {{ $c['icon'] }} {{ $color === 'danger' || $color === 'warning' ? 'animate-pulse' : '' }}"
                    />
                </div>

                <!-- Content -->
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h4 class="text-lg font-extrabold tracking-tight {{ $c['text'] }}">
                                {{ $title }}
                            </h4>
                            <p class="mt-1 text-sm font-medium opacity-80 {{ $c['text'] }}">
                                {{ $message }}
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div class="flex-shrink-0">
                            <x-filament::button
                                size="md"
                                :color="$c['btn']"
                                tag="a"
                                :href="$url"
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                                class="rounded-xl shadow-md transition-transform active:scale-95"
                            >
                                {{ $color === 'info' ? 'Lihat Paket' : 'Perpanjang Sekarang' }}
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
