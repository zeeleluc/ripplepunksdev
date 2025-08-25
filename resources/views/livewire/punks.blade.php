<div>
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold text-center">RipplePunks</h1>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-4 justify-center mb-6">
        <select wire:model.live="color" class="border rounded px-3 py-2">
            <option value="">All Colors</option>
            @foreach ($colors as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>

        <select wire:model.live="type" class="border rounded px-3 py-2">
            <option value="">All Types</option>
            @foreach ($types as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        <select wire:model.live="totalAccessories" class="border rounded px-3 py-2">
            <option value="">All Accessories Count</option>
            @foreach ($totals as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        <select wire:model.live="accessory" class="border rounded px-3 py-2">
            <option value="">All Accessories</option>
            @foreach ($accessories as $a)
                <option value="{{ $a }}">{{ ucfirst(str_replace('_', ' ', $a)) }}</option>
            @endforeach
        </select>
    </div>

    @include('components.custom-pagination', ['paginator' => $nfts])

    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-6">
        @foreach ($nfts as $nft)
            @php
                $metadata = $nft->metadata ?? [];
                $imageUrl = $this->getImageUrl($nft);
            @endphp
            <div class="border rounded p-4 bg-white shadow">
                <img src="{{ $imageUrl }}"
                     alt="{{ $metadata['name'] ?? 'NFT Image' }}"
                     class="w-full object-cover rounded mb-2" />
                <h3 class="text-center font-semibold text-gray-700">
                    {{ $metadata['name'] ?? 'Unnamed NFT' }}
                </h3>
            </div>
        @endforeach
    </div>

    @include('components.custom-pagination', ['paginator' => $nfts])
</div>
