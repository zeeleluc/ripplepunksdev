<div>
    <!-- Pay button -->
    <button wire:click="toggleModal"
            class="w-full bg-gray-900 hover:bg-gray-800 text-white text-lg font-semibold py-2 sm:py-3 px-4 sm:px-6 mb-2 rounded shadow text-center block">
        Pay {{ $amount }} XRP
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white px-4 py-3 rounded shadow-lg max-w-lg w-full relative text-center">
                <!-- Close X button -->
                <button wire:click="toggleModal"
                        class="absolute top-3 right-5 text-gray-500 hover:text-gray-800 text-xl font-bold">
                    &times;
                </button>

                @if ($paymentReceived)
                    <h3 class="font-semibold mb-4 text-2xl">Payment Received!</h3>
                    <p class="text-gray-800 text-base sm:text-lg leading-relaxed px-4 sm:px-0">
                        Thank you for your payment of {{ $amount }} XRP to {{ $destination }}.
                    </p>
                @else
                    <h3 class="font-semibold mb-4 text-2xl">Make a Payment</h3>
                    <p class="text-gray-800 text-base sm:text-lg leading-relaxed px-4 sm:px-0">
                        Scan the QR code to pay {{ $amount }} XRP to {{ $destination }}.
                    </p>
                    @if ($qr)
                        <img src="{{ $qr }}" alt="Payment QR Code" class="mx-auto my-4">
                        <p>Or open in Xumm:
                            <a href="{{ $url }}" target="_blank" class="text-blue-500 underline hover:text-blue-600">
                                Click here
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 sm:w-4 sm:h-4 inline-block ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14M5 5v14h14v-5" />
                                </svg>
                            </a>
                        </p>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            @if ($showModal && !$paymentReceived)
            let interval = setInterval(() => {
                $wire.checkPaymentStatus();
            }, 3000);
            $wire.on('payment-success', () => {
                clearInterval(interval);
            });
            @endif
        });
    </script>
    @endscript
</div>
