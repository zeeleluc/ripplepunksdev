@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold text-center">RipplePunk Badges</h1>
        <h2 class="text-1xl font-bold text-center">
            @if ($holder)
                <a class="text-primary-600 hover:text-primary-800" href="{{ route('holder', ['wallet' => $holder->wallet]) }}">
                    {{ $holder->wallet }}
                </a>
            @endif
        </h2>

        <p class="text-center my-3">
            Badges unlock access to special claims such as fixed royalty shares in XRP, RipplePunk NFTs, and other exclusive digital rewards. The higher your badge level, the better the prizes you can claim — with top-tier badges unlocking the most valuable and rare items. In addition, badges grant access to more features on the website, including the ability to reply, comment, like, share, and more.
        </p>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border rounded shadow divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-lg font-semibold text-gray-700">
                <tr>
                    <th class="px-4 py-2 w-[135px] min-w-[135px] max-w-[135px]">Min. Punks</th>
                    <th class="px-4 py-2">RipplePunks</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                @foreach ($tiers as $count => $badge)
                    <tr>
                        <td class="px-4 py-3 text-gray-500 text-base w-[135px] min-w-[135px] max-w-[135px]">
                            {{ $count }}
                        </td>
                        <td class="px-4 py-3">
                <span class="@if(in_array($badge, $userBadges)) bg-primary-200 text-primary-900 @else bg-gray-100 text-gray-300 @endif text-base font-medium px-2.5 py-1 rounded-full">
                    {{ $badge }}
                </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
