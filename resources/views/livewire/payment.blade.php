<div>
    @if ($status === 'pending')
        @if ($pushed)
            <p>Payment request sent to Xumm app. Waiting for confirmation...</p>
        @else
            <p>Scan the QR code to complete the payment:</p>
            <img src="{{ $qr }}" alt="Xumm QR Code">
            <p><a href="{{ $url }}">Open in Xumm</a></p>
        @endif
    @elseif ($status === 'completed')
        <p>Payment completed successfully!</p>
    @elseif ($status === 'expired')
        <p>Payment request expired. Please try again.</p>
    @elseif ($status === 'timeout')
        <p>Payment request timed out. Please try again.</p>
    @endif

    <script>
        document.addEventListener('livewire:load', function () {
            @if ($status === 'pending')
            setInterval(() => {
            @this.checkPaymentStatus();
            }, 5000); // Poll every 5 seconds
            @endif
        });
    </script>
</div>
