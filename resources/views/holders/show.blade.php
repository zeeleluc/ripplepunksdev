@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Holder</h1>
        @livewire('holder', ['holder' => $holder])
    </div>
@endsection
