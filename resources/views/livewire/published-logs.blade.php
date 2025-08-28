<div class="max-w-6xl mx-auto px-0 py-6">

    <h1 class="text-2xl font-bold mb-6 text-center">The Dev ✍️</h1>

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

                <div>
                    <livewire:interaction-buttons
                        :identifier="'log-' . $log->id"
                        wire:key="log-{{ $log->id }}"
                        class="my-2"
                    />
                </div>

                <small class="text-xs">{{ $log->created_at->diffForHumans() }}</small>
            </div>
        @endforeach

    </div>

    @include('components.custom-pagination', ['paginator' => $logs])

</div>
