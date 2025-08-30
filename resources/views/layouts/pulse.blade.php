<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 text-gray-900">

    <header class="flex justify-center items-center p-6 sm:p-10 bg-primary-600 text-white shadow relative">
        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-7xl font-extrabold text-center">
            <a href="{{ url('/') }}" class="font-karmatic">
                XRPL NFT PULSE
            </a>
        </h1>
    </header>

    <div class="bg-primary-700 shadow text-xs sm:text-base text-white text-center px-6 py-2 font-normal sm:font-bold">
        Pulse the heartbeat of XRPL NFT sales over the past 24 hours.
    </div>

<main class="container mx-auto p-4">
    @yield('content')
</main>

@livewire('footer')

@livewireScripts
</body>
</html>
