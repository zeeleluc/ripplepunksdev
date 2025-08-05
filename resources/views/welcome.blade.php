@extends('layouts.app')

@section('content')
    @php
        $bar1Percent = ($bar1Count / $totalItems) * 100;
        $bar2Percent = ($bar2Count / $totalItems) * 100;

        $initialSupply = 10000;
        $eventualTotal = 20000;
        $outOfCirculation = \App\Models\Nft::ctoWalletCount();
        $newBatchMinted = \App\Models\Nft::count() - 10000;

        $totalSupply = $initialSupply + $newBatchMinted;
        $actualCirculating = $totalSupply - $outOfCirculation;
    @endphp
    <div class="container mx-auto px-0 sm:px-6 py-6">

        <div class="px-0 sm:px-6 pb-3 pt-0">
            <div class="flex flex-col sm:flex-row sm:justify-center sm:items-center gap-3 sm:gap-4 mb-8">
                <a
                    target="_blank"
                    href="https://xrp.cafe/usercollection/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/604"
                    class="w-full sm:w-auto bg-primary-500 hover:bg-primary-600 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    The Other Punks: 2 XRP
                </a>
                <a
                    href="/giveaway"
                    class="w-full sm:w-auto bg-gray-800 hover:bg-gray-900 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    Giveaways
                </a>
                <a
                    href="/about-cto"
                    class="w-full sm:w-auto bg-gray-300 hover:bg-gray-300 text-gray-800 text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    About the CTO
                </a>
            </div>
        </div>

        @livewire('nft-grid')

        <div class="flex flex-col md:flex-row md:justify-between md:gap-6 mt-10">
            <!-- Image Card -->
            <div class="w-full md:w-[30%] text-center border bg-white p-4 sm:p-6 mb-6 md:mb-0">
                <img
                    src="{{ asset('images/project-nft.png') }}"
                    class="mx-auto w-full"
                    alt="Project NFT"
                />
            </div>

            <!-- Table Card -->
            <div class="w-full md:w-[70%] text-center border bg-white p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left border">
                        <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 min-w-[200px]">Metric</th>
                            <th class="px-4 py-2 min-w-[150px]">Value</th>
                            <th class="px-4 py-2 min-w-[300px]">Description</th>
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
                                New Minted Supply<br />
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
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-6 p-0 mt-4 mb-8">

        {{-- Left Card --}}
        <div
            class="bg-white border rounded-lg p-6 shadow w-full md:w-[35%] min-w-[250px]"
        >
            <h2 class="text-xl font-bold mb-4">The Dev Logs</h2>
            <p>
                Soon.
            </p>
        </div>

        {{-- Right Card --}}
        <div
            class="bg-white border rounded-lg p-6 shadow space-y-8 w-full md:w-[65%]"
        >

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

    </div>


    <div class="max-w-3xl mx-auto bg-white border border-gray-300 rounded-xl shadow-lg p-8 my-8">
        <h2 class="text-3xl font-extrabold text-primary mb-6 text-center">RipplePunks: The Ultimate XRPL NFT Revolution</h2>
        <p class="text-gray-700 text-lg mb-4 leading-relaxed">
            Welcome to <strong>RipplePunks</strong> — a groundbreaking collection of <strong>20,000 unique 1/1 Punks</strong> living on the XRP Ledger (XRPL), blending iconic Ethereum punk vibes with next-gen blockchain innovation. Starting with an exclusive original batch of <strong>10,000 legendary OG Punks</strong>, RipplePunks are redefining scarcity, community, and digital ownership in the crypto art space.
        </p>
        <p class="text-gray-700 text-lg mb-4 leading-relaxed">
            But the journey doesn’t stop there. We’re minting an additional <strong>10,000 brand-new Punks</strong> — each boasting fresh, unique traits — extending the legacy while preserving the original spirit. Watch as the collection evolves dynamically, with a portion of the initial supply locked away and a new batch minting steadily, reflecting the pulse of this vibrant ecosystem.
        </p>
        <p class="text-gray-700 text-lg mb-6 leading-relaxed">
            Whether you’re a seasoned collector or a newcomer, RipplePunks offers a chance to own a piece of this thrilling narrative. With transparent supply tracking, exclusive holders’ benefits, and a rapidly growing community, now’s the time to jump in.
        </p>
        <p class="text-center text-xl font-bold text-primary">
            Join the revolution. Own your punk. Shape the future on XRPL.
        </p>
    </div>

@endsection
