@extends('layouts.app')

@section('content')

    <div class="flex">
        <livewire:payment :amount="0.001" :memo="'Test Payment'" />
    </div>

@endsection
