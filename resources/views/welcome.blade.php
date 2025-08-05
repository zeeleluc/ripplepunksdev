@extends('layouts.app')

@section('content')
    @php
        $bar1Percent = ($bar1Count / $totalItems) * 100;
        $bar2Percent = ($bar2Count / $totalItems) * 100;

        $initialSupply = 10000;
        $eventualTotal = 20000;
        $outOfCirculation = 1375;
        $newBatchMinted = 210;

        $totalSupply = $initialSupply + $newBatchMinted;
        $actualCirculating = $totalSupply - $outOfCirculation;
    @endphp

    <div class="max-w-8xl mx-auto p-6 mb-4 my-4">
        <div class="flex flex-wrap justify-center gap-6">
            <div class="w-full md:w-[28%] text-center border bg-white p-6">
                <img
                    src="{{ asset('images/project-nft.png') }}"
                    class="mx-auto w-full"
                    alt="Project NFT"
                />
            </div>

            <div class="w-full md:w-[68%] text-center border bg-white p-6">
                <table class="min-w-full table-auto text-left border">
                    <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Metric</th>
                        <th class="px-4 py-2">Value</th>
                        <th class="px-4 py-2">Description</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    <tr class="bg-gray-50">
                        <td class="px-4 py-2 font-medium">
                            Initial Supply<br />
                            <small>The Original Punks</small>
                        </td>
                        <td class="px-4 py-2">{{ number_format($initialSupply) }}</td>
                        <td class="px-4 py-2">First minted batch of NFTs</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-2 font-medium">
                            New Minted Supply
                            <br />
                            <small>The Other Punks</small>
                        </td>
                        <td class="px-4 py-2">{{ number_format($newBatchMinted) }} / 10,000</td>
                        <td class="px-4 py-2">Second batch being minted gradually</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">Total Supply (Current)</td>
                        <td class="px-4 py-2">{{ number_format($totalSupply) }}</td>
                        <td class="px-4 py-2">Initial plus Newly minted NFTs</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">Eventual Total Supply</td>
                        <td class="px-4 py-2">{{ number_format($eventualTotal) }}</td>
                        <td class="px-4 py-2">Max total supply after all minting completes</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">
                            Out of Circulation
                            <a target="_blank" href="https://bithomp.com/en/nft-explorer?includeWithoutMediaData=true&owner=VaultForRP&issuer=r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS&order=rating&taxon=604&includeBurned=true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                                </svg>
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ number_format($outOfCirculation) }}</td>
                        <td class="px-4 py-2">Locked in a multi-sig or custodial wallet</td>
                    </tr>
                    <tr class="bg-yellow-100 font-bold text-lg">
                        <td class="px-4 py-2 font-medium">Actual Circulating</td>
                        <td class="px-4 py-2">{{ number_format($actualCirculating) }}</td>
                        <td class="px-4 py-2">Current Total minus Out of Circulation</td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-right mt-5 text-sm text-gray-400">
                    Updated 2025-08-05
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto space-y-8 p-6 border bg-white mt-4 mb-8">

        {{-- Bar 1 --}}
        <div>
            <div class="mb-0 font-semibold text-lg">The Original Punks (#0 - #9999)</div>
            <p class="mb-2">
                These are the original 1/1 Punks, perfectly matching the iconic Ethereum Punks collection.
            </p>
            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden">
                <div
                    class="h-full bg-primary-300"
                    style="width: {{ $bar1Percent }}%;"
                ></div>
            </div>
        </div>

        {{-- Bar 2 --}}
        <div>
            <div class="mb-0 font-semibold text-lg">The Other Punks (#10000 - #19999)</div>
            <p class="mb-2">
                Since the CTO plans to stall their original 10k OG Punks, we’re keeping the collection alive by minting 10k brand-new Punks.
                Each of these features unique trait combinations.
            </p>
            <div class="w-full bg-gray-200 rounded h-6 overflow-hidden">
                <div
                    class="h-full bg-primary-500"
                    style="width: {{ $bar2Percent }}%;"
                ></div>
            </div>
        </div>

    </div>

    @livewire('giveaway-wrapper')

    <div class="flex justify-center mt-8">
        <a href="{{ url('/about-cto') }}"
           class="inline-block bg-primary-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
            About the CTO
        </a>
    </div>

@endsection
