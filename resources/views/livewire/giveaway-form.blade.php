<div class="my-3 border rounded pt-2 pb-1 px-3 bg-gray-100">
    @if ($submitted || session('giveaway_submitted_' . $type))

    @else
        @if (session()->has('message'))
            <div class="text-green-600 mb-2">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="submit" class="mb-4">
            <input type="hidden" wire:model="type">

            <div class="my-2">
                <input placeholder="XRP Wallet Address" type="text" wire:model="wallet" class="w-full border p-2 rounded">
                @error('wallet') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-2 rounded">
                Submit XRP Wallet
            </button>
        </form>
    @endif
</div>
