<div>
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @foreach ($nfts as $nft)
            @php
                $metadata = $nft->metadata ?? [];
                $imageUrl = $metadata['image'] ?? null;
            @endphp
            <div class="border rounded p-4 bg-white shadow">
                @if ($imageUrl)
                    <img
                        src="{{ $this->ipfsToHttp($imageUrl) }}"
                        alt="{{ $metadata['name'] ?? 'NFT Image' }}"
                        class="w-full object-cover rounded mb-2"
                    />
                @else
                    <div class="w-full bg-gray-200 flex items-center justify-center rounded-md mb-2">
                        No Image
                    </div>
                @endif
                <h3 class="text-center font-semibold text-gray-700">{{ $metadata['name'] ?? 'Unnamed NFT' }}</h3>
            </div>
        @endforeach
    </div>

    @include('components.custom-pagination', ['paginator' => $nfts])

</div>
