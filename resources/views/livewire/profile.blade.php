<div class="bg-white border rounded p-5 text-center">
    {{ $user->wallet }}
    <br />
    <strong>
        {{ $user->totalNFTs() }} RipplePunks
    </strong>

    @php
        $stickers = \App\Models\User::getStickersForWallet($user->wallet);
        $first = $stickers[0] ?? null;
        $extra = count($stickers) - 1;
    @endphp

    <div class="mt-3">
        @if ($first)
            <a href="{{ route('badges') }}">
                <span class="bg-primary-600 mr-2 text-white text-xs font-medium px-2 py-1 rounded-lg">
                    {{ $first }}
                </span>
            </a>

            @if ($extra > 0)
                <a href="{{ route('badges') }}">
                    <span class="bg-gray-200 mr-2 text-gray-700 text-xs font-medium px-2 py-1 rounded-lg">
                        +{{ $extra }}
                    </span>
                </a>
            @endif
        @else
            <a href="{{ route('badges') }}">
                <span class="bg-gray-100 mr-2 text-gray-500 text-xs font-medium px-2 py-1 rounded-lg">
                    Available Badges
                </span>
            </a>
        @endif
    </div>
</div>
