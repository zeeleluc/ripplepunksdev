@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Profile</h1>
        @livewire('profile', ['user' => $user])
    </div>
@endsection
