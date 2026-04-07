<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - Kitchen Display</title>
    
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div class="h-full w-full">
        {{ $slot }}
    </div>
    
    @filamentScripts
    @vite('resources/js/app.js')
</body>
</html>
