@extends('layouts.app')

@section('content')

    <div class="flex">
        <livewire:payment :amount="1" :memo="'Test Payment'" />
    </div>

@endsection
