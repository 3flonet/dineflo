<div class="fixed inset-x-0 top-0 z-50 flex items-center justify-center p-4 pt-4 sm:pt-0">
    <div class="w-full max-w-md rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4 shadow-lg dark:border-yellow-600 dark:bg-yellow-900/30">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-yellow-900 dark:text-yellow-200">
                    ⚠️ Grace Period Aktif
                </h3>
                <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                    Lisensi Anda berada dalam periode grace dan akan kadaluarsa dalam
                    <strong>{{ $daysRemaining }} hari {{ $hoursRemaining }} jam</strong>.
                </p>
                <p class="mt-2 text-xs text-yellow-700 dark:text-yellow-400">
                    Tanggal kadaluarsa: <strong>{{ \Carbon\Carbon::parse($gracePeriodUntil)->format('d F Y') }}</strong>
                </p>
                <div class="mt-3 flex gap-2">
                    <a href="https://wa.me/628123456789" target="_blank"
                        class="inline-block rounded bg-yellow-600 px-3 py-1 text-xs font-medium text-white hover:bg-yellow-700">
                        Hubungi Support
                    </a>
                    <a href="https://dineflo.test" target="_blank"
                        class="inline-block rounded bg-yellow-600 px-3 py-1 text-xs font-medium text-white hover:bg-yellow-700">
                        Perbarui Sekarang
                    </a>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="flex-shrink-0 text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>