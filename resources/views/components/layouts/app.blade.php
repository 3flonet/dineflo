<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @inject('settings', 'App\Settings\GeneralSettings')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-pub-key" content="{{ config('webpush.vapid.public_key') }}">
    
    <!-- ================= BASIC META ================= -->
    <title>{{ isset($title) ? $title . ' - ' : '' }}{{ $settings->site_name }}</title>
    <meta name="description" content="{{ $settings->site_description }}">
    @php
        $keywords = $settings->site_keywords;
        if (is_string($keywords) && (str_starts_with($keywords, '[') || str_starts_with($keywords, '{'))) {
            $keywords = json_decode($keywords, true);
        }
    @endphp
    <meta name="keywords" content="{{ is_array($keywords) ? implode(', ', $keywords) : $keywords }}">
    <meta name="author" content="{{ $settings->site_author ?? $settings->site_name }}">
    <meta name="robots" content="index, follow">
    <meta name="language" content="{{ app()->getLocale() }}">
    <meta name="revisit-after" content="7 days">

    <!-- ================= BRAND ================= -->
    <meta name="application-name" content="{{ $settings->site_name }}">
    <meta name="apple-mobile-web-app-title" content="{{ $settings->site_name }}">
    @include('filament.pwa-head')

    <!-- ================= CANONICAL ================= -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- ================= OPEN GRAPH ================= -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ isset($title) ? $title . ' - ' : '' }}{{ $settings->site_name }}">
    <meta property="og:description" content="{{ $settings->site_description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $settings->site_name }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    @if($settings->site_og_image || $settings->site_logo)
        <meta property="og:image" content="{{ asset(Storage::url($settings->site_og_image ?? $settings->site_logo)) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    <!-- ================= TWITTER CARD ================= -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ isset($title) ? $title . ' - ' : '' }}{{ $settings->site_name }}">
    <meta name="twitter:description" content="{{ $settings->site_description }}">
    @if($settings->site_og_image || $settings->site_logo)
        <meta name="twitter:image" content="{{ asset(Storage::url($settings->site_og_image ?? $settings->site_logo)) }}">
    @endif
    @if($settings->site_twitter_url)
        @php
            $twitterHandle = str_replace(['https://twitter.com/', 'https://x.com/', 'http://twitter.com/', 'http://x.com/'], '', $settings->site_twitter_url);
            $twitterHandle = ltrim(parse_url($settings->site_twitter_url, PHP_URL_PATH), '/');
            $twitterHandle = explode('/', $twitterHandle)[0] ?? $settings->site_twitter_url;
        @endphp
        <meta name="twitter:site" content="{{ str_starts_with($twitterHandle, '@') ? $twitterHandle : '@' . $twitterHandle }}">
    @endif

    <!-- ================= SaaS / PRODUCT SEO ================= -->
    <meta name="product-type" content="SaaS">
    <meta name="category" content="Restaurant Management Software">
    <meta name="coverage" content="Worldwide">
    <meta name="distribution" content="Global">

    <!-- ================= FAVICON ================= -->
    @if(isset($restaurant) && $restaurant->logo_square && $restaurant->owner?->hasFeature('Remove Branding'))
        <link rel="icon" type="image/png" href="{{ Storage::url($restaurant->logo_square) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url($restaurant->logo_square) }}">
    @elseif($settings->site_favicon)
        <link rel="icon" type="image/png" href="{{ Storage::url($settings->site_favicon) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url($settings->site_favicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Flaticon integrated via direct SVG -->
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    }
                }
            }
        }
    </script>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 20px); }
        .pt-safe { padding-top: env(safe-area-inset-top, 20px); }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05);
        }
        .dark .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }
        .text-gradient-gold {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #D97706, #EA580C);
        }
        .dark .text-gradient-gold {
            background-image: linear-gradient(90deg, #F59E0B, #F15A25);
        }
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
    @vite(['resources/js/app.js'])
    
    <!-- Midtrans Snap Script -->
    @php
        $midtransClientKey = $settings->midtrans_client_key ?? config('services.midtrans.client_key');
        $midtransIsProd = $settings->midtrans_is_production ?? config('services.midtrans.is_production');

        if (isset($restaurant) && ($restaurant->gateway_mode ?? '') === 'own' && !empty(trim($restaurant->midtrans_client_key ?? ''))) {
            $midtransClientKey = trim($restaurant->midtrans_client_key);
            $midtransIsProd = !str_starts_with($midtransClientKey, 'SB-');
        }
    @endphp

    @if($midtransIsProd)
        <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
    @else
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
    @endif
</head>
<body class="bg-gray-50 dark:bg-[#0B0F19] text-gray-900 dark:text-gray-100 transition-colors duration-300" x-data="{ 
    theme: localStorage.theme || 'system',
    initTheme() {
        this.updateTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.theme === 'system') this.updateTheme();
        });
    },
    updateTheme() {
        if (this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.theme = this.theme;
    }
}" x-init="initTheme()">
    
    <div class="min-h-screen flex flex-col">
        {{ $slot }}


        @if(!isset($hideLayoutFooter) || !$hideLayoutFooter)
            @if(!isset($restaurant) || !$restaurant->owner?->hasFeature('Remove Branding'))
                <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 shadow-sm mt-auto transition-colors duration-300">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col items-center gap-y-4 md:flex-row md:justify-between">
                            <div class="order-2 md:order-1">
                                <p class="text-sm leading-5 text-gray-500 dark:text-gray-400 text-center md:text-left font-medium">
                                    &copy; {{ date('Y') }} {{ $settings->site_author ?? '3FLO' }}, Inc. All rights reserved.
                                </p>
                            </div>
                            <div class="flex justify-center space-x-6 order-1 md:order-2">
                                @if($settings->site_facebook_url)
                                    <!-- Facebook -->
                                    <a href="{{ $settings->site_facebook_url }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                        <span class="sr-only">Facebook</span>
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.354c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>
                                    </a>
                                @endif
                                @if($settings->site_instagram_url)
                                    <!-- Instagram -->
                                    <a href="{{ $settings->site_instagram_url }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                        <span class="sr-only">Instagram</span>
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" /></svg>
                                    </a>
                                @endif
                                @if($settings->site_twitter_url)
                                    <!-- X (Twitter) -->
                                    <a href="{{ $settings->site_twitter_url }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                        <span class="sr-only">X</span>
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M13.6823 10.6218L20.2391 3H18.6854L13.0454 9.55393L8.50293 3H3.25049L10.1259 13H3.25049L14.7176 21H16.2713L10.7628 14.5461L15.3505 21H20.6029L13.6823 10.6218ZM11.55 13.5782L10.854 12.5822L5.31293 4.65434H7.69973L12.1645 11.0427L12.8605 12.0387L18.6862 20.3732H16.2994L11.55 13.5782Z" /></svg>
                                    </a>
                                @endif
                                @if($settings->site_github_url)
                                    <!-- GitHub -->
                                    <a href="{{ $settings->site_github_url }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                        <span class="sr-only">GitHub</span>
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" /></svg>
                                    </a>
                                @endif
                                @if($settings->site_youtube_url)
                                    <!-- YouTube -->
                                    <a href="{{ $settings->site_youtube_url }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                                        <span class="sr-only">YouTube</span>
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C22 8.618 22 12 22 12s0 3.382-.418 4.814a2.504 2.504 0 01-1.768 1.768C18.382 19 15 19 15 19s-3.382 0-4.814-.418a2.504 2.504 0 01-1.768-1.768C8 15.382 8 12 8 12s0-3.382.418-4.814a2.504 2.504 0 011.768-1.768C11.618 5 15 5 15 5s3.382 0 4.812.418zM14 15l5-3-5-3v6z" /></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </footer>
            @endif
        @endif
    </div>

    @livewireScripts
    
    <!-- Notification Toast -->
    <div x-data="{ 
            notifications: [],
            add(e) {
                // Livewire 3 dispatch mengirim payload sebagai e.detail[0]
                // Native CustomEvent mengirim langsung sebagai e.detail
                const payload = (Array.isArray(e.detail) ? e.detail[0] : e.detail) || {};
                this.notifications.push({
                    id: Date.now(),
                    type: payload.type || 'info',
                    message: payload.message || '',
                })
                setTimeout(() => {
                    this.notifications.shift()
                }, 3500)
            }
        }"
        @notify.window="add($event)"
        class="fixed bottom-4 right-4 z-[99] space-y-2 pointer-events-none"
    >
        <template x-for="note in notifications" :key="note.id">
            <div class="pointer-events-auto flex items-center gap-2 px-4 py-3 rounded-xl shadow-xl text-white font-semibold text-sm max-w-xs"
                 :class="{
                    'bg-green-600': note.type === 'success',
                    'bg-red-600':   note.type === 'error',
                    'bg-blue-600':  note.type === 'info',
                    'bg-amber-500': note.type === 'warning'
                 }"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-y-2"
             >
                <span x-text="note.type === 'success' ? '✅' : note.type === 'error' ? '❌' : note.type === 'warning' ? '⚠️' : 'ℹ️'" class="text-base"></span>
                <span x-text="note.message"></span>
            </div>
        </template>
    </div>

    <!-- Push Notification Prompt -->
    @auth
    <div x-data="{ 
            showPushPrompt: false,
            isSubscribed: false,
            
            async init() {
                if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
                
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.getSubscription();
                
                this.isSubscribed = !!subscription;
                
                // Show prompt if not subscribed and permission is default
                if (!this.isSubscribed && Notification.permission === 'default') {
                    setTimeout(() => { this.showPushPrompt = true; }, 3000);
                }
            },
            
            async subscribe() {
                try {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') throw new Error('Permission not granted');
                    
                    const registration = await navigator.serviceWorker.ready;
                    const vapidPublicKey = document.querySelector('meta[name=vapid-pub-key]').getAttribute('content');
                    
                    const convertedVapidKey = this.urlBase64ToUint8Array(vapidPublicKey);
                    
                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: convertedVapidKey
                    });
                    
                    // Send to our server
                    await fetch('/push-subscription', {
                        method: 'POST',
                        body: JSON.stringify(subscription),
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                        }
                    });
                    
                    this.isSubscribed = true;
                    this.showPushPrompt = false;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Notifications enabled!', type: 'success' }}));
                } catch (e) {
                    console.error('Failed to subscribe', e);
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Failed to enable notifications.', type: 'error' }}));
                }
            },
            
            urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }
        }"
        x-show="showPushPrompt"
        x-transition
        class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[98] w-[90%] md:w-auto"
        style="display: none;"
    >
        <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-4 md:p-6 flex flex-col sm:flex-row items-center gap-4 max-w-lg">
            <div class="bg-blue-100 p-3 rounded-full text-blue-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <div class="text-center sm:text-left flex-1 text-sm text-gray-700">
                <strong class="block text-gray-900 text-base mb-1">Stay Updated</strong>
                Enable notifications to know when tables are active or orders are ready.
            </div>
            <div class="flex gap-2 w-full sm:w-auto shrink-0 mt-2 sm:mt-0">
                <button @click="showPushPrompt = false" class="px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 rounded-lg transition-colors font-medium">Not Now</button>
                <button @click="subscribe()" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shadow-sm">Enable</button>
            </div>
        </div>
    </div>
    @endauth
    @livewire('public.chatbot')
</body>
</html>