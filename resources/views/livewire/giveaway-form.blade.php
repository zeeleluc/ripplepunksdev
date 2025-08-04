<div>
    @if ($submitted || session('giveaway_submitted_' . $type))

    @else
        <h2 class="text-base font-bold my-2">
            {{ $type }}
        </h2>

        @if ($type === 'Celebrate New Punks')
            <p>
                We're celebrating the launch of <strong>The Other Punks</strong> — the next 10x evolution of the Punks legacy — with a giveaway: if you hold at least <strong>1 OG Punk (#0–#9999)</strong> and <strong>10 of The Other Punks (#10000–#19999)</strong>, you're eligible to receive <strong>2 brand new Punks for free</strong> as a reward for supporting the movement early.
            </p>
        @endif

        @if (session()->has('message'))
            <div class="text-green-600 mb-2">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="submit" class="mb-4">
            <input type="hidden" wire:model="type">

            <div class="my-2">
                <input placeholder="XRP Wallet Address" type="text" wire:model="wallet" class="w-full border p-2 rounded" disabled readonly>
                @error('wallet') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-2 rounded" disabled>
                Enabling soon
            </button>
        </form>
    @endif
</div>
