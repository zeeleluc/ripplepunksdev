<div>
    @forelse($groups as $group)
        <div class="mb-8 border rounded p-4 bg-gray-50 shadow w-full">
            {{-- NFTs Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 justify-items-center">
                @foreach($group['nfts'] as $nft)
                    @php
                        $metadata = $nft->metadata ?? [];
                        $imageUrl = $nft->getImageUrl();
                    @endphp
                    <div class="border rounded p-2 bg-white shadow-sm w-full max-w-[150px] flex flex-col items-center text-center">
                        <img src="{{ $imageUrl }}"
                             alt="{{ $metadata['name'] ?? 'NFT Image' }}"
                             class="w-full h-28 object-cover rounded mb-2" />
                        <h3 class="font-semibold text-gray-700 text-xs truncate mb-2">
                            {{ $metadata['name'] ?? 'Unnamed NFT' }}
                        </h3>
                        <ul class="text-[11px] text-gray-600 text-left w-full space-y-0.5">
                            <li><strong>Type:</strong> {{ $nft->type }}</li>
                            <li><strong>Color:</strong> {{ $nft->color }}</li>
                            <li><strong>Skin:</strong> {{ $nft->skin }}</li>
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-gray-600">No duplicates found ✅</p>
    @endforelse

    {{-- Pagination --}}
    @include('components.custom-pagination', ['paginator' => $dupCombos])
</div>
