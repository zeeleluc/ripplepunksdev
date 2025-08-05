<div>
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @foreach ($nfts as $nft)
            @php
                $metadata = $nft->metadata ?? [];
                $imageUrl = $metadata['image'] ?? null;
            @endphp
            <div class="border rounded-lg p-4 bg-white shadow">
                @if ($imageUrl)
                    <img
                        src="{{ $this->ipfsToHttp($imageUrl) }}"
                        alt="{{ $metadata['name'] ?? 'NFT Image' }}"
                        class="w-full object-cover rounded-md mb-2"
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

    <!-- Custom Pagination -->
    <div class="mt-6 flex items-center justify-center space-x-1 text-sm">

        @php
            $current = $nfts->currentPage();
            $last = $nfts->lastPage();
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

</div>
