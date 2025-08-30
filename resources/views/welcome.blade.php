@extends('layouts.app')

@section('content')
    @php
        $initialSupply = 10000;
        $eventualTotal = 20000;
        $outOfCirculation = \App\Models\Nft::ctoWalletCount();
        $newBatchMinted = \App\Models\Nft::count() - 10000;

        $totalSupply = $initialSupply + $newBatchMinted;
        $actualCirculating = $totalSupply - $outOfCirculation;
    @endphp

    <div class="container mx-auto px-2 sm:px-6 max-w-7xl">

        {{-- NFT Preview & Dev Log --}}
        <div class="flex flex-col md:flex-row md:gap-6 mt-6 sm:mt-10">

            {{-- NFT Preview --}}
            <div class="w-full md:w-1/2 border bg-white p-4 sm:p-6 shadow text-center">
                <img src="{{ asset('images/project-nft.png') }}" class="mx-auto w-full rounded" alt="Project NFT">
            </div>

            {{-- Quick Links --}}
            <div class="w-full md:w-1/2 p-0 sm:p-1 mt-6 md:mt-0 overflow-y-auto max-h-[600px]">
                <livewire:buy-button />
                <a href="/punks"
                   class="w-full bg-primary-800 hover:bg-gray-800 mb-2 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded shadow text-center block">
                    All Punks
                </a>
                <a href="/rewards"
                   class="w-full bg-primary-600 hover:bg-gray-800 mb-2 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded shadow text-center block">
                    Rewards
                </a>
                <a href="/shoutboard"
                   class="w-full sm:w-auto bg-primary-600 hover:bg-gray-800 mb-2 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded shadow text-center block">
                    Shoutboard
                </a>
                <a href="/badges"
                   class="w-full sm:w-auto bg-primary-600 hover:bg-gray-800 mb-2 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded shadow text-center block">
                    Badges
                </a>
                <a href="/holders"
                   class="w-full sm:w-auto bg-primary-600 hover:bg-gray-800 mb-2 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded shadow text-center block">
                    Holders
                </a>
            </div>

            {{-- Dev Log --}}
            <div class="w-full md:w-1/2 border bg-white p-4 sm:p-6 shadow mt-6 md:mt-0 overflow-y-auto max-h-[600px]">
                <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 pl-1">The Dev ✍️</h2>
                @foreach ($logEntries as $logEntry)
                    <div class="border rounded-xl pt-2 p-3 sm:p-4 mb-4">
                        <p class="text-sm sm:text-base mb-0">
                            @if ($logEntry->link)
                                <a target="_blank" href="{{ $logEntry->link }}" class="text-blue-600 underline">🔗</a>
                            @endif
                            {{ $logEntry->text }}
                        </p>
                        <div class="text-xs text-gray-500">{{ $logEntry->created_at->diffForHumans() }}</div>
                        <livewire:interaction-buttons :identifier="'log-' . $logEntry->id" class="mt-2 sm:mt-4" />
                    </div>
                @endforeach
                <a class="text-primary-600 underline" href="{{ route('logs') }}">Read more dev logs..</a>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="bg-white border rounded shadow p-3 sm:p-4 mb-5 mt-5">
            <div class="mb-1 sm:mb-2 font-semibold text-base sm:text-lg">Punks Minting Progress</div>
            <div class="w-full bg-gray-300 rounded h-4 sm:h-6 overflow-hidden flex">
                <div class="h-full bg-primary-300" style="width: {{ $bar1Percent }}%;"></div>
                <div class="h-full bg-primary-500" style="width: {{ $bar2Percent }}%;"></div>
                <div class="h-full bg-gray-100" style="width: {{ $bar3Percent }}%;"></div>
            </div>
            <div class="flex justify-between mt-1 sm:mt-2 text-xs sm:text-sm font-semibold">
                <span>The Original Punks</span>
                <span>The Other Punks</span>
            </div>
        </div>

        {{-- Supply Breakdown --}}
        <div class="flex flex-col md:flex-row md:gap-6 mt-2">
            <div class="w-full border bg-white p-3 sm:p-6 shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left border text-xs sm:text-sm">
                        <thead>
                        <tr class="bg-gray-200">
                            <th class="px-2 sm:px-4 py-1 sm:py-2">Metric</th>
                            <th class="px-2 sm:px-4 py-1 sm:py-2">Value</th>
                            <th class="px-2 sm:px-4 py-1 sm:py-2">Description</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <tr class="bg-gray-50">
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">Initial Supply<br><small>The Original Punks</small></td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($initialSupply) }}</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">First minted batch of NFTs</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">New Minted Supply<br><small>The Other Punks</small></td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($newBatchMinted) }} / 10,000</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">Second batch being minted gradually</td>
                        </tr>
                        <tr>
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">Total Supply (Current)</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($totalSupply) }}</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">Initial plus Newly minted NFTs</td>
                        </tr>
                        <tr>
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">Eventual Total Supply</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($eventualTotal) }}</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">Max total supply after all minting completes</td>
                        </tr>
                        <tr>
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">Out of Circulation</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($outOfCirculation) }}</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">Locked in a multi-sig or custodial wallet</td>
                        </tr>
                        <tr class="bg-yellow-100 font-bold text-sm sm:text-lg">
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">Actual Circulating</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($actualCirculating) }}</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">Current Total minus Out of Circulation</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 sm:mt-4 flex justify-start">
                    <livewire:interaction-buttons identifier="the-supply-breakdown" />
                </div>
            </div>
        </div>

        {{-- NFT Counts Table --}}
        <div class="flex flex-col md:flex-row md:gap-6 mt-6">
            <div class="w-full border bg-white p-3 sm:p-6 shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left border text-xs sm:text-sm">
                        <thead>
                        <tr class="bg-gray-200">
                            <th class="px-2 sm:px-4 py-1 sm:py-2"></th>
                            <th class="px-2 sm:px-4 py-1 sm:py-2">OGs</th>
                            <th class="px-2 sm:px-4 py-1 sm:py-2">Others</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        @foreach ($counts as $type => $batches)
                            <tr class="bg-gray-50">
                                <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">{{ ucfirst($type) }}</td>
                                <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($batches[0]) }}</td>
                                <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($batches[1]) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
