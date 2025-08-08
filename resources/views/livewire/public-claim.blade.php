<div class="bg-white border rounded p-5 text-center">
    {{-- Title / description / prize --}}
    <h2 class="text-xl font-bold">{{ $claim->title }}</h2>
    <p class="mt-1 text-sm text-gray-600">{{ $claim->description }}</p>
    <p class="mt-2 font-semibold">{{ $claim->prize }}</p>

    {{-- Required badges as pills --}}
    <div class="flex flex-wrap justify-center gap-2 mt-2">
        @foreach(explode(',', $claim->required_badges ?? '') as $badge)
            @if(trim($badge) !== '')
                <span class="inline-block bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    {{ trim($badge) }}
                </span>
            @endif
        @endforeach
    </div>

    {{-- Per-card messages: prefer local $message/$error, fall back to session --}}
    @if (!empty($message))
        <div class="bg-green-200 p-2 mt-3 rounded">{{ $message }}</div>
    @elseif (session()->has('message'))
        <div class="bg-green-200 p-2 mt-3 rounded">{{ session('message') }}</div>
    @endif

    @if (!empty($error))
        <div class="bg-red-200 p-2 mt-3 rounded">{{ $error }}</div>
    @elseif (session()->has('error'))
        <div class="bg-red-200 p-2 mt-3 rounded">{{ session('error') }}</div>
    @endif

    {{-- Claim button (only when there are open spots) --}}
    @if (!($claim->isFull ?? false))
        <button
            wire:click="claimNow"
            class="mt-6 mb-4 bg-gray-600 text-white px-6 py-3 rounded hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-lg"
            {{ ($hasClaimed ?? false) ? 'disabled' : '' }}>
            {{ ($hasClaimed ?? false) ? 'Already Claimed' : 'Claim Now' }}
        </button>
    @else
        <p class="mt-6 text-red-600 font-semibold">All claim spots have been filled.</p>
    @endif

    {{-- Spots grid (responsive) --}}
    @php
        $claimedCount = $submissions->count() ?? 0;
        $totalPlots = $claim->total ?? 0;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mt-6">
        @for ($i = 1; $i <= $totalPlots; $i++)
            @if ($i <= $claimedCount)
                @php
                    $submission = $submissions[$i - 1];
                    $isDistributed = !empty($submission->received_at);
                @endphp

                <div class="p-4 bg-primary-500 rounded text-white text-center break-words relative">
                    Claimed by<br />
                    <span class="text-xs">
                        {{ isset($submission->user->wallet)
                            ? substr($submission->user->wallet, 0, 6) . '...' . substr($submission->user->wallet, -4)
                            : 'Unknown'
                        }}
                    </span>

                    @if ($isDistributed)
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-2 right-2 h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </div>
            @else
                <div class="p-4 bg-gray-200 rounded text-center text-gray-500">
                    Open Spot<br />
                    <span class="text-xs">Claimable</span>
                </div>
            @endif
        @endfor
    </div>

    {{-- Missing badges modal (per-card state) --}}
    @if (!empty($confirmingMissingBadges))
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
                <h3 class="font-semibold mb-4 text-lg">Missing Required Badge{{ count($missingBadgesList ?? []) > 1 ? 's' : '' }}</h3>
                <p class="mb-4">You are missing the following required badge{{ count($missingBadgesList ?? []) > 1 ? 's' : '' }}:</p>

                <div class="mb-6 text-center">
                    @foreach ($missingBadgesList as $badge)
                        <span class="inline-block bg-primary-500 text-white px-3 mb-2 mx-1 py-1 rounded-full text-sm font-semibold">
                            {{ $badge }}
                        </span>
                    @endforeach
                </div>

                <p class="text-red-600 pb-3 text-sm">
                    Please note that it may take up to 20 minutes for the badges table to update with recent RipplePunk purchases.
                </p>

                <button wire:click="$set('confirmingMissingBadges', false)" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100 mr-2">
                    Acknowledged
                </button>
            </div>
        </div>
    @endif
</div>
