@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-center mb-6">
            <a class="text-primary-600 hover:text-primary-800" href="{{ route('punks') }}">
                All Punks
            </a>
            /
            RipplePunk #{{ $id }}
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Left Side: Image & Metadata --}}
            <div class="bg-white shadow-lg rounded p-4">
                <img src="{{ $nft->getImageUrl() }}"
                     alt="{{ $nft->name }}"
                     class="w-full rounded mb-4" />

                <h3 class="text-center font-semibold text-gray-700 text-base mb-4">
                    {{ $nft->name }}
                    (<a class="text-primary-600 hover:text-primary-800" href="{{ route('punks', ['type' => $nft->type]) }}">{{ $nft->type }}</a>
                    /
                    <a class="text-primary-600 hover:text-primary-800" href="{{ route('punks', ['type' => $nft->color]) }}">{{ $nft->color }}</a>)
                </h3>

                {{-- Metadata Table --}}
                <table class="w-full text-sm text-left border">
                    <tbody>
                    @if ($nft->total_accessories > 0)
                        @foreach(\App\Models\Nft::getAttributeColumns() as $columnName)
                            @if ($nft->{$columnName})
                                <tr class="border-b">
                                    <th class="px-4 py-2 font-medium text-gray-600">
                                        <a class="text-primary-600 hover:text-primary-800"
                                           href="{{ url('punks') . '?selectedAccessories[0]=' . urlencode($columnName) }}">
                                            {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $columnName)) }}
                                        </a>
                                    </th>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>

            {{-- Right Side: Offers & Selling History --}}
            <div class="bg-white shadow-lg rounded p-4 flex flex-col">
                <h2 class="text-xl font-semibold mb-4">Offers & Selling History</h2>

                {{-- Placeholder for Offers --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-2">Active Offers</h3>
                    <p class="text-gray-500">Soon visible on the website.</p>
                </div>

                {{-- Placeholder for History --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-2">Selling History</h3>
                    <p class="text-gray-500">Soon visible on the website.</p>
                </div>

                {{-- Marketplaces Section --}}
                <div>
                    <h3 class="text-lg font-medium mb-3">Available On</h3>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $links = [
                                'xrp.cafe' => 'https://xrp.cafe/nft/' . $nft->nftoken_id,
                                'bidds.com' => 'https://bidds.com/nft/' . $nft->nftoken_id,
                                'xpmarket.com' => 'https://xpmarket.com/nfts/item/' . $nft->nftoken_id,
                                'opulencex.io' => 'https://nftmarketplace.opulencex.io/nft/' . $nft->nftoken_id,
                            ];
                        @endphp

                        @foreach ($links as $market => $url)
                            <a href="{{ $url }}" target="_blank"
                               class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 shadow transition">
                                {{ $market }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
