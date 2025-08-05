<div>
    @if ($giveaways->count())
        <div class="overflow-x-auto">
            <table class="table-auto min-w-max w-full">
                <thead>
                <tr>
                    <th></th>
                    <th class="text-start">Wallet</th>
                    <th class="text-start">Claimed At</th>
                    <th class="text-start">Received At</th>
                    <th class="text-start">Comment</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($giveaways as $g)
                    <tr>
                        <td class="text-center py-1">
                            @if (!$g->received_giveaway_at && !$g->declined_at)
                                <span class="animate-pulse text-yellow-500">⏳</span>
                            @elseif ($g->declined_at)
                                <span class="text-red-500">❌</span>
                            @elseif ($g->received_giveaway_at)
                                <span class="text-green-600">✅</span>
                            @endif
                        </td>

                        <td class="text-start pr-3">{{ $g->wallet }}</td>
                        <td class="text-start pr-3">{{ $g->claimed_at ? $g->claimed_at->format('Y-m-d') : '-' }}</td>
                        <td class="text-start pr-3">{{ $g->received_giveaway_at ? $g->received_giveaway_at->format('Y-m-d') : '-' }}</td>
                        <td class="text-start">{{ $g->comments ?: '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
