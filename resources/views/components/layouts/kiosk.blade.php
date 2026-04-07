<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    
    <title>{{ $title ?? 'Kiosk Mode' }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Disable text selection and pull-to-refresh for proper app-like feel on kiosk devices */
        body {
            user-select: none;
            -webkit-user-select: none;
            overscroll-behavior-y: contain;
            background-color: #f3f4f6; /* bg-gray-100 */
        }
        /* Custom scrollbar for better kiosk look */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @livewireStyles
    @inject('settings', 'App\Settings\GeneralSettings')

    <!-- Midtrans Snap Script for Kiosk -->
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
<body class="font-sans antialiased text-gray-900 overflow-hidden h-screen flex flex-col">

    {{ $slot }}

    @livewireScripts(['update_route' => request()->root() . '/livewire/update'])
</body>
</html>
