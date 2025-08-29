@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Holder</h1>

        @if ($holder)
            @livewire('holder', ['holder' => $holder])
        @else
            <div class="bg-white border rounded p-5 text-center">
                Wallet {{ $wallet }} is not a holder yet.

                <div class="flex flex-col items-center gap-2 sm:gap-3 text-center w-full mt-4">
                    <livewire:buy-button />
                    <p class="text-gray-700 text-xs sm:text-base leading-snug sm:leading-normal px-4 sm:px-0">
                        Buy RipplePunks and become a holder
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection
