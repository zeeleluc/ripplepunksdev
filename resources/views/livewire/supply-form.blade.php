<div>
    <h2 class="text-xl font-semibold mb-4">Add Supply Record</h2>

    <form wire:submit.prevent="submit">
        <div class="mb-4">
            <label for="out_of_circulation" class="block text-sm font-medium">Out of Circulation</label>
            <input type="number" wire:model="out_of_circulation" id="out_of_circulation"
                   class="mt-1 block w-full border rounded px-3 py-2" />
            @error('out_of_circulation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="new_mints" class="block text-sm font-medium">New Mints</label>
            <input type="number" wire:model="new_mints" id="new_mints"
                   class="mt-1 block w-full border rounded px-3 py-2" />
            @error('new_mints') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save
        </button>
    </form>
</div>
