<div class="bg-white border rounded p-5 text-center">
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
                <span class="bg-primary-600 mr-2 text-white text-xs font-medium px-2 py-1 rounded-lg">
                    {{ $first }}
                </span>
            </a>

            @if ($extra > 0)
                <a href="{{ route('badges', ['wallet' => $holder->wallet]) }}">
                    <span class="bg-gray-200 mr-2 text-gray-700 text-xs font-medium px-2 py-1 rounded-lg">
                        +{{ $extra }}
                    </span>
                </a>
            @endif
        @else
            <a href="{{ route('badges', ['wallet' => $holder->wallet]) }}">
                <span class="bg-gray-100 mr-2 text-gray-500 text-xs font-medium px-2 py-1 rounded-lg">
                    Available Badges
                </span>
            </a>
        @endif
    </div>

    <div class="my-3 text-lg">
        {{ $holder->holdings }} RipplePunks
    </div>

    @livewire('nft-grid', ['owner' => $holder->wallet])
</div>
