<div>
    <!-- Buy button -->
    <button wire:click="toggleModal"
            class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white text-sm sm:text-base font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded shadow text-center">
        Buy
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow-lg max-w-lg w-full text-center">
                <h3 class="font-semibold mb-4 text-2xl">Buy RipplePunks</h3>

                <p class="text-gray-800 text-base sm:text-lg leading-relaxed sm:leading-relaxed px-4 sm:px-0">
                    Newly minted <em>The Other Punks</em> are RipplePunks #10000 through #19999. Offers are welcome starting at <strong>2 XRP</strong>. Listings are set higher to support a rising floor and benefit holders, but 2 XRP offers are still gladly accepted into the project wallet.
                </p>

                <div class="flex justify-center gap-3 mt-4">
                    <button wire:click="toggleModal"
                            class="px-6 py-4 rounded border border-gray-300 hover:bg-gray-100 text-lg">
                        Cancel
                    </button>

                    <a href="{{ $buyUrl }}" target="_blank"
                       class="px-6 py-4 bg-primary-700 hover:bg-primary-600 text-white rounded border font-semibold text-lg">
                        Buy on Cafe
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
