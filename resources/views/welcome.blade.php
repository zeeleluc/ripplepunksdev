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
    <div class="container mx-auto px-0 sm:px-6 py-2">


        <div class="px-0 sm:px-6 pb-3 pt-0">
            <div class="flex flex-col sm:flex-row sm:justify-center sm:items-center gap-3 sm:gap-4 mb-3">
                <a
                    target="_blank"
                    href="https://xrp.cafe/usercollection/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/604"
                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    The Other Punks: 2 XRP
                </a>
                <a
                    href="/rewards"
                    class="w-full sm:w-auto bg-primary-900 hover:bg-primary-800 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    Rewards
                </a>
                <a
                    href="/shoutboard"
                    class="w-full sm:w-auto bg-primary-800 hover:bg-primary-700 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    Shoutboard
                </a>
                <a
                    href="/badges"
                    class="w-full sm:w-auto bg-primary-700 hover:bg-primary-600 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    Badges
                </a>
                <a
                    href="/holders"
                    class="w-full sm:w-auto bg-primary-600 hover:bg-primary-500 text-white text-base font-semibold py-3 px-6 rounded-lg shadow text-center"
                >
                    Holders
                </a>
            </div>
        </div>

        <div class="max-w-9xl mx-auto bg-white border border-gray-300 rounded shadow p-4 mb-8 mt-0">

            <div class="mb-0 font-semibold text-lg pb-2">Punks Minting Progress</div>

            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden flex">
                {{-- Bar 1 (left) --}}
                <div
                    class="h-full bg-primary-300"
                    style="width: {{ $bar1Percent }}%;"
                ></div>

                {{-- Bar 2 (right) --}}
                <div
                    class="h-full bg-primary-500"
                    style="width: {{ $bar2Percent }}%;"
                ></div>

                {{-- Bar 3 (right) --}}
                <div
                    class="h-full bg-gray-100"
                    style="width: {{ $bar3Percent }}%;"
                ></div>
            </div>

            <div class="flex justify-between mt-2 text-sm font-semibold">
                <span>The Original Punks</span>
                <span>The Other Punks</span>
            </div>

        </div>

        @livewire('nft-grid')

        <div class="flex flex-col md:flex-row md:justify-between md:gap-6 mt-10">
            <!-- Image Card -->
            <div class="w-full md:w-[30%] text-center border bg-white p-4 sm:p-6 mb-6 md:mb-0 shadow">
                <img
                    src="{{ asset('images/project-nft.png') }}"
                    class="mx-auto w-full"
                    alt="Project NFT"
                />
            </div>

            <!-- Table Card -->
            <div class="w-full md:w-[70%] text-center border bg-white p-4 sm:p-6 shadow">
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

    <div class="max-w-5xl mx-auto bg-white border rounded shadow p-8 mb-8 mt-6">
        <h2 class="text-xl font-bold mb-4">The Dev 🤘🏼</h2>
        @foreach ($logEntries as $logEntry)
            <div class="my-0 pt-2 pb-4 border-t">
                <p class="p-0 m-0">
                    @if ($logEntry->link)
                        <a target="_blank" href="{{ $logEntry->link }}">
                            🔗
                        </a>
                    @endif
                    {{ $logEntry->text }}
                </p>
                <small class="text-xs">
                    {{ $logEntry->created_at->diffForHumans() }}
                </small>
            </div>
        @endforeach
        <a class=" sm:w-auto bg-primary-500 hover:bg-primary-600 text-white text-base font-semibold mt-4 py-2 px-4 rounded-md shadow text-center" href="{{ route('logs') }}">
            Read More
        </a>
    </div>

    <div class="max-w-3xl mx-auto bg-white border rounded shadow p-8 my-8">
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
