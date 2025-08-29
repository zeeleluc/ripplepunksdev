<div wire:poll.20s.keep-alive class="p-4">
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
    <div class="flex flex-wrap justify-start gap-4 mb-4">
        <div class="flex flex-col items-center bg-white shadow rounded-lg p-3 w-36">
            <span class="text-gray-500 text-sm">Total XRP (24h)</span>
            <span class="text-lg font-bold">{{ number_format($totalXrp, 2) }} XRP</span>
        </div>
        <div class="flex flex-col items-center bg-white shadow rounded-lg p-3 w-36">
            <span class="text-gray-500 text-sm">Total USD (24h)</span>
            <span class="text-lg font-bold">${{ number_format($totalUsd, 2) }}</span>
        </div>
        @foreach($marketplaceCounts as $market => $count)
            <div class="flex flex-col items-center bg-white shadow rounded-lg p-3 w-36">
                <span class="text-gray-500 text-sm">{{ $market }}</span>
                <span class="text-lg font-bold">{{ $count }}</span>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow border overflow-hidden divide-y">
        {{-- Desktop scrollable container --}}
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
                        <div class="font-medium text-sm">{{ $sale->nft_name ?? '-' }}</div>
                        <div class="text-end text-sm">{{ number_format($sale->amount / 1_000_000, 2) }} XRP</div>
                        <div class="text-end text-sm">${{ $amountUsd ? number_format($amountUsd, 2) : '-' }}</div>
                        <div class="text-center truncate text-sm">{{ substr($sale->buyer,0,6) . '...' . substr($sale->buyer,-4) }}</div>
                        <div class="text-center truncate text-sm">{{ substr($sale->seller,0,6) . '...' . substr($sale->seller,-4) }}</div>
                        <div class="text-sm">{{ $sale->marketplace }}</div>
                        <div class="min-w-[200px] text-sm text-end">{{ $sale->accepted_at->format('Y-m-d H:i:s') }} UTC</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Mobile stacked card --}}
        <div class="md:hidden">
            @foreach($sales as $sale)
                @php
                    $amountUsd = is_string($sale->amount_in_convert_currencies)
                        ? json_decode($sale->amount_in_convert_currencies, true)['usd'] ?? null
                        : $sale->amount_in_convert_currencies['usd'] ?? null;
                @endphp

                <div
                    wire:key="sale-{{ $sale->accepted_tx_hash }}-mobile"
                    class="p-3 transition-colors duration-500 border-b {{ in_array($sale->accepted_tx_hash, $highlighted) ? 'flash-highlight' : '' }}"
                >
                    <div class="font-medium text-lg">{{ $sale->nft_name ?? '-' }}</div>
                    <div class="text-base">XRP {{ number_format($sale->amount / 1_000_000, 2) }} / US$ {{ $amountUsd ? number_format($amountUsd, 2) : '-' }}</div>
                    <div class="text-xs">
                        {{ substr($sale->buyer, 0, 6) . '...' . substr($sale->buyer, -4) }}
                        >
                        {{ substr($sale->seller, 0, 6) . '...' . substr($sale->seller, -4) }}
                    </div>
                    <div class="text-xs">{{ $sale->marketplace }} @ {{ $sale->accepted_at->format('Y-m-d H:i:s') }} UTC</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3 p-2 border-t bg-gray-50">
        @include('components.custom-pagination', ['paginator' => $sales])
    </div>
</div>
