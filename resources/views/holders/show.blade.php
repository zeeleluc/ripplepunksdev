@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Holder</h1>

        @if ($holder)
            @livewire('holder', ['holder' => $holder])
        @else
            <div class="text-center text-gray-600 text-lg mt-10">
                Wallet {{ $wallet }} is not a holder yet.
            </div>
        @endif
    </div>
@endsection
