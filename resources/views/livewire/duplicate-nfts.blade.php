<div class="overflow-x-auto">
    @if ($group['nfts']->isNotEmpty())
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($group['nfts']->chunk(2) as $pair)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        @php
                            $first = $pair->first();
                            $metadata = is_array($first->metadata) ? $first->metadata : [];
                        @endphp
                        {{ $metadata['name'] ?? 'Unnamed NFT' }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ $pair[0]->nft_id ?? '-' }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ $pair[0]->owner ?? '-' }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if (isset($pair[1]))
                            {{ $pair[1]->nft_id }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        @if (isset($pair[1]))
                            {{ $pair[1]->owner }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-600 text-center py-4">No duplicates found ✅</p>
    @endif
</div>
