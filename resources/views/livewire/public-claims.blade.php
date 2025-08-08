<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Rewards</h1>

    @if ($claims->isEmpty())
        <p class="text-center text-gray-600">No rewards to claims at the moment.</p>
    @else
        <div class="grid gap-6">
            {{-- Render one Livewire child component per claim (isolated state) --}}
            @foreach ($claims as $claim)
                @livewire('public-claim', ['claim' => $claim], key('claim-'.$claim->id))
            @endforeach
        </div>
    @endif
</div>
