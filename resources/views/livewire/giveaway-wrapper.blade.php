<div class="p-4 border rounded bg-white shadow-lg max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold">Giveaway Soon!</h1>

    @unless(session('giveaway_submitted_' . $type))
        @livewire('giveaway-form', ['type' => $type])
    @endunless

    @livewire('giveaway-index', ['type' => $type])
</div>
