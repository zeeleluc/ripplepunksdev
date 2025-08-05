<div class="p-4 border rounded bg-white shadow-lg max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold mb-3">Giveaway</h1>

    <h2 class="text-base font-bold my-2">
        {{ $type }}
    </h2>

    @if ($type === 'Celebrate New Punks')
        <p class="mb-4">
            We're celebrating the launch of <strong>The Other Punks</strong> — the next 10k evolution of the Punks legacy — with a giveaway: if you hold at least <strong>1 OG Punk (#0–#9999)</strong> and <strong>10 of The Other Punks (#10000–#19999)</strong>, you're eligible to receive <strong>2 brand-new Punks for free</strong> as a reward for supporting the movement early.
        </p>
    @endif

    @unless(session('giveaway_submitted_' . $type))
        @livewire('giveaway-form', ['type' => $type])
    @endunless

    @livewire('giveaway-index', ['type' => $type])
</div>
