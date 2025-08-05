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

<header class="p-4 bg-primary-600 text-white shadow flex justify-between items-center">
    <h1 class="text-xl font-bold">
        <a href="{{ url('/') }}" class="hover:underline">
            {{ config('app.name') }}
        </a>
    </h1>

    @auth
        <form method="POST" action="{{ route('xaman.logout') }}">
            @csrf
            <button type="submit" class="bg-white text-primary-700 px-4 py-2 rounded hover:bg-gray-100">
                Logout
            </button>
        </form>
    @else
        <a href="{{ route('xaman.login') }}" class="bg-white text-primary-700 px-4 py-2 rounded hover:bg-gray-100">
            Login
        </a>
    @endauth
</header>

@auth
    <div class="bg-white shadow text-sm text-gray-700 px-4 py-2 flex items-center justify-between border-b">
        {{ Auth::user()->wallet }}
        @if (Auth::user()->isAdmin())
            <a href="{{ route('admin.supply') }}">
                Manage Supply
            </a>
        @endif
    </div>
@endauth

<main class="container mx-auto p-4">
    @yield('content')
</main>

@livewire('footer')

@livewireScripts
</body>
</html>
