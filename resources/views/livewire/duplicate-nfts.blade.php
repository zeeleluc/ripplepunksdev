@php
    $projectWallet = env('PROJECT_WALLET');
    $rewardsWallet = env('REWARDS_WALLET');
    $hackWallet    = 'rwbaCNkedtHacK8Qer3qdVZaH2fjSvBrJZ';

    $highlightWallets = [$projectWallet, $rewardsWallet, $hackWallet];

    function getWalletHighlightClass($wallet, $hackWallet, $highlightWallets) {
        if ($wallet === $hackWallet) {
            return 'bg-orange-300'; // hack wallet → orange
        }
        return in_array($wallet, $highlightWallets) ? 'bg-yellow-300' : '';
    }
@endphp

<div class="overflow-x-auto">
    @if ($nftsGrouped->isNotEmpty())
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($nftsGrouped as $group)
                @foreach($group->chunk(2) as $pair)
                    <tr>
                        {{-- NFT 1 Name --}}
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ $pair[0]->metadata['name'] ?? 'Unnamed NFT' }}
                        </td>

                        {{-- NFT 1 ID --}}
                        <td class="px-4 py-2 text-sm text-gray-700">
                            <a target="_blank" class="text-primary-600 underline" href="https://xrp.cafe/nft/{{ $pair[0]->nftoken_id }}">
                                #{{ $pair[0]->nft_id ?? '-' }}
                            </a>
                        </td>

                        {{-- NFT 1 Owner --}}
                        <td class="px-4 py-2 text-sm {{ getWalletHighlightClass($pair[0]->owner ?? null, $hackWallet, $highlightWallets) }}">
                            {{ $pair[0]->owner ?? '-' }}
                        </td>

                        {{-- NFT 2 ID --}}
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if(isset($pair[1]))
                                <a target="_blank" class="text-primary-600 underline" href="https://xrp.cafe/nft/{{ $pair[1]->nftoken_id }}">
                                    #{{ $pair[1]->nft_id }}
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        {{-- NFT 2 Owner --}}
                        <td class="px-4 py-2 text-sm {{ isset($pair[1]) ? getWalletHighlightClass($pair[1]->owner, $hackWallet, $highlightWallets) : '' }}">
                            {{ $pair[1]->owner ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-600 text-center py-4">No duplicates found ✅</p>
    @endif
</div>
