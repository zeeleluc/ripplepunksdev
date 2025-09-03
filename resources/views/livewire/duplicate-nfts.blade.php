@php
    $projectWallet   = env('PROJECT_WALLET');
    $rewardsWallet   = env('REWARDS_WALLET');
    $hackWallet      = 'rwbaCNkedtHacK8Qer3qdVZaH2fjSvBrJZ';

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
        <p class="mb-2 text-gray-700">Found {{ $nftsGrouped->count() }} duplicate groups</p>

        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($nftsGrouped as $group)
                @foreach($group->chunk(2) as $pair)
                    @php
                        $first  = $pair->get(0);
                        $second = $pair->get(1);
                    @endphp

                    <tr>
                        {{-- NFT 1 Name --}}
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ $first->metadata['name'] ?? 'Unnamed NFT' }}
                        </td>

                        {{-- NFT 1 ID --}}
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if(!empty($first->nftoken_id))
                                <a target="_blank"
                                   class="underline {{ ($first->nft_id >= 11320) ? 'font-bold text-red-600' : 'text-primary-600' }}"
                                   href="https://xrp.cafe/nft/{{ $first->nftoken_id }}">
                                    #{{ $first->nft_id ?? '-' }}
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        {{-- NFT 1 Owner --}}
                        <td class="px-4 py-2 text-sm {{ getWalletHighlightClass($first->owner ?? null, $hackWallet, $highlightWallets) }}">
                            {{ $first->owner ?? '-' }}
                        </td>

                        {{-- NFT 2 ID --}}
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if($second && !empty($second->nftoken_id))
                                <a target="_blank"
                                   class="underline {{ ($second->nft_id >= 11320) ? 'font-bold text-red-600' : 'text-primary-600' }}"
                                   href="https://xrp.cafe/nft/{{ $second->nftoken_id }}">
                                    #{{ $second->nft_id ?? '-' }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        
                        {{-- NFT 2 Owner --}}
                        <td class="px-4 py-2 text-sm {{ $second ? getWalletHighlightClass($second->owner ?? null, $hackWallet, $highlightWallets) : '' }}">
                            {{ $second->owner ?? '-' }}
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
