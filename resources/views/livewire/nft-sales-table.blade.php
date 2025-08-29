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

    <table class="w-full text-sm border-collapse">
        <thead>
        <tr class="bg-gray-100 text-left">
            <th class="p-2">NFT</th>
            <th class="p-2">Buyer</th>
            <th class="p-2">Seller</th>
            <th class="p-2 text-end">Amount (XRP)</th>
            <th class="p-2 text-end">Amount (US$)</th>
            <th class="p-2">Marketplace</th>
            <th class="p-2">Accepted At</th>
        </tr>
        </thead>
        <tbody>
        @foreach($sales as $sale)
            <tr
                wire:key="sale-{{ $sale->accepted_tx_hash }}"
                class="border-b {{ in_array($sale->accepted_tx_hash, $highlighted) ? 'flash-highlight' : '' }}"
            >
                <td class="p-2">{{ $sale->nft_name ?? '-' }}</td>
                <td class="p-2 truncate max-w-[120px]">{{ $sale->buyer }}</td>
                <td class="p-2 truncate max-w-[120px]">{{ $sale->seller }}</td>
                <td class="p-2 text-end">{{ number_format($sale->amount / 1_000_000, 2) }} XRP</td>
                <td class="p-2 text-end">
                    @php
                        $amountUsd = is_string($sale->amount_in_convert_currencies)
                            ? json_decode($sale->amount_in_convert_currencies, true)['usd'] ?? null
                            : $sale->amount_in_convert_currencies['usd'] ?? null;
                    @endphp
                    ${{ $amountUsd ? number_format($amountUsd, 2) : '-' }}
                </td>
                <td class="p-2">{{ $sale->marketplace }}</td>
                <td class="p-2">{{ $sale->accepted_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        @include('components.custom-pagination', ['paginator' => $sales])
    </div>
</div>
