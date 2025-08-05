<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Giveaways</h1>

    <div class="flex flex-wrap gap-6">
        <!-- Livewire Form Card -->
        <div class="w-full md:w-[100%] bg-white border rounded-xl p-6">

            <h1 class="text-xl font-bold my-2">
                {{ $type }}
            </h1>

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
    </div>
</div>



