<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 text-gray-900">

<header class="p-4 bg-primary text-white shadow">
    <h1 class="text-xl font-bold">
        <a href="{{ url('/') }}" class="hover:underline">
            {{ config('app.name') }}
        </a>
    </h1>
</header>

<main class="container mx-auto p-4">
    @yield('content')
</main>

<footer class="p-4 text-center text-sm text-gray-600">
    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</footer>

@livewireScripts
</body>
</html>
