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

    <div class="container mx-auto px-2 sm:px-6">

        {{-- CTA Section --}}
        <div class="bg-white border rounded shadow pb-3 pt-5 sm:pb-4 sm:pt-7">
            <div class="flex flex-col items-center gap-2 sm:gap-3 text-center w-full">
                <a
                    target="_blank"
                    href="https://xrp.cafe/usercollection/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/604"
                    class="w-full max-w-xs sm:w-auto mx-auto bg-yellow-500 hover:bg-yellow-600 text-white text-sm sm:text-lg font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg shadow"
                >
                    Buy The Other Punks
                </a>
                <p class="text-gray-700 text-xs sm:text-base leading-snug sm:leading-normal px-4 sm:px-0">
                    Newly minted <em>"The Other Punks"</em> — offers welcome starting at <strong>2 XRP</strong>.
                </p>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="px-0 sm:px-6 pb-2 sm:pb-3 pt-3 sm:pt-0 mt-4 sm:mt-5">
            <div class="flex flex-col sm:flex-row sm:justify-center sm:items-center gap-2 sm:gap-4">
                <a href="/rewards"
                   class="w-full sm:w-auto bg-primary-900 hover:bg-primary-800 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg shadow text-center">
                    Rewards
                </a>
                <a href="/shoutboard"
                   class="w-full sm:w-auto bg-primary-800 hover:bg-primary-700 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg shadow text-center">
                    Shoutboard
                </a>
                <a href="/badges"
                   class="w-full sm:w-auto bg-primary-700 hover:bg-primary-600 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg shadow text-center">
                    Badges
                </a>
                <a href="/holders"
                   class="w-full sm:w-auto bg-primary-600 hover:bg-primary-500 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg shadow text-center">
                    Holders
                </a>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="max-w-9xl mx-auto bg-white border rounded shadow p-3 sm:p-4 mb-6 sm:mb-8 mt-3 sm:mt-0">
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

        {{-- NFT Grid --}}
        @livewire('nft-grid')

        {{-- Supply Breakdown --}}
        <div class="flex flex-col md:flex-row md:justify-between md:gap-6 mt-6 sm:mt-10">
            {{-- NFT Preview --}}
            <div class="w-full md:w-[30%] border bg-white p-3 sm:p-6 mb-4 md:mb-0 shadow text-center">
                <img
                    src="{{ asset('images/project-nft.png') }}"
                    class="mx-auto w-full"
                    alt="Project NFT"
                />
            </div>

            {{-- Breakdown Table --}}
            <div class="w-full md:w-[70%] border bg-white p-3 sm:p-6 shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left border text-xs sm:text-sm">
                        <thead>
                        <tr class="bg-gray-200">
                            <th class="px-2 sm:px-4 py-1 sm:py-2 min-w-[160px] sm:min-w-[200px]">Metric</th>
                            <th class="px-2 sm:px-4 py-1 sm:py-2 min-w-[120px] sm:min-w-[150px]">Value</th>
                            <th class="px-2 sm:px-4 py-1 sm:py-2 min-w-[220px] sm:min-w-[300px]">Description</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <tr class="bg-gray-50">
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">
                                Initial Supply<br><small>The Original Punks</small>
                            </td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">{{ number_format($initialSupply) }}</td>
                            <td class="px-2 sm:px-4 py-1 sm:py-2">First minted batch of NFTs</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">
                                New Minted Supply<br><small>The Other Punks</small>
                            </td>
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
                            <td class="px-2 sm:px-4 py-1 sm:py-2 font-medium">
                                Out of Circulation
                                <a
                                    target="_blank"
                                    href="https://bithomp.com/en/nft-explorer?includeWithoutMediaData=true&owner=VaultForRP&issuer=r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS&order=rating&taxon=604&includeBurned=true"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                                    </svg>
                                </a>
                            </td>
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

                {{-- Interaction Buttons, floated left --}}
                <div class="mt-2 sm:mt-4 flex justify-start">
                    <livewire:interaction-buttons identifier="the-supply-breakdown" />
                </div>
            </div>
        </div>

    </div>

    {{-- Dev Log --}}
    <div class="max-w-5xl mx-auto bg-white border rounded shadow p-4 sm:p-8 mb-6 sm:mb-8 mt-4 sm:mt-6">
        <h2 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">The Dev 🤘🏼</h2>
        @foreach ($logEntries as $logEntry)
            <div class="my-0 pt-2 pb-3 sm:pb-4 border-t">
                <p class="m-0 text-sm sm:text-base">
                    @if ($logEntry->link)
                        <a target="_blank" href="{{ $logEntry->link }}">🔗</a>
                    @endif
                    {{ $logEntry->text }}
                </p>
                <div>
                    <livewire:interaction-buttons :identifier="'log-' . $logEntry->id" class="my-1 sm:my-2" />
                </div>
                <small class="text-xs">{{ $logEntry->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
        <a class="bg-primary-500 hover:bg-primary-600 text-white text-sm sm:text-base font-semibold mt-3 sm:mt-4 py-2 px-3 sm:px-4 rounded-md shadow text-center inline-block" href="{{ route('logs') }}">
            Read More
        </a>
    </div>

    {{-- Project Description --}}
    <div class="max-w-3xl mx-auto bg-white border rounded shadow p-4 sm:p-8 my-6 sm:my-8">
        <h2 class="text-lg sm:text-3xl font-extrabold text-primary mb-4 sm:mb-6 text-center">
            RipplePunks: The Ultimate XRPL NFT Revolution
        </h2>
        <p class="text-gray-700 text-sm sm:text-lg mb-3 sm:mb-4 leading-snug sm:leading-relaxed">
            Welcome to <strong>RipplePunks</strong> — a groundbreaking collection of <strong>20,000 unique 1/1 Punks</strong> living on the XRP Ledger (XRPL), blending iconic Ethereum punk vibes with next-gen blockchain innovation...
        </p>
        <p class="text-gray-700 text-sm sm:text-lg mb-3 sm:mb-4 leading-snug sm:leading-relaxed">
            But the journey doesn’t stop there. We’re minting an additional <strong>10,000 brand-new Punks</strong> — each boasting fresh, unique traits — extending the legacy while preserving the original spirit...
        </p>
        <p class="text-gray-700 text-sm sm:text-lg mb-4 sm:mb-6 leading-snug sm:leading-relaxed">
            Whether you’re a seasoned collector or a newcomer, RipplePunks offers a chance to own a piece of this thrilling narrative...
        </p>
        <p class="text-center text-sm sm:text-xl font-bold text-primary">
            Join the revolution. Own your punk. Shape the future on XRPL.
        </p>
    </div>
@endsection
