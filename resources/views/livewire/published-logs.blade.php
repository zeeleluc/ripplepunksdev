<div class="max-w-6xl mx-auto px-0 py-6">

    <h1 class="text-2xl font-bold mb-6 text-center">The Dev 🤘🏼</h1>

    <div class="overflow-x-auto  bg-white border rounded p-4">
        @foreach ($logs as $log)
            <div class="my-0 pt-2 pb-4 border-b">
                <p class="p-0 m-0">
                    @if ($log->link)
                        <a target="_blank" href="{{ $log->link }}">
                            🔗
                        </a>
                    @endif
                    {{ $log->text }}
                </p>
                <small class="text-xs">
                    {{ $log->created_at->format('Y-m-d H:i') }} <sup>UTC</sup>
                </small>
            </div>
        @endforeach

        <!-- Custom Pagination -->
        <div class="mt-6 flex items-center justify-center space-x-1 text-sm py-5">

            @php
                $current = $logs->currentPage();
                $last = $logs->lastPage();
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
</div>
