@extends('layouts.app')

@section('content')
    @php
        $bar1Percent = ($bar1Count / $totalItems) * 100;
        $bar2Percent = ($bar2Count / $totalItems) * 100;
    @endphp

    <div class="max-w-4xl mx-auto space-y-8 p-6 mb-4">

        {{-- Bar 1 --}}
        <div>
            <div class="mb-1 font-semibold text-lg">The Original Punks (#0 - #9999)</div>
            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden">
                <div
                    class="h-full"
                    style="width: {{ $bar1Percent }}%; background: linear-gradient(90deg, {{ $colors['bar1'][0] }}, {{ $colors['bar1'][1] }});"
                ></div>
            </div>
        </div>

        {{-- Bar 2 --}}
        <div>
            <div class="mb-1 font-semibold text-lg">The Other Punks (#10000 - #19999)</div>
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
