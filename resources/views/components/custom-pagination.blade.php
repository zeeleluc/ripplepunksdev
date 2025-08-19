<div class="mt-6 flex items-center justify-center space-x-1 text-sm py-5">
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $range = 2; // pages around current
        $pagesToShow = [];

        // Log for debugging
        \Log::info('Pagination Debug', [
            'current' => $current,
            'last' => $last,
            'requested_page' => request()->query('page', 1)
        ]);

        // If total pages are small, show all
        if ($last <= 5) {
            $pagesToShow = range(1, $last);
        } else {
            // Always show first page
            $pagesToShow[] = 1;

            // Pages before current
            for ($i = $current - $range; $i < $current; $i++) {
                if ($i > 1) $pagesToShow[] = $i;
            }

            // Current page
            $pagesToShow[] = $current;

            // Pages after current
            for ($i = $current + 1; $i <= $current + $range; $i++) {
                if ($i < $last) $pagesToShow[] = $i;
            }

            // Always show last page
            $pagesToShow[] = $last;

            // Remove duplicates and sort
            $pagesToShow = array_unique($pagesToShow);
            sort($pagesToShow);
        }

        $previous = 0;
    @endphp

    {{-- Previous button --}}
    @if($current > 1)
        <button wire:click="gotoPage({{ $current - 1 }})" class="px-3 py-1 border rounded bg-white">
            &larr;
        </button>
    @endif

    {{-- Render pages with "..." --}}
    @foreach($pagesToShow as $page)
        @if ($previous && $page > $previous + 1)
            <span class="px-2">...</span>
        @endif

        <button
            wire:click="gotoPage({{ $page }})"
            class="px-3 py-1 border rounded {{ $page === $current ? 'bg-primary-600 text-white font-bold' : 'bg-white' }}">
            {{ $page }}
        </button>

        @php $previous = $page; @endphp
    @endforeach

    {{-- Next button --}}
    @if($current < $last)
        <button wire:click="gotoPage({{ $current + 1 }})" class="px-3 py-1 border rounded bg-white">
            &rarr;
        </button>
    @endif
</div>
