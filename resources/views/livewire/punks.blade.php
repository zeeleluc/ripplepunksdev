<div class="flex flex-col items-center justify-center px-2 sm:px-4 md:px-6 lg:px-0" x-data="{ modalOpen: @entangle('showAccessoryModal') }">
    {{-- Page Header --}}
    <div class="max-w-6xl w-full py-4 sm:py-6 text-center">
        <h1 class="text-2xl sm:text-3xl font-bold">RipplePunks</h1>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap justify-center gap-2 sm:gap-4 mb-4 sm:mb-6">
        <select wire:model.live="color" class="border rounded px-2 py-1 sm:px-3 sm:py-2 text-sm sm:text-base text-center">
            <option value="">All Colors</option>
            @foreach ($colors as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>

        <select wire:model.live="type" class="border rounded px-2 py-1 sm:px-3 sm:py-2 text-sm sm:text-base text-center">
            <option value="">All Types</option>
            @foreach ($types as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        <select wire:model.live="totalAccessories" class="border rounded px-2 py-1 sm:px-3 sm:py-2 text-sm sm:text-base text-center">
            <option value="">All Accessories Count</option>
            @foreach ($totals as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        {{-- Open Accessories Modal --}}
        <button @click="modalOpen = true; $wire.openAccessoryModal()" class="border rounded px-2 py-1 sm:px-3 sm:py-2 bg-gray-200 hover:bg-gray-300 text-sm sm:text-base flex items-center justify-center space-x-2">
            <span>Select Accessories</span>
            @if (count($selectedAccessories) > 0)
                <span class="ml-1 text-blue-600 font-semibold">({{ count($selectedAccessories) }})</span>
            @endif
            <svg wire:loading wire:target="openAccessoryModal" class="animate-spin h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </button>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading.flex wire:target="color,type,totalAccessories,selectedAccessories,applyFilters,resetFilters" class="text-center my-4 sm:my-8">
        <span class="inline-block px-3 py-1 sm:px-4 sm:py-2 bg-blue-100 text-blue-700 rounded animate-pulse text-sm sm:text-lg">
            Loading RipplePunks...
        </span>
    </div>

    {{-- NFT Grid --}}
    <div wire:loading.remove wire:target="color,type,totalAccessories,selectedAccessories,applyFilters,resetFilters" class="w-full max-w-6xl">
        @if($nfts->count() === 0)
            <div class="text-center py-8 sm:py-16 text-gray-500 space-y-1 sm:space-y-2">
                <p class="text-lg sm:text-xl font-semibold">No RipplePunks found 😢</p>
                <p class="text-sm sm:text-base">Try changing the filters or selecting different accessories.</p>
            </div>
        @else
            @include('components.custom-pagination', ['paginator' => $nfts])
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-6 justify-items-center">
                @foreach ($nfts as $nft)
                    @php
                        $metadata = $nft->metadata ?? [];
                        $imageUrl = $this->getImageUrl($nft);
                    @endphp
                    <a href="{{ route('punks.show', ['id' => $nft->nft_id]) }}" class="border rounded p-2 sm:p-4 bg-white shadow w-full max-w-[180px] sm:max-w-full flex flex-col items-center">
                        <img src="{{ $imageUrl }}" alt="{{ $metadata['name'] ?? 'NFT Image' }}" class="w-full h-36 sm:h-48 object-cover rounded mb-1 sm:mb-2" />
                        <h3 class="text-center font-semibold text-gray-700 text-sm sm:text-base">
                            {{ $metadata['name'] ?? 'Unnamed NFT' }}
                        </h3>
                    </a>
                @endforeach
            </div>
            @include('components.custom-pagination', ['paginator' => $nfts])
        @endif
    </div>

    {{-- Accessory Modal --}}
    <div x-show="modalOpen" x-transition class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 px-2">
        <div class="bg-white rounded shadow-lg w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden">
            {{-- Modal Header --}}
            <h3 class="text-lg sm:text-xl font-semibold text-center py-3 border-b">
                Select Accessories
            </h3>

            {{-- Modal Content --}}
            <div class="flex-1 overflow-y-auto px-2 sm:px-4 py-2 sm:py-3">
                <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-1 sm:gap-2 justify-items-center">
                    @foreach($accessories as $key => $label)
                        <label class="inline-block w-full text-center">
                            <input type="checkbox" wire:model.defer="tempSelectedAccessories" value="{{ $key }}" class="hidden peer">
                            <span class="block px-2 py-1 sm:px-3 sm:py-2 rounded text-xs sm:text-sm cursor-pointer bg-gray-200 text-gray-700 peer-checked:bg-blue-600 peer-checked:text-white hover:bg-blue-500 hover:text-white">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex justify-center space-x-2 px-2 sm:px-4 py-2 border-t bg-white">
                <button type="button" wire:click="closeAccessoryModal" class="px-3 py-1 sm:px-4 sm:py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm sm:text-base flex items-center justify-center space-x-2">
                    <span>Cancel</span>
                    <svg wire:loading wire:target="closeAccessoryModal" class="animate-spin h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
                <button type="button" wire:click="resetFilters" class="px-3 py-1 sm:px-4 sm:py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm sm:text-base flex items-center justify-center space-x-2">
                    <span>Reset</span>
                    <svg wire:loading wire:target="resetFilters" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
                <button type="button" wire:click="applyFilters" class="px-3 py-1 sm:px-4 sm:py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm sm:text-base flex items-center justify-center space-x-2">
                    <span>Apply</span>
                    <svg wire:loading wire:target="applyFilters" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
