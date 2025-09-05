<div>
    <button wire:click="initiatePayment"
            class="w-full bg-gray-900 hover:bg-gray-800 text-white text-lg font-semibold py-2 sm:py-3 px-4 sm:px-6 mb-2 rounded shadow text-center block">
        Pay {{ $amount }} XRP
    </button>

    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white px-4 py-3 rounded shadow-lg max-w-lg w-full relative text-center">
                <button wire:click="toggleModal"
                        class="absolute top-3 right-5 text-gray-500 hover:text-gray-800 text-xl font-bold">
                    &times;
                </button>

                <h3 class="font-semibold mb-4 text-2xl">Make a Payment</h3>

                @if($isLoading)
                    <div class="flex justify-center items-center mb-4">
                        <svg class="animate-spin h-8 w-8 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 text-sm mt-2">Initiating payment...</p>
                @elseif($paymentReceived)
                    <p class="text-gray-800 text-base sm:text-lg leading-relaxed px-4 sm:px-0">
                        Thank you for your payment of {{ $amount }} XRP to {{ $destination }}.
                    </p>
                @else
                    <p class="text-gray-800 text-base sm:text-lg leading-relaxed px-4 sm:px-0">
                        Waiting for you to approve the payment of {{ $amount }} XRP to {{ $destination }} in your Xaman wallet.
                    </p>
                    <p class="text-gray-600 text-sm mt-2">
                        Open the Xaman app to approve the payment via push notification, or scan the QR code below. If you don’t see the notification, check the Events log in Xaman.
                    </p>
                    @if($qrCodeUrl)
                        <div class="flex justify-center mt-4">
                            <img src="{{ $qrCodeUrl }}" alt="QR Code for Payment" class="w-48 h-48">
                        </div>
                    @else
                        <p class="text-red-600 text-sm mt-2">QR code not available. Please try again or check the Events log in Xaman.</p>
                        <button wire:click="retryPayment"
                                class="mt-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                            Try Again
                        </button>
                    @endif
                @endif

                @error('payment')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
                @error('destination')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endif

    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire initialized, showModal: {{ $showModal ? "true" : "false" }}');

            Livewire.on('modal-opened', () => {
                console.log('Modal opened');
                $wire.processPayment();
            });

            Livewire.on('modal-closed', () => {
                console.log('Modal closed');
            });

            Livewire.on('modal-error', (event) => {
                console.log('Modal error: ' + event.message);
            });

            @if($showModal && !$paymentReceived)
            console.log('Starting polling');
            let interval = setInterval(() => {
                $wire.checkPaymentStatus().then(() => {
                    console.log('Status checked, paymentReceived: ' + $wire.paymentReceived);
                    if ($wire.paymentReceived) {
                        clearInterval(interval);
                        console.log('Payment received, polling stopped');
                    }
                });
            }, 5000);

            Livewire.on('payment-success', () => {
                clearInterval(interval);
                console.log('Payment success, polling stopped');
            });
            @endif
        });
    </script>
    @endscript
</div>
