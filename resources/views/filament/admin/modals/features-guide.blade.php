<div class="space-y-4">
    <div class="flex items-center justify-between p-4 bg-primary-50 dark:bg-primary-900/10 rounded-2xl border border-primary-100 dark:border-primary-500/20">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-500 rounded-lg text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="font-black text-gray-900 dark:text-white leading-tight">Panduan Fitur Dineflo</h3>
                <p class="text-xs text-gray-400 font-medium">Pelajari fungsi setiap fitur sebelum mengaktifkannya di paket langganan.</p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-white/5">
                <tr>
                    <th class="px-6 py-4 font-black uppercase tracking-wider text-[10px] text-gray-500 dark:text-gray-400">Fitur</th>
                    <th class="px-6 py-4 font-black uppercase tracking-wider text-[10px] text-gray-500 dark:text-gray-400">Kegunaan & Fungsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-800/50">
                @php
                    $siteName = app(\App\Settings\GeneralSettings::class)->site_name ?? config('app.name', 'Dineflo');
                @endphp
                @foreach(\App\Models\AppFeature::orderBy('title')->get() as $feature)
                @php
                    $shortDesc = str_replace([':site_name', 'Dineflo'], $siteName, $feature->short_description ?? '-');
                    $fullDesc  = str_replace([':site_name', 'Dineflo'], $siteName, $feature->long_description ?? '-');
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                    <td class="px-6 py-4 align-top whitespace-nowrap">
                        <div class="flex items-center gap-2">
                             @if($feature->icon)
                                @svg($feature->icon, 'w-4 h-4 text-primary-500')
                            @endif
                            <span class="font-bold text-gray-900 dark:text-white">{{ $feature->title }}</span>
                        </div>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $feature->badge === 'Premium' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                            {{ $feature->badge }}
                        </span>
                    </td>
                    <td class="px-6 py-4 leading-relaxed text-gray-600 dark:text-gray-400">
                        <p class="font-medium text-gray-800 dark:text-gray-200 mb-1 italic text-xs">"{{ $shortDesc }}"</p>
                        <p class="text-[13px] line-clamp-2 group-hover:line-clamp-none transition-all duration-300">{{ $fullDesc }}</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="p-3 text-center bg-gray-50 dark:bg-gray-800/20 rounded-xl border border-dashed border-gray-200 dark:border-white/10">
        <p class="text-[10px] text-gray-400 italic">Gunakan fitur-fitur ini secara bijak untuk menyusun strategi paket yang kompetitif.</p>
    </div>
</div>
