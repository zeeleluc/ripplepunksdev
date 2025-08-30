<div wire:poll.20s.keep-alive class="py-3">

    <style>
        @keyframes flash-yellow {
            0% { background-color: #fef08a; }
            100% { background-color: transparent; }
        }
        .flash-highlight {
            animation: flash-yellow 1s ease forwards;
        }
    </style>

    {{-- 24h Dashboard Bar --}}
    <div class="flex flex-wrap justify-center gap-4 mb-4">
        <div class="flex flex-col items-center bg-white shadow rounded-lg p-3 w-36">
            <span class="text-gray-500 text-sm text-center">Total XRP (24h)</span>
            <span class="text-lg font-bold text-center">{{ number_format($totalXrp, 2) }} XRP</span>
        </div>
        <div class="flex flex-col items-center bg-white shadow rounded-lg p-3 w-36">
            <span class="text-gray-500 text-sm text-center">Total USD (24h)</span>
            <span class="text-lg font-bold text-center">${{ number_format($totalUsd, 2) }}</span>
        </div>
        @foreach($marketplaceCounts as $market => $count)
            <div class="flex flex-col items-center bg-white shadow rounded-lg p-3 w-36">
                <span class="text-gray-500 text-sm text-center">{{ $market ?: 'Unknown' }}</span>
                <span class="text-lg font-bold text-center">{{ $count }}</span>
            </div>
        @endforeach
    </div>

    {{-- Sales container --}}
    <div class="rounded-lg md:border md:overflow-hidden md:divide-y md:bg-white md:shadow">
        {{-- Desktop scrollable table --}}
        <div class="hidden md:block overflow-x-auto">
            <div class="min-w-[800px]">
                @foreach($sales as $sale)
                    @php
                        $amountUsd = is_string($sale->amount_in_convert_currencies)
                            ? json_decode($sale->amount_in_convert_currencies, true)['usd'] ?? null
                            : $sale->amount_in_convert_currencies['usd'] ?? null;
                    @endphp

                    <div
                        wire:key="sale-{{ $sale->accepted_tx_hash }}-desktop"
                        class="border-b grid grid-cols-7 gap-2 p-2 items-center transition-colors duration-500 {{ in_array($sale->accepted_tx_hash, $highlighted) ? 'flash-highlight' : '' }}"
                    >
                        <div class="font-medium text-sm text-center">{{ $sale->nft_name ?? '-' }}</div>
                        <div class="text-end text-sm">{{ number_format($sale->amount / 1_000_000, 2) }} XRP</div>
                        <div class="text-end text-sm">${{ $amountUsd ? number_format($amountUsd, 2) : '-' }}</div>
                        <div class="text-center truncate text-sm">{{ substr($sale->seller,0,6) . '...' . substr($sale->seller,-4) }}</div>
                        <div class="text-center truncate text-sm">{{ substr($sale->buyer,0,6) . '...' . substr($sale->buyer,-4) }}</div>
                        <div class="text-sm text-center">
                            @if ($marketNftLink = $sale->getMarketNftLink())
                                <a target="_blank" class="underline text-primary-600 hover:text-primary-800" href="{{ $marketNftLink }}">
                                    {{ $sale->marketplace }}
                                </a>
                            @else
                                {{ $sale->marketplace ?: 'Unknown' }}
                            @endif
                        </div>
                        <div class="min-w-[200px] text-sm text-end">{{ $sale->accepted_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Mobile stacked cards --}}
        <div class="md:hidden flex flex-col items-center gap-2 py-2">
            @foreach($sales as $sale)
                @php
                    $amountUsd = is_string($sale->amount_in_convert_currencies)
                        ? json_decode($sale->amount_in_convert_currencies, true)['usd'] ?? null
                        : $sale->amount_in_convert_currencies['usd'] ?? null;
                @endphp

                <div
                    wire:key="sale-{{ $sale->accepted_tx_hash }}-mobile"
                    class="w-full max-w-xs py-2 px-3 border rounded-lg bg-white transition-colors duration-500 {{ in_array($sale->accepted_tx_hash, $highlighted) ? 'flash-highlight' : '' }}"
                >
                    <div class="font-bold text-lg text-center">{{ $sale->nft_name ?? '-' }}</div>
                    <div class="text-base text-center">XRP {{ number_format($sale->amount / 1_000_000, 2) }} / US$ {{ $amountUsd ? number_format($amountUsd, 2) : '-' }}</div>
                    <div class="text-sm text-center">
                        {{ substr($sale->seller, 0, 9) . '...' . substr($sale->seller, -4) }}
                        🫱🏼‍🫲🏿
                        {{ substr($sale->buyer, 0, 9) . '...' . substr($sale->buyer, -4) }}
                    </div>
                    <div class="text-sm text-center">
                        {{ $sale->accepted_at->diffForHumans() }} via
                        @if ($marketNftLink = $sale->getMarketNftLink())
                            <a target="_blank" class="underline text-primary-600 hover:text-primary-800" href="{{ $marketNftLink }}">
                                {{ $sale->marketplace }}
                            </a>
                        @else
                            {{ $sale->marketplace ?: 'Unknown' }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3 p-2 flex justify-center">
        @include('components.custom-pagination', ['paginator' => $sales])
    </div>

    <div class="flex justify-center mt-4">
        <livewire:buy-button />
    </div>
</div>
