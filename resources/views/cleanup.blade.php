@extends('layouts.app')

@section('content')

    <div class="max-w-6xl mx-auto px-0 py-8">

        <div class="bg-white shadow-lg rounded p-6 text-lg">
            <h2 class="font-bold mb-2 text-2xl">Clean Up</h2>
            <p class="mb-6 leading-relaxed">
                Let's get rid of the doubles and the "Blue Bandana"
            </p>

             🔎 Duplicates
            <div class="mb-8">
                <h3 class="font-semibold text-xl mb-3">Duplicates</h3>

                <livewire:duplicate-nfts />

            </div>

            {{-- 🎯 Blue Bandanas --}}
            <div>
                <h3 class="font-semibold text-xl mb-3">Blue Bandanas</h3>

                @if($blueBandanas->isEmpty())
                    <p class="text-gray-600">No blue bandana NFTs found.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 justify-items-center">
                        @foreach($blueBandanas as $nft)
                            @php
                                $metadata = $nft->metadata ?? [];
                                $imageUrl = $nft->getImageUrl();
                            @endphp
                            <div class="border rounded p-2 bg-white shadow-sm w-full max-w-[150px] flex flex-col items-center text-center">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $metadata['name'] ?? 'NFT Image' }}"
                                     class="w-full rounded mb-2" />
                                <h3 class="font-semibold text-gray-700 text-xs truncate mb-2">
                                    {{ $metadata['name'] ?? 'Unnamed NFT' }}
                                </h3>
                                <ul class="text-[11px] text-gray-600 text-left w-full space-y-0.5">
                                    <li><strong>Type:</strong> {{ $nft->type }}</li>
                                    <li><strong>Color:</strong> {{ $nft->color }}</li>
                                    <li>✓ Blue Bandana</li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

@endsection
