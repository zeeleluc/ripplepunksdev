<div class="max-w-6xl mx-auto bg-white border rounded">

    <div class="overflow-x-auto">
        <table class="w-full table-auto border border-gray-300">
            <tbody>
            @foreach ($holders as $index => $holder)
                @if (env('CTO_WALLET') !== $holder->owner)
                    <tr>
                        <td class="border px-4 py-2 align-middle text-xl text-center">
                            {{ ($holders->firstItem() ?? 0) + $index - 1 }}
                        </td>
                        <td class="border px-4 py-6 align-top text-center">
                            <strong>{{ $holder->owner }}</strong>

                            <!-- Mini badge table -->
                            @php
                                $userBadges = \App\Models\User::getStickersForWallet($holder->owner);
                            @endphp

                            <table class="w-full px-0 mt-3 text-xs text-center">
                                <tbody>
                                @foreach ($tiers as $count => $badges)
                                    <tr>
                                        @foreach ($badges as $badge)
                                            <td class="py-1">
                                                <span class="@if(in_array($badge, $userBadges)) bg-primary-200 text-primary-900 @else bg-gray-100 text-gray-300 @endif font-medium px-2.5 py-0.5 rounded-full">
                                                    {{ $badge }}
                                                </span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>

                        <td class="border px-4 py-2 align-middle text-xl text-center">{{ $holder->nft_count }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Custom Pagination -->
    <div class="mt-6 flex items-center justify-center space-x-1 text-sm py-5">

        @php
            $current = $holders->currentPage();
            $last = $holders->lastPage();
            $range = 2; // min 2 pages around current
            $pagesToShow = [];

            // Always show first two
            $pagesToShow[] = 1;
            $pagesToShow[] = 2;

            // Pages around current
            for ($i = $current - $range; $i <= $current + $range; $i++) {
                if ($i > 2 && $i < $last - 1) {
                    $pagesToShow[] = $i;
                }
            }

            // Always show last two
            $pagesToShow[] = $last - 1;
            $pagesToShow[] = $last;

            // Remove invalid and duplicate entries
            $pagesToShow = array_unique(array_filter($pagesToShow));
            sort($pagesToShow);
        @endphp

        {{-- Render pages with "..." --}}
        @php $previous = 0; @endphp
        @foreach ($pagesToShow as $page)
            @if ($previous && $page > $previous + 1)
                <span class="px-2">...</span>
            @endif

            <button
                wire:click="goToPage({{ $page }})"
                class="px-3 py-1 border rounded {{ $page === $current ? 'bg-primary-600 text-white font-bold' : 'bg-white' }}">
                {{ $page }}
            </button>

            @php $previous = $page; @endphp
        @endforeach

    </div>

    <p class="m-4 italic text-sm">
        Please note that {{ \App\Models\Nft::ctoWalletCount() }} RipplePunks held by the CTO wallet have been excluded from this list.
    </p>

</div>
