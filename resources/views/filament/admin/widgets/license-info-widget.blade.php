<div class="fi-widget-card rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    @php
    $info = $this->getLicenseInfo();
    @endphp

    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            🔐 Informasi Lisensi
        </h3>
        @if($info['status_color'] === 'danger')
        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
            ⚠️ Kadaluarsa
        </span>
        @elseif($info['is_grace_period'])
        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
            ⏳ Grace Period
        </span>
        @elseif($info['status'] === 'active')
        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">
            ✓ Aktif
        </span>
        @endif
    </div>

    @if(!$info['is_configured'])
    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/30">
        <p class="text-sm text-blue-900 dark:text-blue-200">
            Lisensi belum dikonfigurasi.
            <a href="{{ route('installer.license') }}" class="font-semibold underline">
                Konfigurasi sekarang
            </a>
        </p>
    </div>
    @else
    <div class="space-y-3">
        <!-- License Key -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">Nomor Lisensi:</span>
            <code class="text-sm font-mono font-medium text-gray-900 dark:text-white">
                {{ $info['license_key'] }}
            </code>
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
            <span class="relative inline-flex h-3 w-3 rounded-full"
                @class([ 'bg-green-600'=> $info['status_color'] === 'success',
                'bg-red-600' => $info['status_color'] === 'danger',
                'bg-yellow-600' => $info['status_color'] === 'warning',
                'bg-gray-400' => $info['status_color'] === 'gray',
                ])>
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"
                    @class([ 'bg-green-600'=> $info['status_color'] === 'success',
                    'bg-red-600' => $info['status_color'] === 'danger',
                    'bg-yellow-600' => $info['status_color'] === 'warning',
                    'bg-gray-400' => $info['status_color'] === 'gray',
                    ])>
                </span>
            </span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['status_label'] }}</span>
        </div>

        <!-- Domain -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">Domain:</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['domain'] }}</span>
        </div>

        <!-- Customer Name -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">Pelanggan:</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['customer_name'] }}</span>
        </div>

        <!-- Last Ping -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">Ping Terakhir:</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['last_ping'] }}</span>
        </div>

        <!-- Grace Period (if active) -->
        @if($info['is_grace_period'])
        <div class="rounded-lg bg-yellow-50 p-3 dark:bg-yellow-900/30">
            <p class="text-xs text-yellow-900 dark:text-yellow-200">
                <strong>Grace Period Aktif</strong><br>
                Berlaku hingga: <strong>{{ $info['grace_period_until'] }}</strong>
            </p>
        </div>
        @elseif($info['expires_at'] && $info['expires_at'] !== '-')
        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
            <span class="text-sm text-gray-600 dark:text-gray-400">Kadaluarsa:</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['expires_at'] }}</span>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-4 flex gap-2">
            <a href="{{ route('filament.admin.resources.licenses.index') }}" class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white transition hover:bg-blue-700">
                🔧 Kelola Lisensi
            </a>
            @if($info['status'] !== 'active')
            <a href="{{ route('installer.license') }}" class="flex-1 rounded-lg bg-gray-600 px-4 py-2 text-center text-sm font-medium text-white transition hover:bg-gray-700">
                ↻ Reset
            </a>
            @endif
        </div>
    </div>
    @endif
</div>