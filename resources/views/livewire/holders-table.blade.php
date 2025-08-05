<div class="max-w-4xl mx-auto bg-white border rounded">

    <div class="overflow-x-auto">
        <table class="w-full table-auto border border-gray-300">
            <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2 border">Owner</th>
                <th class="text-left px-4 py-2 border">RipplePunks</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($holders as $holder)
                <tr>
                    <td class="border px-4 py-2">{{ $holder->owner }}</td>
                    <td class="border px-4 py-2">{{ $holder->nft_count }}</td>
                </tr>
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

</div>
