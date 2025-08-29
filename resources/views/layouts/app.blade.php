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

{{-- Homepage Header (desktop only) --}}
@if (request()->is('/'))
    <header class="hidden sm:flex p-10 bg-primary-600 text-white shadow justify-between items-center relative">
        <!-- Big Centered Title -->
        <h1 class="text-7xl font-extrabold text-center flex-1">
            <a href="{{ url('/') }}" class="font-karmatic">
                {{ config('app.name') }}
            </a>
        </h1>

        <!-- Right: Auth Button -->
        <div class="absolute right-10 top-14">
            @auth
                <form method="POST" action="{{ route('xaman.logout') }}">
                    @csrf
                    <button type="submit" class="bg-white text-primary-700 text-lg px-4 py-2 rounded hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('xaman.login') }}" class="bg-white text-primary-700 text-lg px-4 py-2 rounded hover:bg-gray-100">
                    Login
                </a>
            @endauth
        </div>
    </header>
@endif

{{-- Default Header (all pages, incl. homepage on mobile) --}}
<header class="p-4 sm:p-6 bg-primary-600 text-white shadow flex flex-wrap justify-between items-center
    @if (request()->is('/')) sm:hidden @endif">
    <!-- Left: Logo -->
    <div class="flex flex-wrap items-center gap-3 sm:gap-6">
        <h1 class="text-base sm:text-xl font-bold">
            <a href="{{ url('/') }}" class="font-karmatic">
                {{ config('app.name') }}
            </a>
        </h1>
    </div>

    <!-- Right: Auth Button -->
    <div>
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


    <div class="bg-primary-700 shadow text-sm text-white text-center px-6 py-2 font-bold">
        1 XRP &nbsp;is&nbsp; ~${{ number_format(\App\Helpers\XRP::getRate(), 2) }}
    </div>

    @auth
        <!-- Wallet & Badges Bar -->
        <div class="bg-white shadow text-sm text-gray-700 px-4 py-2 flex items-center justify-between gap-2 border-b flex-nowrap overflow-hidden">
            <!-- Wallet + NFT Count -->
            <div class="flex items-center gap-2 flex-shrink min-w-0">
                <span class="truncate font-medium max-w-[40vw] sm:max-w-[30vw]">
                    {{ Auth::user()->wallet }}
                </span>
            </div>

            @php
                $holder = \App\Models\Holder::where('wallet', Auth::user()->wallet)->first();
                $badges = $holder?->badges ?? [];
                $first = $badges[0] ?? null;
                $extra = max(count($badges) - 1, 0);
            @endphp

                <!-- Badges -->
            <div class="flex items-center gap-1 flex-shrink-0 min-w-0">
                @if ($first)
                    <a href="{{ route('badges') }}">
                        <span class="bg-primary-600 text-white text-xs font-medium px-2 py-1 rounded truncate max-w-[20vw]">
                            {{ $first }}
                        </span>
                    </a>

                    @if ($extra > 0)
                        <a href="{{ route('badges') }}">
                            <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded truncate max-w-[12vw]">
                                +{{ $extra }}
                            </span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('badges') }}">
                        <span class="bg-gray-100 text-gray-500 text-xs font-medium px-2 py-1 rounded truncate max-w-[32vw]">
                            Available Badges
                        </span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Navigation Bar -->
        <div class="bg-white shadow text-sm text-gray-700 px-2 py-1 flex items-center justify-start gap-1 flex-nowrap overflow-x-auto border-b">
            @if (Auth::user()->isAdmin())
                <a href="{{ route('admin.users') }}" class="flex-shrink-0 px-2 py-1 hover:underline whitespace-nowrap">
                    Users
                </a>
                <a href="{{ route('admin.claims') }}" class="flex-shrink-0 px-2 py-1 hover:underline whitespace-nowrap">
                    Claims
                </a>
                <a href="{{ route('admin.log-entry') }}" class="flex-shrink-0 px-2 py-1 hover:underline whitespace-nowrap">
                    Logs
                </a>
            @else
                <a href="{{ route('holder', ['wallet' => Auth::user()->wallet]) }}" class="flex-shrink-0 px-2 py-1 hover:underline whitespace-nowrap">
                    Your Profile
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
