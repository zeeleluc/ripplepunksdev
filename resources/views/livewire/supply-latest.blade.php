<div>
    <h2 class="text-xl font-semibold mb-4">Latest Record</h2>

    @if ($latest)
        <ul class="space-y-2 text-left">
            <li><strong>Out of Circulation:</strong> {{ number_format($latest->out_of_circulation) }}</li>
            <li><strong>New Mints:</strong> {{ number_format($latest->new_mints) }}</li>
            <li><strong>Updated:</strong> {{ $latest->created_at->format('Y-m-d H:i') }} <sup>UTC</sup></li>
        </ul>
    @else
        <p class="text-gray-500">No records found.</p>
    @endif
</div>
