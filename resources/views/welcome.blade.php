@extends('layouts.app')

@section('content')
    @php
        $bar1Percent = ($bar1Count / $totalItems) * 100;
        $bar2Percent = ($bar2Count / $totalItems) * 100;
    @endphp

    <div class="max-w-4xl mx-auto space-y-8 p-6 mb-4">

        {{-- Bar 1 --}}
        <div>
            <div class="mb-1 font-semibold text-lg">The Original Punks (#0 - #9999)</div>
            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden">
                <div
                    class="h-full"
                    style="width: {{ $bar1Percent }}%; background: linear-gradient(90deg, {{ $colors['bar1'][0] }}, {{ $colors['bar1'][1] }});"
                ></div>
            </div>
        </div>

        {{-- Bar 2 --}}
        <div>
            <div class="mb-1 font-semibold text-lg">The Other Punks (#10000 - #19999)</div>
            <div class="w-full bg-gray-300 rounded h-6 overflow-hidden">
                <div
                    class="h-full"
                    style="width: {{ $bar2Percent }}%; background: linear-gradient(90deg, {{ $colors['bar2'][0] }}, {{ $colors['bar2'][1] }});"
                ></div>
            </div>
        </div>

    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="bg-white text-gray-600 shadow-lg rounded-lg p-6 mb-8 text-xl">
            <p class="mb-4 leading-relaxed">
                A different total may be noticed on Café—that’s because <strong class="text-primary">The Dev</strong> is currently minting 10,000 additional RipplePunks. The CTO community is planning to stall their RipplePunks in a wallet they cannot access or control. This is happening because <strong class="text-primary">The Dev</strong> is not handing over the royalties wallet, which has been built and maintained through personal effort. Rather than abandoning the original, <strong class="text-primary">The Dev</strong> is preserving it—and pushing it forward—with The Other RipplePunks: a new wave of 10,000 unique combinations, listed at just 2 XRP each, regardless of type or rarity. Fair and open to all.
            </p>

            <p class="leading-relaxed">
                There are no promises, no points systems, and no complex utilities or royalty shares—at least for now. This is about preserving history and making new history on XRPL. The blockchain and volume speak for themselves. <strong class="text-primary">The Dev</strong> is committed for the long game—minting may take months.
            </p>
        </div>

        <div class="bg-white text-gray-500 shadow-lg rounded-lg p-6 mb-8 text-lg">
            <p class="mb-4 leading-relaxed">
                Accusations are easy to throw, calling <strong class="text-primary">The Dev</strong> a scammer—but sticking to the facts is important. <strong class="text-primary">The Dev</strong> was the only one consistently building on top of the project, shipping features that the broader community often ignored. While many focused solely on accessing a portion of the royalties, <strong class="text-primary">The Dev</strong> kept building.
            </p>

            <p class="mb-4 leading-relaxed">
                Fifty percent of royalties were voluntarily shared with the community until it became legally impossible to continue—coinciding with a period when sales had already slowed to nearly zero. Even after that, <strong class="text-primary">The Dev</strong> continued to support the project by sweeping the floor using what little royalties were received. These RipplePunks were handed to a CTO member with the promise they would be used in giveaways—a promise that was never fulfilled, though that detail is often left out.
            </p>

            <p class="mb-4 leading-relaxed">
                Yes, <strong class="text-primary">The Dev</strong> once told a community member to “fuck off”—a mistake that was quickly followed by an apology and a brief explanation of a personal struggle at the time, though that is rarely mentioned.
            </p>

            <p class="mb-4 leading-relaxed">
                Also forgotten: <strong class="text-primary">The Dev</strong> swept floors of many other XRPL projects and gave away those NFTs as well. Over 75% of minting profits were redistributed in giveaways—literally putting money back into the community’s hands.
            </p>

            <p class="mb-4 leading-relaxed">
                Despite this, it was not enough. The message was clear: “Hand over the royalties or get lost.”
            </p>

            <p class="mb-4 leading-relaxed">
                So, <strong class="text-primary">The Dev</strong> left.
            </p>

            <p class="mb-4 leading-relaxed">
                But upon seeing plans to burn or lock up the original RipplePunks in an abandoned wallet, <strong class="text-primary">The Dev</strong> stepped back in—refusing to let hard work be erased by freeloaders acting like divas, only showing up when it’s time to collect.
            </p>

            <p class="leading-relaxed">
                That is why <strong class="text-primary">The Dev</strong> introduced another 10,000 batch called "The Other Punks." Even if The Original Punks are eventually set aside, The Other Punks will carry the legacy forward and keep the spirit alive.
            </p>
        </div>
    </div>
@endsection
