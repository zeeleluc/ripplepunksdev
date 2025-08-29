<div class="bg-white border rounded p-5 text-center">

    {{-- Alert for special wallets --}}
    @php
        $specialWallets = [
            env('CTO_WALLET') => 'CTO',
            env('PROJECT_WALLET') => 'Projects',
            env('REWARDS_WALLET') => 'Rewards',
        ];
    @endphp

    @if(array_key_exists($holder->wallet, $specialWallets))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-3 text-sm text-left rounded">
            ⚠️ This is the {{ $specialWallets[$holder->wallet] }} wallet.
        </div>
    @endif

    {{ $holder->wallet }}
    <br />

    @php
        $badges = $holder->badges ?? [];
        $first = $badges[0] ?? null;
        $extra = max(count($badges) - 1, 0);
    @endphp

    <div class="mt-3">
        @if ($first)
            <a href="{{ route('badges', ['wallet' => $holder->wallet]) }}">
                <span class="bg-primary-600 mr-2 text-white text-xs font-medium px-2 py-1 rounded">
                    {{ $first }}
                </span>
            </a>

            @if ($extra > 0)
                <a href="{{ route('badges', ['wallet' => $holder->wallet]) }}">
                    <span class="bg-gray-200 mr-2 text-gray-700 text-xs font-medium px-2 py-1 rounded">
                        +{{ $extra }}
                    </span>
                </a>
            @endif
        @else
            <a href="{{ route('badges', ['wallet' => $holder->wallet]) }}">
                <span class="bg-gray-100 mr-2 text-gray-500 text-xs font-medium px-2 py-1 rounded">
                    Available Badges
                </span>
            </a>
        @endif
    </div>

    <div class="my-3 text-lg">
        {{ $holder->holdings }} RipplePunks
    </div>

    <div class="my-3 text-lg">
        @if ($holder->voting_power >= 1)
            ⚡️ {{ $holder->voting_power }} Voting Power
        @else
            ⚡️ No Voting Power
        @endif
    </div>

    @livewire('nft-grid', ['owner' => $holder->wallet])
</div>
