@extends('layouts.app')

@section('content')
    @php
        $bar1Percent = ($bar1Count / $totalItems) * 100;
        $bar2Percent = ($bar2Count / $totalItems) * 100;
    @endphp

    <div class="max-w-6xl mx-auto p-6 mb-4 my-4 text-center">
        <img
            src="{{ asset('images/project-nft.png') }}"
            class="mx-auto w-full max-w-md sm:max-w-xs"
            alt="Project NFT"
        />

        <h1 class="font-bold mt-4 text-2xl">
            RipplePunks: 20k Punks on the XRPL
        </h1>

    </div>

    <div class="max-w-6xl mx-auto space-y-8 p-6 mb-4 border bg-white mt-4 mb-8">

        {{-- Bar 1 --}}
        <div>
            <div class="mb-0 font-semibold text-lg">The Original Punks (#0 - #9999)</div>
            <p class="mb-2">
                These are the original 1/1 Punks, perfectly matching the iconic Ethereum Punks collection.
            </p>
            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden">
                <div
                    class="h-full"
                    style="width: {{ $bar1Percent }}%; background: linear-gradient(90deg, {{ $colors['bar1'][0] }}, {{ $colors['bar1'][1] }});"
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
            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden">
                <div
                    class="h-full"
                    style="width: {{ $bar2Percent }}%; background: linear-gradient(90deg, {{ $colors['bar2'][0] }}, {{ $colors['bar2'][1] }});"
                ></div>
            </div>
        </div>

    </div>

    @livewire('giveaway-wrapper')

    <div class="flex justify-center mt-8">
        <a href="{{ url('/about-cto') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
            About the CTO
        </a>
    </div>

@endsection
