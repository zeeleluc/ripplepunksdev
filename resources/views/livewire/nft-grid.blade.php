<div>
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @foreach ($nfts as $nft)
            @php
                $imageUrl = $this->getImageUrl($nft);
                $metadata = $nft->metadata ?? [];
            @endphp
            <div class="border rounded p-4 bg-white shadow">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $metadata['name'] ?? 'NFT Image' }}"
                    class="w-full object-cover rounded mb-2"
                />
                <h3 class="text-center font-semibold text-gray-700">{{ $metadata['name'] ?? 'Unnamed NFT' }}</h3>
            </div>
        @endforeach
    </div>

    @include('components.custom-pagination', ['paginator' => $nfts])
</div>
