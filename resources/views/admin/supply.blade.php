@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Supply Management</h1>

        <div class="flex flex-wrap gap-6">
            <!-- Livewire Form Card -->
            <div class="w-full md:w-[48%] bg-white border rounded-xl p-6">
                @livewire('supply-form')
            </div>

            <!-- Latest Record Card -->
            <div class="w-full md:w-[48%] bg-white border rounded-xl p-6">
                @livewire('supply-latest')
            </div>
        </div>
    </div>
@endsection
