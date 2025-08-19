<div>

    <div class="max-w-6xl mx-auto bg-white border rounded">

        <div class="overflow-x-auto">
            <table class="w-full table-auto border border-gray-300">
                <tbody>
                @foreach ($holders as $index => $holder)
                    @if (env('PROJECT_WALLET') === $holder->owner)
                        <tr class="bg-yellow-50">
                    @else
                        <tr>
                            @endif
                            <td class="border px-4 py-2 align-middle text-xl text-center min-w-[100px] max-w-[100px]">
                                {{ $holders->firstItem() + $index }}
                            </td>
                            <td class="border px-4 py-6 align-top text-center">
                                <strong>{{ $holder->owner }}</strong>
                                @if (env('PROJECT_WALLET') === $holder->owner)
                                    <div>
                                        <p>
                                            This holding is from the <strong>project's wallet</strong>.<br>
                                            Fresh mints from <em>The Other Punks</em> are listed at <strong>2 XRP</strong>.<br>
                                            If you see any listed higher, they were bought back from <em>paper hands</em>
                                            who sold below 2 XRP, then relisted with <strong>+2 XRP</strong> added.
                                        </p>
                                    </div>
                                @else
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
                                @endif
                            </td>
                            <td class="border px-4 py-2 align-middle text-xl text-center min-w-[100px] max-w-[100px]">{{ $holder->nft_count }}</td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>

        <p class="m-4 italic text-sm">
            Please note that {{ \App\Models\Nft::ctoWalletCount() }} RipplePunks held by the CTO wallet have been excluded from this list.
        </p>

    </div>

    @include('components.custom-pagination', ['paginator' => $holders])

</div>
