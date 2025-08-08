<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-1 text-center">Rewards</h1>

    @if ($claims->isEmpty())
        <p class="text-center text-gray-600">No rewards to claims at the moment.</p>
    @else

        <p class="text-center mb-5">
            There {{ $claims->count() === 1 ? 'is' : 'are' }} currently {{ $claims->count() }} reward{{ $claims->count() === 1 ? '' : 's' }} running.
        </p>

        <div class="grid gap-6">
            {{-- Render one Livewire child component per claim (isolated state) --}}
            @foreach ($claims as $claim)
                @livewire('public-claim', ['claim' => $claim], key('claim-'.$claim->id))
            @endforeach
        </div>
    @endif
</div>
