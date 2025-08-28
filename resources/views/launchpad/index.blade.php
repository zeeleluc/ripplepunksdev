@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold text-center">Punk Launchpad</h1>

        <p class="text-center mt-3">
            A portion of the collected fees will be shared with OG holders through our rewards system.
        </p>

        <div class="max-w-4xl mx-auto px-0 py-8">

            <div class="bg-white shadow-lg rounded p-6 text-lg">
                <p class="mb-6 leading-relaxed">
                    Launch your NFT collection on the XRPL with simple and transparent pricing.
                    Costs depend on whether you hold OGs, Rewinds, or Quartets.
                    See the breakdown below:
                </p>

                <div class="mb-6">
                    <h3 class="font-semibold text-xl mb-2">Pricing</h3>
                    <ul class="list-disc list-inside space-y-2">
                        <li><strong>Standard (no OGs):</strong> 2 XRP setup fee + 0.02 XRP per NFT</li>
                        <li><strong>OG Holder:</strong> 1 XRP setup fee + 0.01 XRP per NFT</li>
                        <li><strong>OG Holder + 10 matching Rewinds or a Complete Quartet:</strong> No setup fee + 0.001 XRP per NFT</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-xl mb-2">Examples (1000 NFTs)</h3>
                    <table class="table-auto border-collapse border border-gray-300 w-full text-left">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">Condition</th>
                            <th class="border border-gray-300 px-4 py-2">Setup Fee</th>
                            <th class="border border-gray-300 px-4 py-2">Per NFT</th>
                            <th class="border border-gray-300 px-4 py-2">Total Cost</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">Standard</td>
                            <td class="border border-gray-300 px-4 py-2">2 XRP</td>
                            <td class="border border-gray-300 px-4 py-2">1000 × 0.02 = 20 XRP</td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">22 XRP</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">OG Holder</td>
                            <td class="border border-gray-300 px-4 py-2">1 XRP</td>
                            <td class="border border-gray-300 px-4 py-2">1000 × 0.01 = 10 XRP</td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">11 XRP</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">OG Holder + Rewinds/Quartet</td>
                            <td class="border border-gray-300 px-4 py-2">0 XRP</td>
                            <td class="border border-gray-300 px-4 py-2">1000 × 0.001 = 1 XRP</td>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">1 XRP</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

@endsection
