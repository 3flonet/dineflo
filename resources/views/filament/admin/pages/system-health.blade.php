<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($healthStatus as $key => $status)
            <x-filament::section>
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl {{ $status['healthy'] ? 'bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400' : 'bg-danger-100 dark:bg-danger-900/30 text-danger-600 dark:text-danger-400' }}">
                        @if ($status['healthy'])
                            <x-heroicon-o-check-circle class="w-8 h-8" />
                        @else
                            <x-heroicon-o-x-circle class="w-8 h-8" />
                        @endif
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white capitalize">
                            {{ str_replace('_', ' ', $key) }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $status['message'] }}
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    <!-- Performance Monitoring Placeholder Section -->
    <x-filament::section class="mt-8">
        <x-slot name="heading">
             Performance Monitoring & Alerts
        </x-slot>
        <x-slot name="description">
            To view detailed query performance and production errors.
        </x-slot>
        
        <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
            <p>
                <strong>Failed Jobs:</strong> Any failing background jobs (e.g. queue workers handling email/WA) will automatically trigger an email alert to the support address configured in General Settings.
            </p>
            <p>
                <strong>Query Performance:</strong> For advanced performance monitoring, including slow queries or N+1 issues when records exceed 10K, we recommend integrating tools such as <strong>Laravel Telescope</strong> (for local/staging debugging) or <strong>Sentry / Datadog</strong> (for production).
            </p>
            <p>
                <strong>External Uptime:</strong> You can link an external monitor (like UptimeRobot) to the <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded">/up</code> endpoint to receive external alerts when this panel is unreachable.
            </p>
        </div>
    </x-filament::section>

</x-filament-panels::page>
