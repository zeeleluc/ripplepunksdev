@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-0 py-6">
        <h1 class="text-2xl font-bold text-center">RipplePunk Badges</h1>
        @if (!\Illuminate\Support\Facades\Auth::check())
            <h2 class="text-1xl mb-3 text-center">Login with your Xaman wallet to see your badges</h2>
        @endif

        <p class="text-center my-3">
            Badges unlock access to special claims such as fixed royalty shares in XRP, RipplePunk NFTs, and other exclusive digital rewards. The higher your badge level, the better the prizes you can claim — with top-tier badges unlocking the most valuable and rare items. In addition, badges grant access to more features on the website, including the ability to reply, comment, like, share, and more.
        </p>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full border rounded shadow divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-lg font-semibold text-gray-700">
                <tr>
                    <th class="px-4 py-2 min-w-[135px]">Min. Punks</th>
                    <th class="px-4 py-2 min-w-[200px]">
                        Any RipplePunks<br />
                        <small>
                            #0 - #19999
                        </small>
                    </th>
                    <th class="px-4 py-2 min-w-[200px]">
                        The Original Punks<br />
                        <small>
                            #0 - #9999
                        </small>
                    </th>
                    <th class="px-4 py-2 min-w-[200px]">
                        The Other Punks<br />
                        <small>
                            #10000 - #19999
                        </small>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                @foreach ($tiers as $count => $badges)
                    <tr>
                        <td class="px-4 py-3 text-gray-500 text-base">{{ $count }}</td>

                        @foreach ($badges as $badge)
                            <td class="px-4 py-3">
                                <span class="@if(in_array($badge, $userBadges)) bg-primary-200 text-primary-900 @else bg-gray-100 text-gray-300 @endif text-base font-medium px-2.5 py-1 rounded-full">
                                    {{ $badge }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
