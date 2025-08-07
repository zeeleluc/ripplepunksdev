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

<header class="p-4 sm:p-6 bg-primary-600 text-white shadow flex flex-wrap justify-between items-center">
    <!-- Left: Logo and Links -->
    <div class="flex flex-wrap items-center gap-3 sm:gap-6">
        <h1 class="text-base sm:text-xl font-bold">
            <a href="{{ url('/') }}" class="hover:underline">
                {{ config('app.name') }}
            </a>
        </h1>

        <!-- Outgoing Links -->
        <nav class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm font-medium">
            <a href="https://xrp.cafe/collection/ripplepunks" target="_blank" class="hover:underline flex items-center gap-0.5 sm:gap-1">
                xrp.cafe
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 sm:w-4 sm:h-4 inline-block ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                </svg>
            </a>
            <a href="https://bidds.com/collection/ripplepunks/" target="_blank" class="hover:underline flex items-center gap-0.5 sm:gap-1">
                bidds
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 sm:w-4 sm:h-4 inline-block ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                </svg>
            </a>
        </nav>
    </div>

    <!-- Right: Auth Button -->
    <div class="mt-2 sm:mt-0">
        @auth
            <form method="POST" action="{{ route('xaman.logout') }}">
                @csrf
                <button type="submit" class="bg-white text-primary-700 text-sm px-3 py-1 sm:px-4 sm:py-2 rounded hover:bg-gray-100">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('xaman.login') }}" class="bg-white text-primary-700 text-sm px-3 py-1 sm:px-4 sm:py-2 rounded hover:bg-gray-100">
                Login
            </a>
        @endauth
    </div>
</header>

    @auth
        <div class="bg-white shadow text-sm text-gray-700 px-6 py-2 flex items-center justify-between border-b">
            <span class="truncate max-w-[60%] overflow-hidden whitespace-nowrap">
                {{ Auth::user()->wallet }}
            </span>

            @if (Auth::user()->isAdmin())
                <a href="{{ route('admin.log-entry') }}" class="ml-4 flex-shrink-0">
                    Logs
                </a>
            @endif
        </div>

        <div class="bg-white shadow text-sm text-gray-700 px-6 py-2 flex items-center border-b">
        <span class="truncate max-w-[60%] overflow-hidden whitespace-nowrap mr-4">
            {{ Auth::user()->totalNFTs() }} RipplePunks
        </span>

            @php
                $stickers = \App\Models\User::getStickersForWallet(Auth::user()->wallet);
                $first = $stickers[0] ?? null;
                $extra = count($stickers) - 1;
            @endphp

            <div class="flex gap-2 items-center">
                @if ($first)
                    <a href="{{ route('badges') }}">
                    <span class="bg-primary-600 text-white text-xs font-medium px-2 py-1 rounded-lg">
                        {{ $first }}
                    </span>
                    </a>

                    @if ($extra > 0)
                        <a href="{{ route('badges') }}">
                        <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded-lg">
                            +{{ $extra }}
                        </span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('badges') }}">
                    <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-1 rounded-lg">
                        Available Badges
                    </span>
                    </a>
                @endif
            </div>
        </div>

    @endauth

<main class="container mx-auto p-4">
    @yield('content')
</main>

@livewire('footer')

@livewireScripts
</body>
</html>
