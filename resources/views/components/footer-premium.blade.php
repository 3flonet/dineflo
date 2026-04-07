@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<footer class="bg-white dark:bg-[#070A12] border-t border-gray-200 dark:border-white/5 py-12 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-8">
            <!-- Brand & Description -->
            <div class="flex flex-col gap-4 max-w-sm">
                <div class="flex items-center gap-3">
                    @if($settings->site_logo)
                        <img src="{{ Storage::url($settings->site_logo) }}" alt="{{ $settings->site_name }}" class="h-8 w-auto object-contain">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-indigo-600 dark:from-indigo-500 dark:to-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" /></svg>
                        </div>
                    @endif
                    <span class="font-bold text-gray-900 dark:text-white text-lg">{{ $settings->site_name }}</span>
                </div>
                @if($settings->site_description)
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $settings->site_description }}</p>
                @endif
                <p class="text-xs text-gray-400 dark:text-gray-600 font-medium">
                    &copy; {{ date('Y') }} {{ $settings->site_author ?? $settings->site_name }}. All rights reserved.
                </p>
            </div>

            <!-- Navigation & Socials -->
            <div class="flex flex-col md:items-end gap-6">
                <!-- Links -->
                <nav class="flex flex-wrap gap-x-8 gap-y-3 text-sm font-bold">
                    <a href="{{ route('home') }}#fitur" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-colors">Fitur</a>
                    <a href="{{ route('home') }}#solusi" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-colors">Solusi</a>
                    <a href="{{ route('home') }}#harga" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-colors">Harga</a>
                    <a href="{{ route('filament.restaurant.auth.login') }}" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-colors">Login</a>
                    @if($settings->support_email)
                        <a href="mailto:{{ $settings->support_email }}" class="text-primary-600 dark:text-indigo-400 hover:underline transition-colors">{{ $settings->support_email }}</a>
                    @endif
                </nav>
                
                <!-- Social Icons -->
                <div class="flex gap-5">
                    @if($settings->site_facebook_url)
                        <a href="{{ $settings->site_facebook_url }}" target="_blank" class="text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-all transform hover:scale-110">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.354c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>
                        </a>
                    @endif
                    @if($settings->site_instagram_url)
                        <a href="{{ $settings->site_instagram_url }}" target="_blank" class="text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-all transform hover:scale-110">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" /></svg>
                        </a>
                    @endif
                    @if($settings->site_twitter_url)
                        <a href="{{ $settings->site_twitter_url }}" target="_blank" class="text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-all transform hover:scale-110">
                            <span class="sr-only">X</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13.6823 10.6218L20.2391 3H18.6854L13.0454 9.55393L8.50293 3H3.25049L10.1259 13H3.25049L14.7176 21H16.2713L10.7628 14.5461L15.3505 21H20.6029L13.6823 10.6218ZM11.55 13.5782L10.854 12.5822L5.31293 4.65434H7.69973L12.1645 11.0427L12.8605 12.0387L18.6862 20.3732H16.2994L11.55 13.5782Z" /></svg>
                        </a>
                    @endif
                    @if($settings->site_youtube_url)
                        <a href="{{ $settings->site_youtube_url }}" target="_blank" class="text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-all transform hover:scale-110">
                            <span class="sr-only">YouTube</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C22 8.618 22 12 22 12s0 3.382-.418 4.814a2.504 2.504 0 01-1.768 1.768C18.382 19 15 19 15 19s-3.382 0-4.814-.418a2.504 2.504 0 01-1.768-1.768C8 15.382 8 12 8 12s0-3.382.418-4.814a2.504 2.504 0 011.768-1.768C11.618 5 15 5 15 5s3.382 0 4.812.418zM14 15l5-3-5-3v6z" /></svg>
                        </a>
                    @endif
                    @if($settings->site_github_url)
                        <a href="{{ $settings->site_github_url }}" target="_blank" class="text-gray-400 hover:text-primary-600 dark:hover:text-amber-400 transition-all transform hover:scale-110">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" /></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
